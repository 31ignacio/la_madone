<?php
// app/Http/Controllers/CaisseController.php

namespace App\Http\Controllers;

use App\Models\Produit;
use App\Models\Facture;
use App\Models\FactureLigne;
use App\Models\Reglement;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\EmecefService;

class CaisseController extends Controller
{
    public function index()
    {
        $clients = Client::orderBy('nom')->get(['id', 'nom','prenom', 'telephone']);

        return view('caisse.index',compact('clients'));
    }

    /**
     * Recherche de produits pour la caisse (AJAX).
     * Retourne aussi les paliers de prix pour affichage JS.
     */
    public function recherche(Request $request)
    {
        $search = trim((string) $request->get('q', ''));

        $produits = Produit::where('actif', true)
            ->where('stock_actuel', '>', 0)
            ->where(function ($query) use ($search) {
                $query->where('libelle', 'like', "%{$search}%")
                      ->orWhere('code_barre', $search)
                      ->orWhere('reference', 'like', "%{$search}%");
            })
            ->orderByRaw('CASE WHEN code_barre = ? THEN 0 ELSE 1 END', [$search])
            ->select('id', 'libelle',
                'prix_detail', 'seuil_detail',
                'prix_moyen',  'seuil_moyen',
                'prix_gros',   'seuil_gros',
                'stock_actuel', 'unite', 'reference', 'code_barre')
            ->limit(10)
            ->get();

        // Ajouter les paliers calculés à chaque produit
        $produits = $produits->map(function ($p) {
            return [
                'id'           => $p->id,
                'libelle'      => $p->libelle,
                'reference'    => $p->reference,
                'code_barre'   => $p->code_barre,
                'stock_actuel' => (float) $p->stock_actuel,
                'unite'        => $p->unite,
                'prix_detail'  => (float) $p->prix_detail,
                'seuil_detail' => (int) ($p->seuil_detail ?? 1),
                'prix_moyen'   => $p->prix_moyen  ? (float) $p->prix_moyen  : null,
                'seuil_moyen'  => $p->seuil_moyen ? (int)   $p->seuil_moyen : null,
                'prix_gros'    => $p->prix_gros   ? (float) $p->prix_gros   : null,
                'paliers'      => $p->paliers,
            ];
        });

        return response()->json($produits);
    }

    /**
     * Enregistrement de la facture.
     * Le prix est déterminé automatiquement par quantité via getPrixParQuantite().
    */
    // public function valider(Request $request)
    // {
    //     $request->validate([
    //         'mode_paiement'       => 'required|in:espece,carte,mobile_money,credit',
    //         'montant_recu'        => 'required|numeric|min:0',
    //         'client_nom'          => 'nullable|string|max:255',
    //         'client_telephone'    => 'nullable|string|max:20',
    //         'client_id'           => 'nullable|exists:clients,id',
    //         'remise'              => 'nullable|numeric|min:0',
    //         'produits'            => 'required|array|min:1',
    //         'produits.*.id'       => 'required|exists:produits,id',
    //         'produits.*.quantite' => 'required|numeric|min:0.01',
    //     ]);

    //     // ══ VÉRIFICATION STOCK — AVANT la transaction ══
    //     $erreurStock = [];

    //     foreach ($request->produits as $item) {
    //         $produit = Produit::findOrFail($item['id']);
    //         $qte     = (float) $item['quantite'];

    //         if ($produit->stock_actuel <= 0) {
    //             $erreurStock[] = "\"{$produit->libelle}\" est en rupture de stock.";
    //         } elseif ($produit->stock_actuel < $qte) {
    //             $erreurStock[] = sprintf(
    //                 '"%s" : stock insuffisant (dispo : %s %s · demandé : %s %s)',
    //                 $produit->libelle,
    //                 number_format($produit->stock_actuel, 2, ',', ' '),
    //                 $produit->unite,
    //                 number_format($qte, 2, ',', ' '),
    //                 $produit->unite
    //             );
    //         }
    //     }

    //     if (!empty($erreurStock)) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Stock insuffisant pour ' . count($erreurStock) . ' produit(s).',
    //             'erreurs' => $erreurStock,
    //         ], 422);
    //     }
    //     // ══ FIN VÉRIFICATION STOCK ══

    //     DB::beginTransaction();


    //     try {
    //         $sousTotal = 0;
    //         $lignes    = [];

