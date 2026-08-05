<?php

namespace App\Http\Controllers;


use App\Models\Facture;
use App\Models\Produit;
use App\Models\MouvementStock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class RapportController extends Controller
{
    public function index()
    {
        return view('rapports.index');
    }

    public function ventes(Request $request)
    {
        $dateDebut = $request->get('date_debut', now()->startOfMonth()->format('Y-m-d'));
        $dateFin   = $request->get('date_fin', now()->format('Y-m-d'));

        $factures = Facture::with(['lignes', 'user'])
            ->where('statut', 'payee')
            ->whereDate('created_at', '>=', $dateDebut)
            ->whereDate('created_at', '<=', $dateFin)
            ->get();

        $totalCA      = $factures->sum('total');
        $totalRemises = $factures->sum('remise');
        $nbVentes     = $factures->count();

        $ventesParJour = $factures->groupBy(fn($f) => $f->created_at->format('d/m/Y'))
            ->map(fn($group) => $group->sum('total'));

        return view('rapports.ventes', compact(
            'factures', 'totalCA', 'totalRemises',
            'nbVentes', 'ventesParJour', 'dateDebut', 'dateFin'
        ));
    }
    

    public function stock(Request $request)
    {
        $produits = Produit::with(['categorie', 'fournisseur'])
            ->where('actif', true)
            ->orderBy('libelle')
            ->get();

        $valeurTotaleStock = $produits->sum(fn($p) => $p->stock_actuel * $p->prix_achat);
        $produitsRupture   = $produits->where('stock_actuel', '<=', 0)->count();
        $produitsFaibles   = $produits->filter(fn($p) => $p->isStockFaible())->count();

        return view('rapports.stock', compact(
            'produits', 'valeurTotaleStock',
            'produitsRupture', 'produitsFaibles'
        ));
    }

    public function exportPdf(Request $request)
    {
        $type = $request->get('type', 'stock');

        if ($type === 'stock') {
            $produits = Produit::with('categorie')->where('actif', true)->orderBy('libelle')->get();
            $pdf = Pdf::loadView('rapports.pdf-stock', compact('produits'))
                ->setPaper('a4', 'landscape');
            return $pdf->download('rapport-stock-' . now()->format('Y-m-d') . '.pdf');
        }

        $dateDebut = $request->get('date_debut', now()->startOfMonth()->format('Y-m-d'));
        $dateFin   = $request->get('date_fin', now()->format('Y-m-d'));
        $factures  = Facture::with('lignes')
            ->where('statut', 'payee')
            ->whereDate('created_at', '>=', $dateDebut)
            ->whereDate('created_at', '<=', $dateFin)
            ->get();

        $pdf = Pdf::loadView('rapports.pdf-ventes', compact('factures', 'dateDebut', 'dateFin'))
            ->setPaper('a4', 'landscape');

        return $pdf->download('rapport-ventes-' . now()->format('Y-m-d') . '.pdf');
    }
}