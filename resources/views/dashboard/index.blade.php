@extends('layouts.app')

@section('title', 'Tableau de bord')
@section('page-title', 'Tableau de bord')

@section('breadcrumb')
    <li class="breadcrumb-item active">Dashboard</li>
@endsection

@push('styles')
    <style>
        :root {
            --dark:   #0f172a;
            --border: #e2e8f0;
            --sh:     0 4px 24px rgba(15,52,96,.08);
            --r:      16px;
        }

        /* ══ KPI CARDS ══ */
        .kpi-card {
            border-radius: var(--r);
            padding: 22px 20px;
            position: relative; overflow: hidden;
            transition: transform .2s, box-shadow .2s;
            text-decoration: none !important;
            display: block;
            margin-bottom: 16px;
        }
        .kpi-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 16px 40px rgba(0,0,0,.15);
            text-decoration: none !important;
        }
        .kpi-card::after {
            content:''; position:absolute;
            bottom:-30px; right:-30px;
            width:110px; height:110px; border-radius:50%;
            background:rgba(255,255,255,.1);
        }
        .kpi-card::before {
            content:''; position:absolute;
            top:-20px; right:30px;
            width:70px; height:70px; border-radius:50%;
            background:rgba(255,255,255,.07);
        }
        .kpi-ico {
            width:48px; height:48px; border-radius:14px;
            background:rgba(255,255,255,.2);
            display:flex; align-items:center; justify-content:center;
            font-size:20px; color:#fff; margin-bottom:16px;
            position:relative; z-index:1;
        }
        .kpi-val {
            font-size:clamp(1.3rem,3vw,1.8rem);
            font-weight:900; color:#fff; line-height:1;
            margin-bottom:4px; position:relative; z-index:1;
        }
        .kpi-val sup { font-size:.5em; font-weight:600; opacity:.8; }
        .kpi-lbl {
            font-size:12px; font-weight:600;
            color:rgba(255,255,255,.75);
            text-transform:uppercase; letter-spacing:.6px;
            position:relative; z-index:1;
        }
        .kpi-foot {
            margin-top:14px; padding-top:12px;
            border-top:1px solid rgba(255,255,255,.15);
            font-size:11px; color:rgba(255,255,255,.65);
            display:flex; align-items:center; gap:6px;
            position:relative; z-index:1;
        }

        .kpi-blue   { background:linear-gradient(140deg,#1d4ed8,#3b82f6); box-shadow:0 8px 28px rgba(59,130,246,.35); }
        .kpi-green  { background:linear-gradient(140deg,#065f46,#10b981); box-shadow:0 8px 28px rgba(16,185,129,.35); }
        .kpi-orange { background:linear-gradient(140deg,#92400e,#f59e0b); box-shadow:0 8px 28px rgba(245,158,11,.35); }
        .kpi-red    { background:linear-gradient(140deg,#7f1d1d,#ef4444); box-shadow:0 8px 28px rgba(239,68,68,.35); }

        /* ══ STAT BOXES ══ */
        .stat-box {
            background:#fff; border:1px solid var(--border);
            border-radius:var(--r); box-shadow:var(--sh);
            padding:18px 20px;
            display:flex; align-items:center; gap:16px;
            transition:transform .2s; margin-bottom:16px;
        }
        .stat-box:hover { transform:translateY(-3px); }
        .stat-box-ico {
            width:50px; height:50px; border-radius:14px;
            display:flex; align-items:center; justify-content:center;
            font-size:20px; flex-shrink:0;
        }
        .stat-box-ico.green  { background:#d1fae5; color:#10b981; }
        .stat-box-ico.yellow { background:#fef3c7; color:#f59e0b; }
        .stat-box-ico.red    { background:#fee2e2; color:#ef4444; }
        .stat-box-val { font-size:22px; font-weight:900; color:var(--dark); line-height:1; }
        .stat-box-lbl { font-size:12px; color:#64748b; font-weight:600; margin-top:3px;
                        text-transform:uppercase; letter-spacing:.5px; }

        /* ══ PANEL CARDS ══ */
        .pd-card {
            background:#fff; border:1px solid var(--border);
            border-radius:var(--r); box-shadow:var(--sh);
            overflow:hidden; margin-bottom:20px;
            height:100%;
        }
        .pd-card-head {
            padding:16px 20px; border-bottom:1px solid var(--border);
            display:flex; align-items:center; justify-content:space-between;
        }
        .pd-card-title {
            display:flex; align-items:center; gap:10px;
            font-size:12px; font-weight:800;
            text-transform:uppercase; letter-spacing:.7px;
            color:var(--dark); margin:0;
        }
        .pd-card-ico {
            width:30px; height:30px; border-radius:8px;
            display:flex; align-items:center; justify-content:center;
            font-size:12px; color:#fff;
        }
        .pd-card-action {
            font-size:11px; font-weight:700;
            color:#3b82f6; text-decoration:none;
            padding:5px 12px; border-radius:8px;
            border:1px solid #bfdbfe;
            background:#eff6ff;
            transition:all .15s;
        }
        .pd-card-action:hover { background:#dbeafe; text-decoration:none; color:#1d4ed8; }

        /* ══ CHART WRAP ══ */
        .chart-wrap { padding:20px; }

        /* ══ TOP PRODUITS ══ */
        .top-item {
            display:flex; align-items:center; gap:12px;
            padding:12px 20px; border-bottom:1px solid #f1f5f9;
            transition:background .12s;
        }
        .top-item:last-child { border-bottom:none; }
        .top-item:hover { background:#fafbff; }
        .top-rank {
            width:26px; height:26px; border-radius:8px;
            display:flex; align-items:center; justify-content:center;
            font-size:11px; font-weight:900; flex-shrink:0;
        }
        .rank-1 { background:#fef3c7; color:#d97706; }
        .rank-2 { background:#f1f5f9; color:#475569; }
        .rank-3 { background:#fef3c7; color:#b45309; }
        .rank-n { background:#f1f5f9; color:#94a3b8; }
        .top-name { flex:1; font-size:13px; font-weight:600; color:#374151; min-width:0;
                    white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
        .top-qty {
            font-size:11px; font-weight:700;
            background:#e0f2fe; color:#0369a1;
            padding:3px 9px; border-radius:20px;
        }

        /* ══ TABLE ══ */
        .dash-table { font-size:12px; }
        .dash-table th {
            padding:11px 16px; color:#64748b; font-size:10px;
            text-transform:uppercase; letter-spacing:.5px; font-weight:700;
            background:#f8fafc; border-bottom:1px solid var(--border);
        }
        .dash-table td {
            padding:11px 16px; vertical-align:middle;
            border-bottom:1px solid #f1f5f9;
        }
        .dash-table tr:last-child td { border-bottom:none; }
        .dash-table tr:hover td { background:#fafbff; }

        /* ══ STATUS BADGES ══ */
        .s-badge {
            display:inline-flex; align-items:center; gap:4px;
            padding:3px 9px; border-radius:20px;
            font-size:10px; font-weight:700;
        }
        .s-payee   { background:#d1fae5; color:#059669; }
        .s-cours   { background:#fef3c7; color:#d97706; }
        .s-annulee { background:#fee2e2; color:#dc2626; }
        .s-faible  { background:#fef3c7; color:#d97706; }
        .s-rupture { background:#fee2e2; color:#dc2626; }

        /* ══ ALERTE PRODUITS ══ */
        .alerte-item {
            display:flex; align-items:center; gap:12px;
            padding:11px 20px; border-bottom:1px solid #f1f5f9;
            transition:background .12s;
        }
        .alerte-item:last-child { border-bottom:none; }
        .alerte-item:hover { background:#fafbff; }
        .alerte-ico {
            width:32px; height:32px; border-radius:9px;
            display:flex; align-items:center; justify-content:center;
            font-size:13px; flex-shrink:0;
        }
        .alerte-ico.faible  { background:#fef3c7; color:#d97706; }
        .alerte-ico.rupture { background:#fee2e2; color:#ef4444; }
        .alerte-name { flex:1; font-size:12px; font-weight:600; color:#374151; min-width:0;
                    white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
        .alerte-stock { font-size:11px; color:#94a3b8; font-weight:600; }

        /* ══ GREETING ══ */
        .greeting-bar {
             background: linear-gradient(135deg, rgba(15,23,42,0.82) 0%, rgba(15,52,96,0.80) 60%, rgba(15,48,96,0.82) 100%),
                url("{{ asset('image/caisse1.jpg') }}") center/cover no-repeat;
            border-radius:var(--r); padding:24px 28px;
            display:flex; align-items:center; justify-content:space-between;
            margin-bottom:22px; overflow:hidden; position:relative;
            box-shadow:0 8px 32px rgba(15,52,96,.2);
        }
        .greeting-bar::before {
            content:''; position:absolute;
            top:-40px; right:-40px; width:200px; height:200px; border-radius:50%;
            background:radial-gradient(circle,rgba(59,130,246,.15),transparent 65%);
        }
        .greeting-title {
            font-size:clamp(1rem,2.5vw,1.3rem);
            font-weight:800; color:#fff; margin:0 0 4px;
        }
        .greeting-sub { font-size:13px; color:rgba(255,255,255,.6); margin:0; }
        .greeting-date {
            text-align:right; flex-shrink:0;
        }
        .greeting-date-val {
            font-size:13px; font-weight:700; color:rgba(255,255,255,.8);
            background:rgba(255,255,255,.1); border:1px solid rgba(255,255,255,.15);
            border-radius:10px; padding:6px 14px; display:inline-block;
        }
        .greeting-date-time {
            font-size:11px; color:rgba(255,255,255,.45); margin-top:5px;
            text-align:right;
        }

        /* ══ RESPONSIVE ══ */
        @media(max-width:767px) {
            .kpi-val { font-size:1.3rem; }
            .greeting-bar { flex-direction:column; gap:12px; text-align:center; }
            .greeting-date { text-align:center; }
        }
    </style>
@endpush

@section('content')

    {{-- ════ GREETING BAR ════ --}}
   <div class="greeting-bar" style="background: linear-gradient(135deg, rgba(15,23,42,0.82) 0%, rgba(30,58,95,0.80) 60%, rgba(15,52,96,0.82) 100%), url('{{ asset('image/caisse1.jpg') }}') center/cover no-repeat;">
        <div style="position:relative; z-index:1">
            <h4 class="greeting-title">
                Bonjour, {{ auth()->user()->prenom }} {{ auth()->user()->nom }} 
            </h4>
            <p class="greeting-sub">
                Voici un résumé de votre activité du jour
            </p>
        </div>
        <div class="greeting-date" style="position:relative; z-index:1">
            <div class="greeting-date-val" id="liveDate"></div>
            <div class="greeting-date-time" id="liveTime"></div>
        </div>
    </div>

     @if(auth()->user()->isAdmin())

        {{-- DASHBOARD ADMIN COMPLET --}}
        @include('dashboard.admin')

    @else

        {{-- DASHBOARD CAISSIER / SUPERVISEUR --}}
        @include('dashboard.simple')

    @endif
    @push('scripts')
        <script>
            /* ══ DATE/HEURE LIVE ══ */
            function updateClock() {
                const now = new Date();
                const opts = { weekday:'long', day:'numeric', month:'long', year:'numeric' };
                document.getElementById('liveDate').textContent =
                    now.toLocaleDateString('fr-FR', opts);
                document.getElementById('liveTime').textContent =
                    now.toLocaleTimeString('fr-FR', { hour:'2-digit', minute:'2-digit' });
            }
            updateClock();
            setInterval(updateClock, 30000);

            /* ══ GRAPHIQUE ══ */
            const caData = @json($caParJour);
            const labels = caData.map(d => d.date);
            const values = caData.map(d => d.total);
            const maxVal = Math.max(...values);

            new Chart(document.getElementById('caChart'), {
                type: 'bar',
                data: {
                    labels,
                    datasets: [{
                        label: 'CA (FCFA)',
                        data: values,
                        backgroundColor: values.map(v =>
                            v === maxVal
                                ? 'rgba(59,130,246,1)'
                                : 'rgba(59,130,246,.45)'
                        ),
                        borderColor: 'rgba(59,130,246,1)',
                        borderWidth: 0,
                        borderRadius: 8,
                        borderSkipped: false,
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#0f172a',
                            titleColor: '#94a3b8',
                            bodyColor: '#e2e8f0',
                            padding: 12,
                            borderColor: '#1e293b',
                            borderWidth: 1,
                            cornerRadius: 10,
                            callbacks: {
                                label: ctx =>
                                    '  ' + new Intl.NumberFormat('fr-FR').format(ctx.raw) + ' FCFA'
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: { display: false },
                            ticks: { color: '#94a3b8', font: { size: 11, weight: '600' } },
                            border: { display: false }
                        },
                        y: {
                            beginAtZero: true,
                            grid: { color: '#f1f5f9', drawBorder: false },
                            border: { display: false, dash: [4, 4] },
                            ticks: {
                                color: '#94a3b8',
                                font: { size: 11 },
                                callback: v => new Intl.NumberFormat('fr-FR', {
                                    notation: 'compact', compactDisplay: 'short'
                                }).format(v)
                            }
                        }
                    }
                }
            });
        </script>
    @endpush
    

@endsection