    //         foreach ($request->produits as $item) {
    //             $produit = Produit::findOrFail($item['id']);
    //             $qte     = (float) $item['quantite'];

    //             $prixUnitaire = $produit->getPrixParQuantite($qte);
    //             $sousLigne    = $prixUnitaire * $qte;
    //             $sousTotal   += $sousLigne;

    //             $lignes[] = [
    //                 'produit'       => $produit,
    //                 'quantite'      => $qte,
    //                 'prix_unitaire' => $prixUnitaire,
    //                 'sous_total'    => $sousLigne,
    //             ];
    //         }

    //         $remise   = (float) ($request->remise ?? 0);
    //         $total    = $sousTotal - $remise;
    //         $isCredit = $request->mode_paiement === 'credit';
    //         $monnaie  = $isCredit ? 0 : max($request->montant_recu - $total, 0);
    //         $clientId = $request->input('client_id');

    //         // ── Créer la facture ──
    //         $facture = Facture::create([
    //             'user_id'          => auth()->id(),
    //             'numero'           => Facture::genererNumero(),
    //             'client_id'        => $clientId,
    //             'client_telephone' => $request->client_telephone,
    //             'statut'           => $isCredit ? 'en_cours' : 'payee',
    //             'mode_paiement'    => $request->mode_paiement,
    //             'sous_total'       => $sousTotal,
    //             'remise'           => $remise,
    //             'total'            => $total,
    //             'montant_recu'     => $isCredit ? 0 : $request->montant_recu,
    //             'monnaie'          => $monnaie,
    //             'reste_a_payer'    => $isCredit ? $total : 0,
    //         ]);

    //         $factureId = $facture->id; // ← garder l'id avant commit

    //         // ── Créer les lignes + déduire le stock ──
    //         foreach ($lignes as $ligne) {
    //             FactureLigne::create([
    //                 'facture_id'    => $facture->id,
    //                 'produit_id'    => $ligne['produit']->id,
    //                 'libelle'       => $ligne['produit']->libelle,
    //                 'quantite'      => $ligne['quantite'],
    //                 'prix_unitaire' => $ligne['prix_unitaire'],
    //                 'sous_total'    => $ligne['sous_total'],
    //             ]);

    //             $ligne['produit']->sortieStock(
    //                 $ligne['quantite'],
    //                 $ligne['prix_unitaire'],
    //                 auth()->id(),
    //                 'Vente facture ' . $facture->numero,
    //                 $facture->id
    //             );
    //         }

    //         // ── Règlement ──
    //         if (!$isCredit) {
    //             Reglement::create([
    //                 'facture_id'    => $facture->id,
    //                 'user_id'       => auth()->id(),
    //                 'montant'       => min($request->montant_recu, $total),
    //                 'mode_paiement' => $request->mode_paiement,
    //                 'notes'         => 'Paiement caisse',
    //             ]);
    //         }

    //         DB::commit();

    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Erreur : ' . $e->getMessage(),
    //         ], 500);
    //     }

    //     // ════════════════════════════════════════════════════
    //     // Normalisation e-MECeF — HORS transaction DB
    //     // On recharge depuis la BD pour avoir toutes les relations
    //     // ════════════════════════════════════════════════════
    //     $facture = Facture::with(['lignes.produit', 'client', 'user'])
    //                     ->findOrFail($factureId);

    //     $normalisee   = false;
    //     $emecef       = null;
    //     $emecefErreur = null;

    //     try {
    //         $service = new EmecefService();
    //         $result  = $service->normaliser($facture);

    //         if ($result['success']) {

    //             // UPDATE direct avec query builder — contourne tout cache Eloquent
    //             DB::table('factures')
    //             ->where('id', $factureId)
    //             ->update([
    //                 'emecef_uid'      => $result['uid'],
    //                 'emecef_code'     => $result['codeMECeFDGI'],
    //                 'emecef_qr'       => $result['qrCode'],
    //                 'emecef_datetime' => $result['dateTime'],
    //                 'emecef_counters' => $result['counters'],
    //                 'emecef_nim'      => $result['nim'],
    //                 'normalisee'      => true,
    //                 'normalisee_at'   => now(),
    //             ]);

    //             $normalisee = true;
    //             $emecef     = $result;

    //             // Log::info('eMECeF ✓ facture ' . $facture->numero . ' — code: ' . $result['codeMECeFDGI']);

