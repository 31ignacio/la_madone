<?php

namespace App\Http\Controllers;

use App\Models\Facture;
use App\Models\Reglement;
use App\Models\MouvementStock;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;

class FactureController extends Controller
{
    public function index()
{
    $user = auth()->user();
    
    $factures = Facture::with('user')
        ->when(!$user->isAdmin() && !$user->isSuperviseur(), function ($query) use ($user) {
            // Caissier et Caissier Haut : voir uniquement leurs propres factures
            return $query->where('user_id', $user->id);
        })
        // Admin et Superviseur : voir toutes les factures (pas de filtre)
        ->when(request('search'), function ($query, $search) {
            return $query->where(function ($q) use ($search) {
                $q->where('numero', 'like', "%{$search}%")
                  ->orWhere('client_nom', 'like', "%{$search}%");
            });
        })
        ->when(request('statut'), function ($query, $statut) {
            return $query->where('statut', $statut);
        })
        ->when(request('date_debut'), function ($query, $date) {
            return $query->whereDate('created_at', '>=', $date);
        })
        ->when(request('date_fin'), function ($query, $date) {
            return $query->whereDate('created_at', '<=', $date);
        })
        ->latest()
        ->paginate(20);

    return view('factures.index', compact('factures'));
}

    public function show(Facture $facture)
    {
        // dd($facture);
        $facture->load(['user', 'lignes.produit', 'reglements.user']);
        
        

        return view('factures.show', compact('facture'));
    }

    public function credits()
    {
        $factures = Facture::with(['user', 'lignes', 'reglements.user'])
            ->where('mode_paiement', 'credit')
            ->where('statut', 'en_cours')
            ->latest()
            ->paginate(20);

        // ✅ Calcul total reste depuis BDD
        $totalCredit = Facture::with('reglements')
            ->where('mode_paiement', 'credit')
            ->where('statut', 'en_cours')
            ->get()
            ->sum(fn($f) => $f->reste_a_payer);

        return view('factures.credits', compact('factures', 'totalCredit'));
    }

    public function regler(Request $request, Facture $facture)
    {
       
        $resteAPayer = $facture->reste_a_payer;
      
        $request->validate([
            'montant' => [
                'required',
                'numeric',
                'min:1',
                function ($attr, $value, $fail) use ($resteAPayer) {
                    if (floatval($value) > $resteAPayer) {
                        $fail('Le montant ne peut pas dépasser le reste dû : ' .
                              number_format($resteAPayer, 0, ',', ' ') . ' FCFA');
                    }
                }
            ],
            'mode_paiement' => 'required|in:espece,mobile_money,carte',
            'notes'         => 'nullable|string|max:255',
        ]);

        if ($facture->statut !== 'en_cours') {
            return back()->with('error', 'Cette facture est déjà entièrement réglée.');
        }

        $montant = floatval($request->montant);

        // ✅ Enregistrer le versement
        Reglement::create([
            'facture_id'    => $facture->id,
            'user_id'       => auth()->id(),
            'montant'       => $montant,
            'mode_paiement' => $request->mode_paiement,
            'notes'         => $request->notes,
        ]);

        // ✅ Recalculer depuis BDD après insertion
        $nouveauReste = $resteAPayer - $request->montant;
        if ($nouveauReste <= 0) {
            $facture->update([
                'statut'       => 'payee',
                'montant_recu' => $facture->montant_paye,
                'monnaie'      => 0,
            ]);

            return back()->with('success',
                '🎉 Facture ' . $facture->numero . ' entièrement réglée !'
            );
        }

        return back()->with('success',
            '✅ Versement de ' . number_format($montant, 0, ',', ' ') .
            ' FCFA enregistré. Reste : ' .
            number_format($nouveauReste, 0, ',', ' ') . ' FCFA'
        );
    }

    public function pdf(Facture $facture)
    {
        $facture->load(['user', 'lignes.produit', 'reglements.user']);
        $pdf = PDF::loadView('factures.pdf', compact('facture'));
        $pdf->setPaper('A4', 'portrait');
        return $pdf->stream('facture-' . $facture->numero . '.pdf');
    }

    public function annuler(Facture $facture)
    {
        if ($facture->statut === 'annulee') {
            return back()->with('error', 'Facture déjà annulée.');
        }

        // Remettre les stocks
        foreach ($facture->lignes as $ligne) {
            if ($ligne->produit) {
                $ligne->produit->entreeStock(
                    $ligne->quantite,
                    $ligne->prix_unitaire,
                    auth()->id(),
                    null,
                    'Annulation facture ' . $facture->numero,
                    $facture->numero
                );
            }
        }

        $facture->update(['statut' => 'annulee']);

        return back()->with('success', 'Facture annulée et stocks remis à jour.');
    }

    
    public function destroy(Facture $facture)
    {
        DB::beginTransaction();

        try {
            // ❌ NE PAS TOUCHER AU STOCK ICI

            // Supprimer mouvements liés (temporaire avec motif)
            MouvementStock::where('motif', 'like', '%' . $facture->numero . '%')->delete();

            // Supprimer relations
            $facture->lignes()->delete();
            $facture->reglements()->delete();

            // Supprimer facture
            $facture->delete();

            DB::commit();

            return redirect()->route('factures.index')
                ->with('success', 'Facture supprimée.');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()->with('error', 'Erreur : ' . $e->getMessage());
        }
    }

}