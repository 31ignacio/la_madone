@extends('layouts.app')

@section('title', 'Récapitulatif de Stock')
@section('page-title', 'Récapitulatif de Stock')

@section('breadcrumb')
    <li class="breadcrumb-item active">Récapitulatif</li>
@endsection

@push('styles')
<style>
:root {
    --dark:   #0f172a;
    --border: #e2e8f0;
    --sh:     0 4px 24px rgba(15,52,96,.09);
    --r:      16px;
}
.m-hero {
    background: linear-gradient(140deg, #0f172a 0%, #1e3a5f 55%, #0f3460 100%);
    border-radius: var(--r); padding: 22px 26px; margin-bottom: 20px;
    position: relative; overflow: hidden;
    box-shadow: 0 12px 40px rgba(15,52,96,.28);
}
.m-hero::before {
    content:''; position:absolute; top:-60px; right:-60px;
    width:220px; height:220px; border-radius:50%;
    background:radial-gradient(circle,rgba(233,69,96,.15),transparent 65%);
    pointer-events:none;
}
.m-hero-title { font-size:clamp(.95rem,3vw,1.3rem); font-weight:800; color:#fff; margin:0; }
.m-hero-sub   { font-size:11px; color:rgba(255,255,255,.55); margin-top:3px; }
.filter-active {
    display:inline-flex; align-items:center; gap:4px;
    background:rgba(255,255,255,.15); border:1px solid rgba(255,255,255,.25);
    color:#fff; padding:3px 10px; border-radius:20px; font-size:10px; font-weight:700;
}
.pd-card {
    background:#fff; border:1px solid var(--border);
    border-radius:var(--r); box-shadow:var(--sh);
    overflow:hidden; margin-bottom:20px;
}
.pd-card-head {
    padding:14px 20px; border-bottom:1px solid var(--border);
    display:flex; align-items:center; justify-content:space-between;
    flex-wrap:wrap; gap:10px;
}
.pd-card-title {
    display:flex; align-items:center; gap:9px;
    font-size:11px; font-weight:800; text-transform:uppercase;
    letter-spacing:.7px; color:var(--dark); margin:0;
}
.pd-card-ico {
    width:28px; height:28px; border-radius:7px;
    display:flex; align-items:center; justify-content:center;
    font-size:11px; color:#fff; flex-shrink:0;
}
.filter-wrap { padding:14px 20px; border-bottom:none; background:#fafbff; }

/* ══ TABLEAU ══ */
.recap-table { width:100%; border-collapse:collapse; }
.recap-table thead tr { background:#f8fafc; border-bottom:2px solid #e2e8f0; }
.recap-table th {
    padding:12px 20px; font-size:10px; font-weight:800;
    text-transform:uppercase; letter-spacing:.6px; color:#64748b; white-space:nowrap;
}
.recap-table th:not(:first-child) { text-align:right; }
.recap-table td { padding:14px 20px; border-bottom:1px solid #f1f5f9; vertical-align:middle; }
.recap-table td:not(:first-child) { text-align:right; }
.recap-table tbody tr:last-child td { border-bottom:none; }
.recap-table tbody tr:hover td { background:#fafbff; }

.r-nom { font-size:13px; font-weight:700; color:#0f172a; }
.r-ref { font-size:10px; color:#94a3b8; margin-top:2px; }

.pill-e {
    display:inline-flex; align-items:center; gap:5px;
    background:#d1fae5; color:#059669;
    padding:5px 14px; border-radius:20px; font-size:12px; font-weight:800;
}
.pill-s {
    display:inline-flex; align-items:center; gap:5px;
    background:#fee2e2; color:#dc2626;
    padding:5px 14px; border-radius:20px; font-size:12px; font-weight:800;
}
.pill-r-ok {
    display:inline-flex; align-items:center; gap:5px;
    background:#dbeafe; color:#1d4ed8;
    padding:5px 14px; border-radius:20px; font-size:12px; font-weight:800;
}
.pill-r-neg {
    display:inline-flex; align-items:center; gap:5px;
    background:#fef3c7; color:#b45309;
    padding:5px 14px; border-radius:20px; font-size:12px; font-weight:800;
}
.recap-total td {
    background:#f8fafc !important; border-top:2px solid #e2e8f0 !important;
    padding-top:15px !important; padding-bottom:15px !important;
}
.recap-total-lbl {
    font-size:11px; font-weight:800; text-transform:uppercase; letter-spacing:.5px; color:#64748b;
}
</style>
@endpush

@section('content')

{{-- HERO --}}
<div class="m-hero">
    <div style="position:relative;z-index:1">
        <p class="m-hero-sub mb-1">
            <i class="fas fa-boxes mr-1"></i> Gestion des stocks
            @if(request()->hasAny(['produit_id','date_debut','date_fin']))
                <span class="filter-active ml-2"><i class="fas fa-filter"></i> Filtre actif</span>
                @if(request('date_debut'))
                    <span class="filter-active ml-1">
                        Du {{ \Carbon\Carbon::parse(request('date_debut'))->format('d/m/Y') }}
                    </span>
                @endif
                @if(request('date_fin'))
                    <span class="filter-active ml-1">
                        Au {{ \Carbon\Carbon::parse(request('date_fin'))->format('d/m/Y') }}
                    </span>
                @endif
            @endif
        </p>
        <h4 class="m-hero-title">Récapitulatif des Mouvements</h4>
    </div>
</div>

{{-- FILTRES --}}
<div class="pd-card">
    <div class="filter-wrap">
        <form method="GET" action="{{ route('stock.mouvements') }}">
            <div class="row align-items-end">

                <div class="col-12 col-sm-6 col-md-4 mb-2">
                    <label class="small font-weight-bold text-muted mb-1">
                        <i class="fas fa-box mr-1"></i> Produit
                    </label>
                    <select name="produit_id" class="form-control form-control-sm select2"
                            onchange="this.form.submit()">
                        <option value="">Tous les produits</option>
                        @foreach($produits as $p)
                            <option value="{{ $p->id }}"
                                {{ request('produit_id') == $p->id ? 'selected' : '' }}>
                                {{ $p->libelle }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-6 col-sm-3 col-md-2 mb-2">
                    <label class="small font-weight-bold text-muted mb-1">
                        <i class="fas fa-calendar mr-1"></i> Du
                    </label>
                    <input type="date" name="date_debut" class="form-control form-control-sm"
                           value="{{ request('date_debut') }}" onchange="this.form.submit()">
                </div>

                <div class="col-6 col-sm-3 col-md-2 mb-2">
                    <label class="small font-weight-bold text-muted mb-1">
                        <i class="fas fa-calendar mr-1"></i> Au
                    </label>
                    <input type="date" name="date_fin" class="form-control form-control-sm"
                           value="{{ request('date_fin') }}" onchange="this.form.submit()">
                </div>

                <div class="col-12 col-sm-6 col-md-4 mb-2">
                    <label class="small d-block mb-1">&nbsp;</label>
                    <div class="d-flex" style="gap:6px">
                        <button type="submit" class="btn btn-primary btn-sm flex-fill">
                            <i class="fas fa-search mr-1"></i> Filtrer
                        </button>
                        @if(request()->hasAny(['produit_id','date_debut','date_fin']))
                            <a href="{{ route('stock.mouvements') }}"
                               class="btn btn-secondary btn-sm flex-fill">
                                <i class="fas fa-times mr-1"></i> Reset
                            </a>
                        @endif
                    </div>
                </div>

            </div>
        </form>
    </div>
</div>

{{-- RÉCAPITULATIF --}}
<div class="pd-card">
    <div class="pd-card-head">
        <h6 class="pd-card-title">
            <span class="pd-card-ico" style="background:linear-gradient(135deg,#059669,#10b981)">
                <i class="fas fa-table"></i>
            </span>
            Récapitulatif par produit
            @if(request('date_debut') || request('date_fin'))
                <span style="font-size:10px;font-weight:600;color:#94a3b8;
                             text-transform:none;letter-spacing:0;margin-left:4px">
                    —
                    @if(request('date_debut'))
                        Du {{ \Carbon\Carbon::parse(request('date_debut'))->format('d/m/Y') }}
                    @endif
                    @if(request('date_fin'))
                        au {{ \Carbon\Carbon::parse(request('date_fin'))->format('d/m/Y') }}
                    @endif
                </span>
            @endif
        </h6>
    </div>

    <div class="table-responsive">
        <table class="recap-table">
            <thead>
                <tr>
                    <th>Produit</th>
                    <th>Entrées</th>
                    <th>Sorties</th>
                    <th>Stock actuel</th>  {{-- ← Reste = stock réel maintenant --}}
                </tr>
            </thead>
            <tbody>

                @forelse($rapport as $row)
                <tr>
                    <td>
                        <div class="r-nom">
    {{ optional($row->produit)->libelle ?? 'Produit supprimé' }}
</div>
                       <div class="r-ref">
    {{ $row->produit?->reference ?? '—' }}
</div>
                    </td>
                    <td>
                        <span class="pill-e">
                            <i class="fas fa-arrow-down" style="font-size:9px"></i>
                            {{ number_format($row->total_entree, 2, ',', ' ') }}
                        </span>
                    </td>
                    <td>
                        <span class="pill-s">
                            <i class="fas fa-arrow-up" style="font-size:9px"></i>
                            {{ number_format($row->total_sortie, 2, ',', ' ') }}
                        </span>
                    </td>
                    <td>
                        @if($row->stock_actuel > 0)
                            <span class="pill-r-ok">
                                <i class="fas fa-box" style="font-size:9px"></i>
                                {{ number_format($row->stock_actuel, 2, ',', ' ') }}
                            </span>
                        @else
                            <span class="pill-r-neg">
                                <i class="fas fa-exclamation-triangle" style="font-size:9px"></i>
                                {{ number_format($row->stock_actuel, 2, ',', ' ') }}
                            </span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="text-center py-5" style="color:#94a3b8">
                        <i class="fas fa-box-open fa-2x d-block mb-2" style="opacity:.3"></i>
                        Aucune donnée
                    </td>
                </tr>
                @endforelse

                {{-- Ligne total --}}
                @if($rapport->count() > 1)
                @php
                    $gE = $rapport->sum('total_entree');
                    $gS = $rapport->sum('total_sortie');
                    $gStock = $rapport->sum('stock_actuel');
                @endphp
                <tr class="recap-total">
                    <td>
                        <span class="recap-total-lbl">
                            <i class="fas fa-calculator mr-1" style="color:#059669"></i>
                            Total général
                        </span>
                    </td>
                    <td>
                        <span class="pill-e">
                            <i class="fas fa-arrow-down" style="font-size:9px"></i>
                            {{ number_format($gE, 2, ',', ' ') }}
                        </span>
                    </td>
                    <td>
                        <span class="pill-s">
                            <i class="fas fa-arrow-up" style="font-size:9px"></i>
                            {{ number_format($gS, 2, ',', ' ') }}
                        </span>
                    </td>
                    <td>
                        <span class="pill-r-ok">
                            <i class="fas fa-box" style="font-size:9px"></i>
                            {{ number_format($gStock, 2, ',', ' ') }}
                        </span>
                    </td>
                </tr>
                @endif

            </tbody>
        </table>
    </div>
</div>

@endsection