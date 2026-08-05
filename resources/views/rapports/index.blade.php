{{-- resources/views/rapports/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Rapports & Statistiques')
@section('page-title', 'Rapports & Statistiques')

@section('breadcrumb')
    <li class="breadcrumb-item active">Rapports</li>
@endsection

@push('styles')
<style>
.rp-wrap { font-family: 'DM Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; }

/* ── HERO ── */
.rp-hero {
    background: linear-gradient(135deg, #0f172a 0%, #1e3a5f 55%, #0f3460 100%);
    border-radius: 20px;
    padding: 26px 32px;
    margin-bottom: 28px;
    position: relative; overflow: hidden;
    box-shadow: 0 10px 40px rgba(15,23,42,.2);
    display: flex; align-items: center; justify-content: space-between; gap: 20px;
}
.rp-hero::before {
    content:''; position:absolute; top:-60px; right:-60px;
    width:240px; height:240px; border-radius:50%;
    background:rgba(255,255,255,.05); pointer-events:none;
}
.rp-hero::after {
    content:''; position:absolute; bottom:-40px; left:35%;
    width:180px; height:180px; border-radius:50%;
    background:rgba(37,99,235,.08); pointer-events:none;
}
.rp-hero-left { position:relative; z-index:1; }
.rp-hero-title { font-size:clamp(1rem,2.5vw,1.4rem); font-weight:800; color:#fff; margin:0 0 6px; }
.rp-hero-sub   { font-size:12px; color:rgba(255,255,255,.5); margin:0; }
.rp-hero-right {
    position:relative; z-index:1;
    display:flex; gap:10px; flex-wrap:wrap;
}
.rp-hero-stat {
    background:rgba(255,255,255,.08); border:1px solid rgba(255,255,255,.12);
    border-radius:14px; padding:10px 18px; text-align:center;
}
.rp-hero-stat-val { font-size:18px; font-weight:900; color:#fff; line-height:1; }
.rp-hero-stat-lbl { font-size:10px; color:rgba(255,255,255,.5); font-weight:600; margin-top:3px; text-transform:uppercase; letter-spacing:.5px; }

/* ── SECTION TITLE ── */
.rp-section-lbl {
    font-size:11px; font-weight:800; text-transform:uppercase;
    letter-spacing:.8px; color:#94a3b8;
    margin-bottom:16px;
    display:flex; align-items:center; gap:8px;
}
.rp-section-lbl::after { content:''; flex:1; height:1px; background:#f1f5f9; }

/* ── RAPPORT CARD ── */
.rp-grid {
    display:grid;
    grid-template-columns: repeat(3, 1fr);
    gap:18px;
    margin-bottom:28px;
}
.rp-card {
    background:#fff; border:1px solid #e8edf5;
    border-radius:20px; overflow:hidden;
    box-shadow:0 2px 16px rgba(11,15,26,.05);
    transition:transform .22s, box-shadow .22s;
    display:flex; flex-direction:column;
}
.rp-card:hover { transform:translateY(-5px); box-shadow:0 12px 36px rgba(11,15,26,.1); }

.rp-card-top {
    padding:28px 24px 20px;
    display:flex; flex-direction:column; align-items:center;
    text-align:center; flex:1;
    position:relative; overflow:hidden;
}
.rp-card-top::before {
    content:''; position:absolute; top:-30px; right:-30px;
    width:100px; height:100px; border-radius:50%;
    opacity:.06;
}

/* Couleurs par type */
.rpc-ventes .rp-card-top  { background:linear-gradient(160deg,#eff6ff 0%,#fff 60%); }
.rpc-ventes .rp-card-top::before { background:#2563eb; }
.rpc-stock  .rp-card-top  { background:linear-gradient(160deg,#fffbeb 0%,#fff 60%); }
.rpc-stock  .rp-card-top::before { background:#d97706; }
.rpc-pdf    .rp-card-top  { background:linear-gradient(160deg,#fef2f2 0%,#fff 60%); }
.rpc-pdf    .rp-card-top::before { background:#dc2626; }
.rpc-ca     .rp-card-top  { background:linear-gradient(160deg,#f0fdf4 0%,#fff 60%); }
.rpc-ca     .rp-card-top::before { background:#059669; }
.rpc-mvt    .rp-card-top  { background:linear-gradient(160deg,#faf5ff 0%,#fff 60%); }
.rpc-mvt    .rp-card-top::before { background:#7c3aed; }
.rpc-caisses .rp-card-top { background:linear-gradient(160deg,#fff1f2 0%,#fff 60%); }
.rpc-caisses .rp-card-top::before { background:#e11d48; }

/* Icône cercle */
.rp-ico-wrap {
    width:64px; height:64px; border-radius:20px;
    display:flex; align-items:center; justify-content:center;
    font-size:24px; margin-bottom:16px;
    box-shadow:0 4px 16px rgba(0,0,0,.1);
}
.rpc-ventes  .rp-ico-wrap { background:linear-gradient(135deg,#1e3a8a,#2563eb); color:#fff; }
.rpc-stock   .rp-ico-wrap { background:linear-gradient(135deg,#92400e,#d97706); color:#fff; }
.rpc-pdf     .rp-ico-wrap { background:linear-gradient(135deg,#991b1b,#dc2626); color:#fff; }
.rpc-ca      .rp-ico-wrap { background:linear-gradient(135deg,#065f46,#059669); color:#fff; }
.rpc-mvt     .rp-ico-wrap { background:linear-gradient(135deg,#4c1d95,#7c3aed); color:#fff; }
.rpc-caisses .rp-ico-wrap { background:linear-gradient(135deg,#9f1239,#e11d48); color:#fff; }

.rp-card-name { font-size:15px; font-weight:800; color:#0f172a; margin-bottom:8px; }
.rp-card-desc { font-size:12px; color:#94a3b8; line-height:1.5; flex:1; }

/* Badge "Export" */
.rp-badge-export {
    display:inline-flex; align-items:center; gap:4px;
    background:#fef2f2; color:#dc2626; border:1px solid #fecaca;
    border-radius:20px; padding:3px 10px;
    font-size:10px; font-weight:800;
    margin-top:10px;
}

/* Footer du card */
.rp-card-foot {
    padding:16px 20px; border-top:1px solid #f1f5f9;
    background:#fafbff;
}
.rp-card-btn {
    display:flex; align-items:center; justify-content:center; gap:8px;
    width:100%; padding:11px; border-radius:12px; border:none;
    font-size:13px; font-weight:800; cursor:pointer;
    transition:all .2s; text-decoration:none;
}
.rp-card-btn:hover { transform:translateY(-2px); text-decoration:none; }

.rpc-ventes  .rp-card-btn { background:linear-gradient(135deg,#1e3a8a,#2563eb); color:#fff !important; box-shadow:0 4px 14px rgba(37,99,235,.3); }
.rpc-stock   .rp-card-btn { background:linear-gradient(135deg,#92400e,#d97706); color:#fff !important; box-shadow:0 4px 14px rgba(217,119,6,.3); }
.rpc-pdf     .rp-card-btn { background:linear-gradient(135deg,#991b1b,#dc2626); color:#fff !important; box-shadow:0 4px 14px rgba(220,38,38,.3); }
.rpc-ca      .rp-card-btn { background:linear-gradient(135deg,#065f46,#059669); color:#fff !important; box-shadow:0 4px 14px rgba(5,150,105,.3); }
.rpc-mvt     .rp-card-btn { background:linear-gradient(135deg,#4c1d95,#7c3aed); color:#fff !important; box-shadow:0 4px 14px rgba(124,58,237,.3); }
.rpc-caisses .rp-card-btn { background:linear-gradient(135deg,#9f1239,#e11d48); color:#fff !important; box-shadow:0 4px 14px rgba(225,29,72,.3); }

.rpc-ventes  .rp-card-btn:hover { box-shadow:0 8px 22px rgba(37,99,235,.4); }
.rpc-stock   .rp-card-btn:hover { box-shadow:0 8px 22px rgba(217,119,6,.4); }
.rpc-pdf     .rp-card-btn:hover { box-shadow:0 8px 22px rgba(220,38,38,.4); }
.rpc-ca      .rp-card-btn:hover { box-shadow:0 8px 22px rgba(5,150,105,.4); }
.rpc-mvt     .rp-card-btn:hover { box-shadow:0 8px 22px rgba(124,58,237,.4); }
.rpc-caisses .rp-card-btn:hover { box-shadow:0 8px 22px rgba(225,29,72,.4); }

/* ── EXPORTS ROW ── */
.rp-export-grid {
    display:grid;
    grid-template-columns: repeat(4, 1fr);
    gap:14px;
}
.rp-export-item {
    background:#fff; border:1px solid #e8edf5;
    border-radius:16px; padding:18px 16px;
    display:flex; align-items:center; gap:14px;
    text-decoration:none; transition:all .2s;
    box-shadow:0 2px 10px rgba(11,15,26,.04);
}
.rp-export-item:hover {
    transform:translateY(-3px);
    box-shadow:0 8px 24px rgba(11,15,26,.1);
    text-decoration:none;
}
.rp-exp-ico {
    width:42px; height:42px; border-radius:12px;
    display:flex; align-items:center; justify-content:center;
    font-size:16px; flex-shrink:0;
}
.rp-exp-name { font-size:13px; font-weight:800; color:#0f172a; margin-bottom:2px; }
.rp-exp-fmt  { font-size:11px; color:#94a3b8; font-weight:600; }

/* ── RESPONSIVE ── */
@media(max-width:992px) {
    .rp-grid        { grid-template-columns: repeat(2, 1fr); }
    .rp-export-grid { grid-template-columns: repeat(2, 1fr); }
}
@media(max-width:600px) {
    .rp-hero        { flex-direction:column; align-items:flex-start; }
    .rp-grid        { grid-template-columns: 1fr; }
    .rp-export-grid { grid-template-columns: 1fr 1fr; }
}

/* ── ANIM ── */
@keyframes fadeUp { from{opacity:0;transform:translateY(14px)} to{opacity:1;transform:translateY(0)} }
.rp-hero         { animation:fadeUp .4s ease both; }
.rp-grid         { animation:fadeUp .4s .1s ease both; }
.rp-export-grid  { animation:fadeUp .4s .2s ease both; }
</style>
@endpush

@section('content')
<div class="rp-wrap">

    {{-- ── HERO ── --}}
    <div class="rp-hero">
        <div class="rp-hero-left">
            <h1 class="rp-hero-title">
                <i class="fas fa-chart-bar mr-2" style="opacity:.7"></i>
                Rapports &amp; Statistiques
            </h1>
            <p class="rp-hero-sub">Analysez les performances, exportez les données</p>
        </div>
        <div class="rp-hero-right">
            <div class="rp-hero-stat">
                <div class="rp-hero-stat-val">{{ \App\Models\Facture::whereDate('created_at', today())->count() }}</div>
                <div class="rp-hero-stat-lbl">Ventes aujourd'hui</div>
            </div>
            <div class="rp-hero-stat">
                <div class="rp-hero-stat-val">
                    {{ number_format(\App\Models\Facture::where('statut','payee')->whereDate('created_at', today())->sum('total') / 1000, 0) }}K
                </div>
                <div class="rp-hero-stat-lbl">CA du jour (FCFA)</div>
            </div>
        </div>
    </div>

    {{-- ── RAPPORTS ANALYTIQUES ── --}}
    <div class="rp-section-lbl">
        <i class="fas fa-chart-line" style="font-size:10px"></i> Rapports analytiques
    </div>

    <div class="rp-grid">

        {{-- Ventes --}}
        <div class="rp-card rpc-ventes">
            <div class="rp-card-top">
                <div class="rp-ico-wrap"><i class="fas fa-chart-line"></i></div>
                <div class="rp-card-name">Rapport des Ventes</div>
                <div class="rp-card-desc">
                    Analyse du chiffre d'affaires, évolution journalière, top produits vendus et performances par caissier.
                </div>
            </div>
            <div class="rp-card-foot">
                <a href="{{ route('rapports.ventes') }}" class="rp-card-btn">
                    <i class="fas fa-eye"></i> Voir le rapport
                </a>
            </div>
        </div>

        {{-- Stock --}}
        <div class="rp-card rpc-stock">
            <div class="rp-card-top">
                <div class="rp-ico-wrap"><i class="fas fa-warehouse"></i></div>
                <div class="rp-card-name">Rapport de Stock</div>
                <div class="rp-card-desc">
                    État actuel des stocks, valorisation par catégorie, produits en rupture et alertes de réapprovisionnement.
                </div>
            </div>
            <div class="rp-card-foot">
                <a href="{{ route('rapports.stock') }}" class="rp-card-btn">
                    <i class="fas fa-eye"></i> Voir le rapport
                </a>
            </div>
        </div>

         {{-- Export PDF Stock --}}
        <div class="rp-card rpc-pdf">
            <div class="rp-card-top">
                <div class="rp-ico-wrap"><i class="fas fa-file-pdf"></i></div>
                <div class="rp-card-name">Export Stock PDF</div>
                <div class="rp-card-desc">
                    Générez et téléchargez instantanément l'état complet du stock au format PDF.
                </div>
                <span class="rp-badge-export">
                    <i class="fas fa-download" style="font-size:9px"></i> Téléchargement direct
                </span>
            </div>
            <div class="rp-card-foot">
                <a href="{{ route('rapports.export-pdf') }}?type=stock"
                   class="rp-card-btn" target="_blank">
                    <i class="fas fa-download"></i> Télécharger PDF
                </a>
            </div>
        </div>


    </div>

    

</div>
@endsection