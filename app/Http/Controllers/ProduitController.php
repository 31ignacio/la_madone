<?php

namespace App\Http\Controllers;

use App\Imports\ProduitsImport;
use App\Models\Categorie;
use App\Models\Fournisseur;
use App\Models\Produit;
use App\Services\ProduitImportService;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Throwable;

class ProduitController extends Controller
{
    public function index(Request $request)
    {
        $query = Produit::with(['categorie', 'fournisseur'])->where('actif', true);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('libelle', 'like', "%{$search}%")
                    ->orWhere('reference', 'like', "%{$search}%")
                    ->orWhere('code_barre', 'like', "%{$search}%");
            });
        }

        if ($request->filled('categorie_id')) {
            $query->where('categorie_id', $request->categorie_id);
        }

        if ($request->filled('statut_stock')) {
            match ($request->statut_stock) {
                'rupture' => $query->where('stock_actuel', '<=', 0),
                'faible' => $query->whereColumn('stock_actuel', '<=', 'stock_minimum')
                    ->where('stock_actuel', '>', 0),
                'normal' => $query->whereColumn('stock_actuel', '>', 'stock_minimum'),
                default => null,
            };
        }

        $produits = $query->orderBy('libelle')->paginate(20)->withQueryString();
        $categories = Categorie::orderBy('nom')->get();
        $fournisseurs = Fournisseur::where('actif', true)->orderBy('nom')->get();

        $stats = [
            'normal' => Produit::whereColumn('stock_actuel', '>', 'stock_minimum')->count(),
            'faible' => Produit::whereColumn('stock_actuel', '<=', 'stock_minimum')->where('stock_actuel', '>', 0)->count(),
            'rupture' => Produit::where('stock_actuel', '<=', 0)->count(),
        ];

        return view('produits.index', compact('produits', 'categories','stats', 'fournisseurs'));
    }

    public function store(Request $request, ProduitImportService $produitImportService)
    {
        $produit = $produitImportService->createFromArray(
            $request->all(),
            auth()->id(),
            $request->file('image')
        );

        return redirect()->route('produits.index')
            ->with('success', "Produit  {$produit->libelle}  crée avec succès.");
    }

   public function import(Request $request, ProduitImportService $produitImportService)
    {
        $request->validate([
            'fichier' => 'required|file|mimes:xlsx,xls,csv,txt|max:10240',
        ], [
            'fichier.required' => 'Veuillez sélectionner un fichier Excel ou CSV.',
            'fichier.mimes'    => 'Le fichier doit être au format xlsx, xls ou csv.',
            'fichier.max'      => 'Le fichier ne doit pas dépasser 10 Mo.',
        ]);

        try {
            $spreadsheet = IOFactory::load($request->file('fichier')->getRealPath());
            $rows        = collect($spreadsheet->getActiveSheet()->toArray(null, true, true, false));
            $spreadsheet->disconnectWorksheets();
            unset($spreadsheet);
        } catch (\Throwable $e) {
            return redirect()
                ->route('produits.index')
                ->withInput(['_form' => 'import'])
                ->with('error', "Impossible de lire le fichier : {$e->getMessage()}");
        }

        $import = new ProduitsImport($produitImportService, auth()->id());

        try {
            $import->collection($rows);
        } catch (\Throwable $e) {
            return redirect()
                ->route('produits.index')
                ->withInput(['_form' => 'import'])
                ->with('error', "Erreur lors de l'import : {$e->getMessage()}");
        }

        $redirect = redirect()
            ->route('produits.index')
            ->withInput(['_form' => 'import']);

        // ── Messages de retour ──
        if ($import->importedCount > 0 && empty($import->errors)) {
            return $redirect->with('success',
                "{$import->importedCount} produit(s) importé(s) avec succès."
            );
        }

        if ($import->importedCount > 0 && !empty($import->errors)) {
            return $redirect
                ->with('success', "{$import->importedCount} produit(s) importé(s).")
                ->with('warning', count($import->errors) . ' ligne(s) ignorée(s).')
                ->with('import_errors', $import->errors);
        }

        if ($import->importedCount === 0 && !empty($import->errors)) {
            return $redirect
                ->with('error', 'Aucun produit importé — vérifiez le format du fichier.')
                ->with('import_errors', $import->errors);
        }

        return $redirect->with('warning', 'Aucun produit trouvé dans le fichier.');
    }

    public function show(Produit $produit)
    {
        $produit->load(['categorie', 'fournisseur']);
        $mouvements = $produit->mouvementsStock()
            ->with('user')
            ->latest()
            ->limit(10)
            ->get();

        return view('produits.show', compact('produit', 'mouvements'));
    }

     public function update(Request $request, Produit $produit)
    {
        $validated = $request->validate([
            'categorie_id' => 'required|exists:categories,id',
            'fournisseur_id' => 'nullable|exists:fournisseurs,id',
            'libelle' => 'required|string|max:255',
            'reference' => 'nullable|string|unique:produits,reference,' . $produit->id,
            'code_barre' => 'nullable|string|unique:produits,code_barre,' . $produit->id,
            'unite' => 'required|string|max:50',
            'prix_achat' => 'required|numeric|min:0',
            'prix_detail' => 'required|numeric|min:0',
            'seuil_detail' => 'nullable|integer|min:1',
            'prix_moyen' => 'nullable|numeric|min:0',
            'seuil_moyen' => 'nullable|integer|min:1|gt:seuil_detail',
            'prix_gros' => 'nullable|numeric|min:0',
            'seuil_gros' => 'nullable|integer|min:1',
            'stock_minimum' => 'required|numeric|min:0',
        ], [
            'seuil_moyen.gt' => 'Le seuil moyen doit être supérieur au seuil détail.',
        ]);

        if (empty($validated['prix_moyen'])) {
            $validated['prix_moyen'] = null;
            $validated['seuil_moyen'] = null;
        }
        if (empty($validated['prix_gros'])) {
            $validated['prix_gros'] = null;
            $validated['seuil_gros'] = null;
        }
        if (empty($validated['seuil_moyen'])) {
            $validated['seuil_gros'] = null;
        }

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('produits', 'public');
        }

        $produit->update($validated);

        return redirect()->route('produits.index')
            ->with('success', "Produit  {$produit->libelle}  mis à jour avec succès.");
    }

    // public function destroy(Produit $produit)
    // {
    //     $produit->update(['actif' => false]);

    //     return redirect()->route('produits.index')
    //         ->with('success', 'Produit désactivé avec succès.');
    // }

    // Suppression DÉFINITIVE d'un seul produit
    public function destroy(Produit $produit)
    {
        $produit->forceDelete(); // Si SoftDeletes activé
        // ou simplement : $produit->delete(); // Si pas de SoftDeletes
        return redirect()->route('produits.index')
            ->with('success', 'Produit supprimé définitivement.');
    }

    // Suppression DÉFINITIVE en masse
    public function destroyBulk(Request $request)
    {
        $ids = $request->input('ids', []);
        if (empty($ids)) {
            return redirect()->route('produits.index')
                ->with('error', 'Aucun produit sélectionné.');
        }

        $count = Produit::whereIn('id', $ids)->count();
        Produit::whereIn('id', $ids)->forceDelete();
        // ou : Produit::whereIn('id', $ids)->delete();

        return redirect()->route('produits.index')
            ->with('success', $count . ' produit(s) supprimé(s) définitivement.');
    }

    public function historique(Produit $produit)
    {
        $totalEntrees = $produit->mouvementsStock()->where('type', 'like', '%entree%')->sum('quantite');
        $totalSorties = $produit->mouvementsStock()->where('type', 'not like', '%entree%')->sum('quantite');
        $totalMouvements = $produit->mouvementsStock()->count();

        $mouvements = $produit->mouvementsStock()
            ->with(['user', 'fournisseur'])
            ->latest()
            ->paginate(20);

        return view('produits.historique', compact(
            'produit',
            'mouvements',
            'totalEntrees',
            'totalSorties',
            'totalMouvements'
        ));
    }
}

