<?php

namespace App\Http\Controllers;

use App\Models\Produit;
use App\Models\Facture;
use App\Models\MouvementStock;
use App\Models\Alerte;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $today     = Carbon::today();
        $thisMonth = Carbon::now()->startOfMonth();

        // Chiffre d'affaires
        $caJour  = Facture::where('statut', 'payee')
            ->whereDate('created_at', $today)
            ->sum('total');

        $caMois  = Facture::where('statut', 'payee')
            ->where('created_at', '>=', $thisMonth)
            ->sum('total');

        // Ventes
        $ventesJour = Facture::where('statut', 'payee')
            ->whereDate('created_at', $today)
            ->count();

        $ventesMois = Facture::where('statut', 'payee')
            ->where('created_at', '>=', $thisMonth)
            ->count();

        // Produits
        $totalProduits   = Produit::where('actif', true)->count();
        $produitsRupture = Produit::where('stock_actuel', '<=', 0)->count();
        $produitsFaibles = Produit::whereColumn('stock_actuel', '<=', 'stock_minimum')
            ->where('stock_actuel', '>', 0)
            ->count();

        // Alertes non traitées
        $alertesNonTraitees = Alerte::where('traitee', false)->count();

        // Top 5 produits les plus vendus ce mois
        $topProduits = DB::table('facture_lignes')
            ->join('factures', 'facture_lignes.facture_id', '=', 'factures.id')
            ->join('produits', 'facture_lignes.produit_id', '=', 'produits.id')
            ->where('factures.statut', 'payee')
            ->where('factures.created_at', '>=', $thisMonth)
            ->select(
                'produits.libelle',
                DB::raw('SUM(facture_lignes.quantite) as total_vendu'),
                DB::raw('SUM(facture_lignes.sous_total) as total_ca')
            )
            ->groupBy('produits.id', 'produits.libelle')
            ->orderByDesc('total_vendu')
            ->limit(5)
            ->get();

        // Graphique CA 7 derniers jours
        $caParJour = Facture::where('statut', 'payee')
            ->where('created_at', '>=', Carbon::now()->subDays(7))
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(total) as total')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Dernières factures
        $dernieresFactures = Facture::with('user')
            ->latest()
            ->limit(5)
            ->get();

        // Produits en alerte
        $produitsEnAlerte = Produit::where('actif', true)
            ->whereColumn('stock_actuel', '<=', 'stock_minimum')
            ->with('categorie')
            ->limit(5)
            ->get();

        return view('dashboard.index', compact(
            'caJour', 'caMois',
            'ventesJour', 'ventesMois',
            'totalProduits', 'produitsRupture', 'produitsFaibles',
            'alertesNonTraitees',
            'topProduits',
            'caParJour',
            'dernieresFactures',
            'produitsEnAlerte'
        ));
    }
}