    //         } else {
    //             $emecefErreur = $result['errorDesc'] ?? 'Erreur inconnue e-MECeF';
    //             // Log::warning('eMECeF ✗ facture ' . $facture->numero . ' : ' . $emecefErreur);
    //         }

    //     } catch (\Exception $e) {
    //         $emecefErreur = $e->getMessage();
    //         // Log::error('eMECeF exception facture ' . $facture->numero . ' : ' . $e->getMessage());
    //     }

    //     return response()->json([
    //         'success'       => true,
    //         'facture_id'    => $factureId,
    //         'numero'        => $facture->numero,
    //         'total'         => $total,
    //         'monnaie'       => $monnaie,
    //         'is_credit'     => $isCredit,
    //         'normalisee'    => $normalisee,
    //         'emecef_code'   => $emecef['codeMECeFDGI'] ?? null,
    //         'emecef_qr'     => $emecef['qrCode']       ?? null,
    //         'emecef_erreur' => $emecefErreur,
    //         'message'       => $isCredit
    //             ? 'Vente à crédit enregistrée — ' . number_format($total, 0, ',', ' ') . ' FCFA à encaisser.'
    //             : 'Vente enregistrée.' . ($normalisee ? ' ✓ Facture normalisée.' : ''),
    //     ]);
    // }

