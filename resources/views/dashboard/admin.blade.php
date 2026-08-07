{{-- ════ KPI CARDS ════ --}}
<div class="row">
    <div class="col-6 col-lg-3">
        <a href="#" class="kpi-card kpi-green">
            <div class="kpi-ico"><i class="fas fa-chart-line"></i></div>
            <div class="kpi-val">
                {{ number_format($caJourCaissier, 0, ',', ' ') }}
                <sup>FCFA</sup>
            </div>
            <div class="kpi-lbl">CA du jour (Caissier)</div>
            <div class="kpi-foot">
                <i class="fas fa-arrow-right"></i> Voir le rapport
            </div>
        </a>
    </div>

    <div class="col-6 col-lg-3">
        <a href="#" class="kpi-card kpi-green">
            <div class="kpi-ico"><i class="fas fa-cash-register"></i></div>
            <div class="kpi-val">
                {{ number_format($caJourCaissierHaut, 0, ',', ' ') }}
                <sup>FCFA</sup>
            </div>
            <div class="kpi-lbl">CA du jour (Caissier Haut)</div>
            <div class="kpi-foot">
                <i class="fas fa-arrow-right"></i> Voir le rapport
            </div>
        </a>
    </div>
    <div class="col-6 col-lg-3">
        <a href="{{ route('rapports.ventes') }}" class="kpi-card kpi-red">
            <div class="kpi-ico"><i class="fas fa-chart-pie"></i></div>
            <div class="kpi-val">{{ number_format($caJourTotal, 0, ',', ' ') }}
                <sup>FCFA</sup>
            </div>
            <div class="kpi-lbl">CA du jour (Total)</div>
            <div class="kpi-foot">
                <i class="fas fa-arrow-right"></i> Voir le rapport
            </div>
        </a>
    </div>
    <div class="col-6 col-lg-3">
        <a href="{{ route('rapports.ventes') }}" class="kpi-card kpi-blue">
            <div class="kpi-ico"><i class="fas fa-calendar-alt"></i></div>
            <div class="kpi-val">
                {{ number_format($caMois, 0, ',', ' ') }}
                <sup>FCFA</sup>
            </div>
            <div class="kpi-lbl">CA du mois</div>
            <div class="kpi-foot">
                <i class="fas fa-arrow-right"></i> Voir le rapport
            </div>
        </a>
    </div>
</div>

{{-- ════ STAT BOXES ════ --}}
<div class="row">
    <div class="col-md-4">
        <div class="stat-box">
            <div class="stat-box-ico green">
                <i class="fas fa-boxes"></i>
            </div>
            <div>
                <div class="stat-box-val">{{ $totalProduits }}</div>
                <div class="stat-box-lbl">Produits actifs</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-box">
            <div class="stat-box-ico yellow">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <div>
                <div class="stat-box-val">{{ $produitsFaibles }}</div>
                <div class="stat-box-lbl">Stock faible</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-box">
            <div class="stat-box-ico red">
                <i class="fas fa-times-circle"></i>
            </div>
            <div>
                <div class="stat-box-val">{{ $produitsRupture }}</div>
                <div class="stat-box-lbl">Rupture de stock</div>
            </div>
        </div>
    </div>
</div>

{{-- ════ GRAPHIQUE + TOP PRODUITS ════ --}}
<div class="row">

    {{-- Graphique --}}
    <div class="col-12 col-lg-8">
        <div class="pd-card">
            <div class="pd-card-head">
                <h6 class="pd-card-title">
                    <span class="pd-card-ico" style="background:#3b82f6">
                        <i class="fas fa-chart-bar"></i>
                    </span>
                    Chiffre d'affaires — 7 derniers jours
                </h6>
            </div>
            <div class="chart-wrap">
                <canvas id="caChart" height="95"></canvas>
            </div>
        </div>
    </div>

    {{-- Top produits --}}
    <div class="col-12 col-lg-4">
        <div class="pd-card">
            <div class="pd-card-head">
                <h6 class="pd-card-title">
                    <span class="pd-card-ico" style="background:#f59e0b">
                        <i class="fas fa-trophy"></i>
                    </span>
                    Top 5 du mois
                </h6>
            </div>
            @forelse($topProduits as $i => $produit)
                <div class="top-item">
                    <div
                        class="top-rank {{ $i == 0 ? 'rank-1' : ($i == 1 ? 'rank-2' : ($i == 2 ? 'rank-3' : 'rank-n')) }}">
                        {{ $i + 1 }}
                    </div>
                    <span class="top-name">{{ $produit->libelle }}</span>
                    <span class="top-qty">
                        {{ number_format($produit->total_vendu, 0, ',', ' ') }} u.
                    </span>
                </div>
            @empty
                <div class="text-center py-4" style="color:#94a3b8; font-size:13px">
                    <i class="fas fa-chart-bar fa-2x d-block mb-2" style="opacity:.3"></i>
                    Aucune vente ce mois
                </div>
            @endforelse
        </div>
    </div>

