{{-- resources/views/factures/pdf.blade.php --}}
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Facture {{ $facture->numero }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 11px;
            color: #333;
            padding: 30px;
            background: #fff;
        }

        /* ── HEADER ── */
        .header {
            display: table;
            width: 100%;
            margin-bottom: 25px;
            border-bottom: 3px solid #1a1a2e;
            padding-bottom: 15px;
        }
        .header-left  { display: table-cell; vertical-align: middle; width: 60%; }
        .header-right { display: table-cell; vertical-align: middle; width: 40%; text-align: right; }

        .header-left h1 {
            font-size: 26px;
            color: #1a1a2e;
            letter-spacing: 2px;
            font-weight: 900;
        }
        .header-left p {
            font-size: 10px;
            color: #777;
            margin-top: 4px;
        }

        .facture-label {
            background: #1a1a2e;
            color: white;
            padding: 8px 18px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: bold;
            display: inline-block;
            letter-spacing: 1px;
        }
        .facture-numero {
            font-size: 16px;
            font-weight: bold;
            color: #1a1a2e;
            margin-top: 5px;
        }
        .facture-date {
            font-size: 10px;
            color: #999;
            margin-top: 3px;
        }

        /* ── INFOS ── */
        .infos {
            display: table;
            width: 100%;
            margin-bottom: 20px;
            border-spacing: 10px;
        }
        .infos-cell {
            display: table-cell;
            width: 50%;
            vertical-align: top;
            padding: 12px 15px;
            border-radius: 6px;
        }
        .infos-cell.left  { background: #f0f4ff; border-left: 4px solid #1a1a2e; }
        .infos-cell.right { background: #f8f9fa; border-left: 4px solid #aaa; }

        .infos-cell h4 {
            font-size: 10px;
            text-transform: uppercase;
            color: #1a1a2e;
            letter-spacing: 1px;
            margin-bottom: 8px;
            font-weight: bold;
        }
        .infos-cell table { width: 100%; }
        .infos-cell td { padding: 3px 0; font-size: 11px; border: none; }
        .infos-cell td:first-child { color: #777; width: 45%; }
        .infos-cell td:last-child  { font-weight: bold; color: #333; }

        /* ── STATUT BADGE ── */
        .statut-badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 9px;
            font-weight: bold;
            color: white;
        }
        .statut-payee   { background: #27ae60; }
        .statut-credit  { background: #f39c12; }
        .statut-annulee { background: #e74c3c; }

        /* ── TABLE PRODUITS ── */
        table.produits {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        table.produits thead tr {
            background: #1a1a2e;
            color: white;
        }
        table.produits thead th {
            padding: 9px 10px;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: .5px;
            border: none;
        }
        table.produits tbody td {
            padding: 8px 10px;
            border-bottom: 1px solid #f0f0f0;
            font-size: 11px;
        }
        table.produits tbody tr:nth-child(even) { background: #fafafa; }

        /* ── TOTAUX ── */
        .totaux {
            display: table;
            width: 100%;
        }
        .totaux-spacer { display: table-cell; width: 55%; }
        .totaux-box    { display: table-cell; width: 45%; vertical-align: top; }

        table.total-table {
            width: 100%;
            border-collapse: collapse;
        }
        table.total-table td {
            padding: 6px 10px;
            border: none;
            font-size: 11px;
        }
        table.total-table tr.sep td {
            border-top: 1px solid #eee;
        }
        table.total-table tr.grand-total td {
            background: #1a1a2e;
            color: white;
            font-weight: bold;
            font-size: 14px;
            padding: 10px;
            border-radius: 0;
        }
        table.total-table tr.credit-row td {
            background: #fff8e1;
            color: #e65100;
            font-weight: bold;
        }
        .text-right  { text-align: right; }
        .text-center { text-align: center; }

        /* ── VERSEMENTS ── */
        .versements-section {
            margin-top: 15px;
            border: 1px solid #e0e0e0;
            border-radius: 6px;
            overflow: hidden;
        }
        .versements-title {
            background: #f39c12;
            color: white;
            padding: 7px 12px;
            font-size: 11px;
            font-weight: bold;
        }
        table.versements {
            width: 100%;
            border-collapse: collapse;
        }
        table.versements th {
            background: #fef9f0;
            padding: 6px 10px;
            font-size: 10px;
            color: #777;
            border-bottom: 1px solid #eee;
            text-transform: uppercase;
        }
        table.versements td {
            padding: 6px 10px;
            font-size: 11px;
            border-bottom: 1px solid #f5f5f5;
        }

        /* ── NOTE ── */
        .note-credit {
            margin-top: 15px;
            background: #fff8e1;
            border-left: 4px solid #f39c12;
            padding: 10px 14px;
            border-radius: 4px;
            font-size: 11px;
            color: #7f4f00;
        }

        /* ── FOOTER ── */
        .footer {
            margin-top: 30px;
            border-top: 1px solid #eee;
            padding-top: 12px;
            text-align: center;
            font-size: 10px;
            color: #aaa;
        }
        .footer strong { color: #1a1a2e; }
    </style>
</head>
<body>

{{-- ══ HEADER ══ --}}
<div class="header">
    <div class="header-left">
        <h1> SUPERMARCHÉ</h1>
        <p>Cotonou, Bénin &nbsp;|&nbsp; Tél : (229) 01......... &nbsp;|&nbsp; contact@supermarche.bj</p>
    </div>
    <div class="header-right">
        <span class="facture-label">FACTURE</span>
        <div class="facture-numero">{{ $facture->numero }}</div>
        <div class="facture-date">{{ $facture->created_at->format('d/m/Y à H:i') }}</div>
    </div>
</div>

{{-- ══ INFOS ══ --}}
<div class="infos">
    <div class="infos-cell left">
        <h4>Informations facture</h4>
        <table>
            <tr>
                <td>Caissier</td>
                <td>{{ $facture->user->nom_complet ?? "_" }}</td>
            </tr>
           
            <tr>
                <td>Paiement</td>
                <td>{{ $facture->mode_paiement_label }}</td>
            </tr>
            <tr>
                <td>Statut</td>
                <td>
                    <span class="statut-badge
                        {{ $facture->statut == 'payee'   ? 'statut-payee'   :
                          ($facture->statut == 'annulee' ? 'statut-annulee' :
                                                           'statut-credit') }}">
                        {{ $facture->statut_label }}
                    </span>
                </td>
            </tr>
        </table>
    </div>
    <div class="infos-cell right">
        <h4>Client</h4>
        <table>
            <tr>
                <td>Nom</td>
                <td>
    {{ optional($facture->client)->nom 
        ? $facture->client->nom . ' ' . $facture->client->prenom 
        : 'Client divers' 
    }}
</td>
            </tr>
            <tr>
                <td>Téléphone</td>
                <td>{{ $facture->client->telephone ?? '—' }}</td>
            </tr>
            @if($facture->notes)
            <tr>
                <td>Notes</td>
                <td>{{ $facture->notes }}</td>
            </tr>
            @endif
        </table>
    </div>
</div>

{{-- ══ PRODUITS ══ --}}
<table class="produits">
    <thead>
        <tr>
            <th style="width:5%">#</th>
            <th>Désignation</th>
            <th class="text-right" style="width:15%">Prix unit.</th>
            <th class="text-right" style="width:12%">Qté</th>
            <th class="text-right" style="width:18%">Sous-total</th>
        </tr>
    </thead>
    <tbody>
        @foreach($facture->lignes as $i => $ligne)
            <tr>
                <td class="text-center" style="color:#aaa">{{ $i + 1 }}</td>
                <td><strong>{{ $ligne->libelle }}</strong></td>
                <td class="text-right">{{ number_format($ligne->prix_unitaire, 0, ',', ' ') }} F</td>
                <td class="text-right">{{ $ligne->quantite }}</td>
                <td class="text-right">
                    <strong>{{ number_format($ligne->sous_total, 0, ',', ' ') }} F</strong>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

{{-- ══ TOTAUX ══ --}}
<div class="totaux">
    <div class="totaux-spacer"></div>
    <div class="totaux-box">
        <table class="total-table">
            <tr>
                <td>Sous-total</td>
                <td class="text-right">{{ number_format($facture->sous_total, 0, ',', ' ') }} FCFA</td>
            </tr>
            @if($facture->remise > 0)
            <tr>
                <td style="color:#e74c3c">Remise</td>
                <td class="text-right" style="color:#e74c3c">
                    - {{ number_format($facture->remise, 0, ',', ' ') }} FCFA
                </td>
            </tr>
            @endif
            <tr class="grand-total">
                <td>TOTAL</td>
                <td class="text-right">{{ number_format($facture->total, 0, ',', ' ') }} FCFA</td>
            </tr>

            @if($facture->mode_paiement !== 'credit')
                {{-- Paiement immédiat --}}
                <tr class="sep">
                    <td style="color:#777">Montant reçu</td>
                    <td class="text-right">
                        {{ number_format($facture->montant_recu, 0, ',', ' ') }} FCFA
                    </td>
                </tr>
                <tr>
                    <td style="color:#27ae60"><strong>Monnaie</strong></td>
                    <td class="text-right" style="color:#27ae60">
                        <strong>{{ number_format($facture->monnaie, 0, ',', ' ') }} FCFA</strong>
                    </td>
                </tr>
            @else
                {{-- Crédit --}}
                <tr class="sep">
                    <td style="color:#27ae60">Déjà payé</td>
                    <td class="text-right" style="color:#27ae60">
                        <strong>{{ number_format($facture->montant_paye, 0, ',', ' ') }} FCFA</strong>
                    </td>
                </tr>
                <tr class="credit-row">
                    <td>⏳ Reste dû</td>
                    <td class="text-right">
                        {{ number_format($facture->reste_a_payer, 0, ',', ' ') }} FCFA
                    </td>
                </tr>
            @endif
        </table>
    </div>
</div>

{{-- ══ HISTORIQUE VERSEMENTS (si crédit) ══ --}}
@if($facture->mode_paiement === 'credit' && $facture->reglements->count() > 0)
<div class="versements-section">
    <div class="versements-title">
        📋 Historique des versements ({{ $facture->reglements->count() }})
    </div>
    <table class="versements">
        <thead>
            <tr>
                <th>#</th>
                <th>Date</th>
                <th>Mode</th>
                <th>Notes</th>
                <th class="text-right">Montant</th>
            </tr>
        </thead>
        <tbody>
            @foreach($facture->reglements->sortBy('created_at') as $i => $reg)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $reg->created_at->format('d/m/Y H:i') }}</td>
                    <td>{{ $reg->mode_paiement_label }}</td>
                    <td style="color:#999">{{ $reg->notes ?? '—' }}</td>
                    <td class="text-right" style="color:#27ae60; font-weight:bold;">
                        {{ number_format($reg->montant, 0, ',', ' ') }} FCFA
                    </td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4" class="text-right"
                    style="font-weight:bold; padding:7px 10px; background:#f5f5f5;">
                    Total versé :
                </td>
                <td class="text-right"
                    style="font-weight:bold; padding:7px 10px;
                           background:#f5f5f5; color:#27ae60;">
                    {{ number_format($facture->montant_paye, 0, ',', ' ') }} FCFA
                </td>
            </tr>
        </tfoot>
    </table>
</div>
@endif

{{-- ══ NOTE CREDIT ══ --}}
@if($facture->mode_paiement === 'credit' && $facture->statut === 'en_cours')
<div class="note-credit">
    ⚠️ <strong>Vente à crédit</strong> —
    Reste à régler : <strong>{{ number_format($facture->reste_a_payer, 0, ',', ' ') }} FCFA</strong>
</div>
@endif

{{-- ══ FOOTER ══ --}}
<div class="footer">
    <strong>Merci pour votre achat !</strong>
    &nbsp;|&nbsp;
    SuperMarché &copy; {{ date('Y') }}
    &nbsp;|&nbsp;
    Imprimé le {{ now()->format('d/m/Y à H:i') }}
</div>

</body>
</html>