    public function valider(Request $request)
    {
        $request->validate([
            'mode_paiement'       => 'required|in:espece,carte,mobile_money,credit',
            'montant_recu'        => 'required|numeric|min:0',
            'client_nom'          => 'nullable|string|max:255',
            'client_telephone'    => 'nullable|string|max:20',
            'client_id'           => 'nullable|exists:clients,id',
            'remise'              => 'nullable|numeric|min:0',
            'produits'            => 'required|array|min:1',
            'produits.*.id'       => 'required|exists:produits,id',
            'produits.*.quantite' => 'required|numeric|min:0.01',
            'produits.*.prix_unitaire' => 'nullable|numeric|min:0',
        ]);

        // ══ VÉRIFICATION STOCK — AVANT la transaction ══
        $erreurStock = [];

        foreach ($request->produits as $item) {
            $produit = Produit::findOrFail($item['id']);
            $qte     = (float) $item['quantite'];

            if ($produit->stock_actuel <= 0) {
                $erreurStock[] = "\"{$produit->libelle}\" est en rupture de stock.";
            } elseif ($produit->stock_actuel < $qte) {
                $erreurStock[] = sprintf(
                    '"%s" : stock insuffisant (dispo : %s %s · demandé : %s %s)',
                    $produit->libelle,
                    number_format($produit->stock_actuel, 2, ',', ' '),
                    $produit->unite,
                    number_format($qte, 2, ',', ' '),
                    $produit->unite
                );
            }
        }

        if (!empty($erreurStock)) {
            return response()->json([
                'success' => false,
                'message' => 'Stock insuffisant pour ' . count($erreurStock) . ' produit(s).',
                'erreurs' => $erreurStock,
            ], 422);
        }
        // ══ FIN VÉRIFICATION STOCK ══

        DB::beginTransaction();


        try {
            $sousTotal = 0;
            $lignes    = [];

            foreach ($request->produits as $item) {
                $produit = Produit::findOrFail($item['id']);
                $qte     = (float) ($item['quantite'] ?? 0);
                $prixUnitaire = isset($item['prix_unitaire']) && $item['prix_unitaire'] !== ''
                    ? (float) $item['prix_unitaire']
                    : $produit->getPrixParQuantite($qte);

                $sousLigne  = round($prixUnitaire * $qte, 2);
                $sousTotal += $sousLigne;

                $lignes[] = [
                    'produit'       => $produit,
                    'quantite'      => $qte,
                    'prix_unitaire' => $prixUnitaire,
                    'sous_total'    => $sousLigne,
                ];
            }

            $remise      = (float) ($request->remise ?? 0);
            $isCredit    = $request->mode_paiement === 'credit';
            $montantRecu = (float) ($request->montant_recu ?? 0);
            $total       = max(round($sousTotal - $remise, 2), 0);
            $monnaie     = $isCredit ? 0 : max($montantRecu - $total, 0);
            $clientId    = $request->input('client_id');
            $clientNom   = $request->input('client_nom');
            $clientTelephone = $request->input('client_telephone');

            // ── Créer la facture ──
            $facture = Facture::create([
                'user_id'          => auth()->id(),
                'numero'           => Facture::genererNumero(),
                'client_id'        => $clientId,
                'client_nom'       => $clientNom,
                'client_telephone' => $clientTelephone,
                'statut'           => $isCredit ? 'en_cours' : 'payee',
                'mode_paiement'    => $request->mode_paiement,
                'sous_total'       => $sousTotal,
                'remise'           => $remise,
                'total'            => $total,
                'montant_recu'     => $isCredit ? 0 : $montantRecu,
                'monnaie'          => $monnaie,
                'reste_a_payer'    => $isCredit ? $total : 0,
            ]);

            $factureId = $facture->id; // ← garder l'id avant commit

            // ── Créer les lignes + déduire le stock ──
            foreach ($lignes as $ligne) {
                FactureLigne::create([
                    'facture_id'    => $facture->id,
                    'produit_id'    => $ligne['produit']->id,
                    'libelle'       => $ligne['produit']->libelle,
                    'quantite'      => $ligne['quantite'],
                    'prix_unitaire' => $ligne['prix_unitaire'],
                    'sous_total'    => $ligne['sous_total'],
                ]);

                $ligne['produit']->sortieStock(
                    $ligne['quantite'],
                    $ligne['prix_unitaire'],
                    auth()->id(),
                    'Vente facture ' . $facture->numero,
                    $facture->id
                );
            }

            // ── Règlement ──
            if (!$isCredit) {
                Reglement::create([
                    'facture_id'    => $facture->id,
                    'user_id'       => auth()->id(),
                    'montant'       => min($montantRecu, $total),
                    'mode_paiement' => $request->mode_paiement,
                    'notes'         => 'Paiement caisse',
                ]);
            }

            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Erreur : ' . $e->getMessage(),
            ], 500);
        }

        // ════════════════════════════════════════════════════
        // Normalisation e-MECeF — HORS transaction DB
        // On recharge depuis la BD pour avoir toutes les relations
        // ════════════════════════════════════════════════════
        $facture = Facture::with(['lignes.produit', 'client', 'user'])
                        ->findOrFail($factureId);

        $normalisee   = false;
        $emecef       = null;
        $emecefErreur = null;

        try {
            $service = new EmecefService();
            $result  = $service->normaliser($facture);

            if ($result['success']) {

                // UPDATE direct avec query builder — contourne tout cache Eloquent
                DB::table('factures')
                ->where('id', $factureId)
                ->update([
                    'emecef_uid'      => $result['uid'],
                    'emecef_code'     => $result['codeMECeFDGI'],
                    'emecef_qr'       => $result['qrCode'],
                    'emecef_datetime' => $result['dateTime'],
                    'emecef_counters' => $result['counters'],
                    'emecef_nim'      => $result['nim'],
                    'normalisee'      => true,
                    'normalisee_at'   => now(),
                ]);

                $normalisee = true;
                $emecef     = $result;

                // Log::info('eMECeF ✓ facture ' . $facture->numero . ' — code: ' . $result['codeMECeFDGI']);

            } else {
                $emecefErreur = $result['errorDesc'] ?? 'Erreur inconnue e-MECeF';
                // Log::warning('eMECeF ✗ facture ' . $facture->numero . ' : ' . $emecefErreur);
            }

        } catch (\Exception $e) {
            $emecefErreur = $e->getMessage();
            // Log::error('eMECeF exception facture ' . $facture->numero . ' : ' . $e->getMessage());
        }

        return response()->json([
            'success'       => true,
            'facture_id'    => $factureId,
            'numero'        => $facture->numero,
            'total'         => $total,
            'monnaie'       => $monnaie,
            'is_credit'     => $isCredit,
            'normalisee'    => $normalisee,
            'emecef_code'   => $emecef['codeMECeFDGI'] ?? null,
            'emecef_qr'     => $emecef['qrCode']       ?? null,
            'emecef_erreur' => $emecefErreur,
            'message'       => $isCredit
                ? 'Vente à crédit enregistrée — ' . number_format($total, 0, ',', ' ') . ' FCFA à encaisser.'
                : 'Vente enregistrée.' . ($normalisee ? ' ✓ Facture normalisée.' : ''),
        ]);
    }

    
    public function ticket($id)
    {
        
        $facture = Facture::with('lignes.produit')->findOrFail($id);

        return view('caisse.ticket', compact('facture'));
    }
}