</div>

{{-- ════ FACTURES + ALERTES ════ --}}
<div class="row">

    {{-- Dernières factures --}}
    <div class="col-12 col-lg-7">
        <div class="pd-card">
            <div class="pd-card-head">
                <h6 class="pd-card-title">
                    <span class="pd-card-ico" style="background:#0f172a">
                        <i class="fas fa-file-invoice"></i>
                    </span>
                    Dernières factures
                </h6>
                <a href="{{ route('factures.index') }}" class="pd-card-action">
                    <i class="fas fa-arrow-right mr-1"></i> Voir tout
                </a>
            </div>
            <div class="table-responsive">
                <table class="table dash-table mb-0">
                    <thead>
                        <tr>
                            <th>N° Facture</th>
                            <th>Client</th>
                            <th class="text-right">Total</th>
                            <th>Statut</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($dernieresFactures as $facture)
                            <tr>
                                <td>
                                    <a href="{{ route('factures.show', $facture) }}"
                                        style="font-weight:700; color:#0f3460">
                                        {{ $facture->numero }}
                                    </a>
                                </td>
                                <td style="color:#374151; font-weight:600">
                                    {{ $facture->client_nom ?? 'Client anonyme' }}
                                </td>
                                <td class="text-right" style="font-weight:800; color:#0f172a">
                                    {{ number_format($facture->total, 0, ',', ' ') }}
                                    <small style="color:#94a3b8; font-weight:500">FCFA</small>
                                </td>
                                <td>
                                    @if ($facture->statut == 'payee')
                                        <span class="s-badge s-payee">
                                            <i class="fas fa-check-circle"></i> Payée
                                        </span>
                                    @elseif($facture->statut == 'en_cours')
                                        <span class="s-badge s-cours">
                                            <i class="fas fa-clock"></i> En cours
                                        </span>
                                    @else
                                        <span class="s-badge s-annulee">
                                            <i class="fas fa-ban"></i> Annulée
                                        </span>
                                    @endif
                                </td>
                                <td style="color:#94a3b8; font-size:11px">
                                    {{ $facture->created_at->format('d/m/y H:i') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4" style="color:#94a3b8">
                                    <i class="fas fa-file-invoice fa-2x d-block mb-2" style="opacity:.3"></i>
                                    Aucune facture
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Produits en alerte --}}
    <div class="col-12 col-lg-5">
        <div class="pd-card">
            <div class="pd-card-head">
                <h6 class="pd-card-title">
                    <span class="pd-card-ico" style="background:#f59e0b">
                        <i class="fas fa-exclamation-triangle"></i>
                    </span>
                    Produits en alerte
                </h6>
                <a href="{{ route('alertes.index') }}" class="pd-card-action">
                    <i class="fas fa-arrow-right mr-1"></i> Voir tout
                </a>
            </div>

            @forelse($produitsEnAlerte as $produit)
                @php $isRupture = $produit->stock_actuel <= 0; @endphp
                <div class="alerte-item">
                    <div class="alerte-ico {{ $isRupture ? 'rupture' : 'faible' }}">
                        <i class="fas fa-{{ $isRupture ? 'times-circle' : 'exclamation-triangle' }}"></i>
                    </div>
                    <div style="flex:1; min-width:0">
                        <div class="alerte-name">{{ $produit->libelle }}</div>
                        <div class="alerte-stock">
                            Stock : {{ $produit->stock_actuel }} {{ $produit->unite }}
                        </div>
                    </div>
                    <span class="s-badge {{ $isRupture ? 's-rupture' : 's-faible' }}">
                        {{ $isRupture ? 'Rupture' : 'Faible' }}
                    </span>
                </div>
            @empty
                <div class="text-center py-5" style="color:#94a3b8; font-size:13px">
                    <div
                        style="width:50px; height:50px; border-radius:50%;
                                    background:#d1fae5; display:flex; align-items:center;
                                    justify-content:center; margin:0 auto 12px">
                        <i class="fas fa-check" style="color:#10b981; font-size:20px"></i>
                    </div>
                    Tous les stocks sont OK !
                </div>
            @endforelse
        </div>
    </div>

</div>
