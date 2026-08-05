{{-- resources/views/clients/show.blade.php --}}
@extends('layouts.app')
@section('title', $client->nom_complet)
@section('page-title', 'Fiche Client')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('clients.index') }}">Clients</a></li>
    <li class="breadcrumb-item active">{{ $client->nom_complet }}</li>
@endsection

@push('styles')
<style>
@import url('https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700;800&family=Syne:wght@700;800&display=swap');
.cs-wrap { font-family:'DM Sans',sans-serif; }

.cs-hero { border-radius:20px; padding:28px 32px; margin-bottom:22px; position:relative; overflow:hidden; box-shadow:0 10px 40px rgba(15,23,42,.18); }
.cs-hero-bg { position:absolute; inset:0; z-index:0; background:linear-gradient(135deg,#0f172a 0%,#3730a3 60%,#1e1b4b 100%); }
.cs-hero-orb1 { position:absolute; top:-60px; right:-60px; width:240px; height:240px; border-radius:50%; background:rgba(99,102,241,.12); z-index:0; }
.cs-hero-orb2 { position:absolute; bottom:-40px; left:30%; width:180px; height:180px; border-radius:50%; background:rgba(255,255,255,.04); z-index:0; }
.cs-hero-content { position:relative; z-index:1; display:flex; align-items:center; gap:22px; flex-wrap:wrap; }
.cs-hero-avatar { width:72px; height:72px; border-radius:20px; flex-shrink:0; display:flex; align-items:center; justify-content:center; font-family:'Syne',sans-serif; font-size:24px; font-weight:800; color:#fff; border:3px solid rgba(255,255,255,.2); }
.cs-hero-name { font-family:'Syne',sans-serif; font-size:clamp(1.1rem,3vw,1.5rem); font-weight:800; color:#fff; margin:0 0 6px; }
.cs-hero-meta { display:flex; flex-wrap:wrap; gap:14px; align-items:center; }
.cs-meta-item { display:flex; align-items:center; gap:5px; font-size:12px; color:rgba(255,255,255,.55); }
.cs-hero-actions { display:flex; gap:8px; align-items:center; margin-left:auto; }
.cs-action-btn { display:inline-flex; align-items:center; gap:7px; border-radius:11px; padding:9px 18px; font-size:12px; font-weight:700; cursor:pointer; transition:.2s; text-decoration:none; border:none; }
.cs-action-btn:hover { transform:translateY(-2px); text-decoration:none; }
.cab-edit { background:linear-gradient(135deg,#d97706,#f59e0b); color:#fff; box-shadow:0 4px 12px rgba(217,119,6,.3); }
.cab-back { background:rgba(255,255,255,.1); border:1px solid rgba(255,255,255,.15); color:rgba(255,255,255,.8); }
.cab-back:hover { background:rgba(255,255,255,.18); color:#fff; }
.cab-del  { background:rgba(239,68,68,.15); border:1px solid rgba(239,68,68,.3); color:#fca5a5; }
.cab-del:hover { background:rgba(239,68,68,.25); color:#fff; }

.cs-type-badge { display:inline-flex; align-items:center; gap:5px; padding:4px 12px; border-radius:20px; font-size:10px; font-weight:800; }
.ctb-part { background:rgba(99,102,241,.2); border:1px solid rgba(99,102,241,.3); color:#a5b4fc; }
.ctb-ent  { background:rgba(14,165,233,.2); border:1px solid rgba(14,165,233,.3); color:#7dd3fc; }
.cs-statut-badge { display:inline-flex; align-items:center; gap:5px; padding:4px 12px; border-radius:20px; font-size:10px; font-weight:800; }
.csb-actif   { background:rgba(16,185,129,.2); border:1px solid rgba(16,185,129,.3); color:#34d399; }
.csb-inactif { background:rgba(148,163,184,.15); border:1px solid rgba(148,163,184,.2); color:#94a3b8; }

.cs-kpi-row { display:grid; grid-template-columns:repeat(4,1fr); gap:14px; margin-bottom:22px; }
@media(max-width:900px){ .cs-kpi-row { grid-template-columns:repeat(2,1fr); } }
.cs-kpi { background:#fff; border:1px solid #e8edf5; border-radius:16px; padding:18px; box-shadow:0 2px 12px rgba(11,15,26,.05); }
.cs-kpi-ico { width:40px; height:40px; border-radius:12px; display:flex; align-items:center; justify-content:center; font-size:15px; margin-bottom:10px; }
.cs-kpi-val { font-family:'Syne',sans-serif; font-size:1.4rem; font-weight:800; line-height:1; margin-bottom:3px; }
.cs-kpi-lbl { font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:.5px; opacity:.6; }

.cs-body { display:grid; grid-template-columns:1fr 290px; gap:18px; align-items:start; }
@media(max-width:1024px){ .cs-body { grid-template-columns:1fr; } }

.cs-card { background:#fff; border:1px solid #e8edf5; border-radius:16px; overflow:hidden; box-shadow:0 2px 12px rgba(11,15,26,.04); margin-bottom:14px; }
.cs-card-hd { padding:13px 20px; border-bottom:1px solid #f1f5f9; display:flex; align-items:center; gap:10px; background:#fafbff; }
.cs-card-ico { width:28px; height:28px; border-radius:8px; display:flex; align-items:center; justify-content:center; font-size:12px; color:#fff; flex-shrink:0; }
.cs-card-hd h6 { font-size:10px; font-weight:800; text-transform:uppercase; letter-spacing:.6px; color:#374151; margin:0; }
.cs-card-bd { padding:18px 20px; }

.info-grid { display:grid; grid-template-columns:1fr 1fr; gap:14px; }
@media(max-width:600px){ .info-grid { grid-template-columns:1fr; } }
.info-lbl { font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:.5px; color:#9ca3af; margin-bottom:5px; }
.info-val { font-size:13px; font-weight:600; color:#0f172a; }
.info-val a { color:#6366f1; text-decoration:none; font-weight:700; }
.info-val a:hover { text-decoration:underline; }

.cs-table { width:100%; font-size:12px; border-collapse:collapse; }
.cs-table th { padding:9px 14px; font-size:9px; font-weight:800; text-transform:uppercase; letter-spacing:.5px; color:#64748b; border-bottom:1px solid #f1f5f9; background:#f8fafc; }
.cs-table td { padding:11px 14px; vertical-align:middle; border-bottom:1px solid #f9fafb; }
.cs-table tr:last-child td { border-bottom:none; }
.cs-table tr:hover td { background:#fafbff; }

.cs-badge { display:inline-flex; align-items:center; gap:4px; padding:3px 8px; border-radius:20px; font-size:10px; font-weight:700; }
.csb-paye   { background:#d1fae5; color:#065f46; }
.csb-credit { background:#fef3c7; color:#92400e; }
.csb-annule { background:#fee2e2; color:#7f1d1d; }

.cs-info-pill { display:flex; align-items:center; gap:10px; padding:10px 0; border-bottom:1px solid #f8fafc; }
.cs-info-pill:last-child { border-bottom:none; }
.cip-ico { width:32px; height:32px; border-radius:9px; display:flex; align-items:center; justify-content:center; font-size:12px; flex-shrink:0; }
.cip-lbl { font-size:10px; color:#9ca3af; font-weight:600; }
.cip-val { font-size:12px; font-weight:700; color:#0f172a; }

.cs-empty { padding:30px 20px; text-align:center; color:#94a3b8; }
.cs-empty i { font-size:30px; display:block; margin-bottom:8px; opacity:.3; }

@keyframes fadeUp { from{opacity:0;transform:translateY(12px)} to{opacity:1;transform:translateY(0)} }
.cs-hero     { animation:fadeUp .35s ease both; }
.cs-kpi-row  { animation:fadeUp .35s .07s ease both; }
.cs-body     { animation:fadeUp .35s .14s ease both; }
</style>
@endpush

@section('content')
@php
    $colors = ['#6366f1','#059669','#d97706','#0ea5e9','#dc2626','#7c3aed','#16a34a'];
    $color  = $colors[$client->id % count($colors)];
@endphp
<div class="cs-wrap">

    <!-- HERO -->
    <div class="cs-hero">
        <div class="cs-hero-bg"></div>
        <div class="cs-hero-orb1"></div>
        <div class="cs-hero-orb2"></div>
        <div class="cs-hero-content">
            <div class="cs-hero-avatar" style="background:{{ $color }}">{{ $client->initiales }}</div>
            <div style="flex:1; min-width:0">
                <h1 class="cs-hero-name">{{ $client->nom_complet }}</h1>
                <div class="cs-hero-meta">
                    <span class="cs-type-badge {{ $client->type==='entreprise'?'ctb-ent':'ctb-part' }}">
                        {{ $client->type==='entreprise'?'🏢':'👤' }} {{ $client->type_label }}
                    </span>
                    @if($client->telephone)
                        <span class="cs-meta-item"><i class="fas fa-phone"></i> {{ $client->telephone }}</span>
                    @endif
                    @if($client->ville)
                        <span class="cs-meta-item"><i class="fas fa-map-marker-alt"></i> {{ $client->ville }}</span>
                    @endif
                    <span class="cs-statut-badge {{ $client->actif?'csb-actif':'csb-inactif' }}">
                        <span style="width:5px;height:5px;border-radius:50%;background:currentColor"></span>
                        {{ $client->actif?'Actif':'Inactif' }}
                    </span>
                </div>
            </div>
            <div class="cs-hero-actions">
                <a href="{{ route('clients.index') }}" class="cs-action-btn cab-back"><i class="fas fa-arrow-left"></i></a>
                <a href="{{ route('clients.edit', $client) }}" class="cs-action-btn cab-edit"><i class="fas fa-edit"></i> Modifier</a>
                <button onclick="confirmDelete('del-cs-{{ $client->id }}')" class="cs-action-btn cab-del"><i class="fas fa-trash"></i></button>
                <form id="del-cs-{{ $client->id }}" action="{{ route('clients.destroy', $client) }}" method="POST" class="d-none">
                    @csrf @method('DELETE')
                </form>
            </div>
        </div>
    </div>

    <!-- KPI -->
    <div class="cs-kpi-row">
        <div class="cs-kpi">
            <div class="cs-kpi-ico" style="background:linear-gradient(135deg,#ede9fe,#ddd6fe); color:#7c3aed"><i class="fas fa-receipt"></i></div>
            <div class="cs-kpi-val" style="color:#1e1b4b">{{ $stats['nb_factures'] }}</div>
            <div class="cs-kpi-lbl" style="color:#1e1b4b">Factures</div>
        </div>
        <div class="cs-kpi">
            <div class="cs-kpi-ico" style="background:linear-gradient(135deg,#d1fae5,#a7f3d0); color:#059669"><i class="fas fa-coins"></i></div>
            <div class="cs-kpi-val" style="color:#065f46; font-size:1.1rem">
                {{ $stats['total_depense'] >= 1000000 ? number_format($stats['total_depense']/1000000,1,',',' ').'M' : number_format($stats['total_depense']/1000,0,',',' ').'K' }}
            </div>
            <div class="cs-kpi-lbl" style="color:#065f46">Total dépensé (FCFA)</div>
        </div>
        <div class="cs-kpi">
            <div class="cs-kpi-ico" style="background:linear-gradient(135deg,#fef3c7,#fde68a); color:#d97706"><i class="fas fa-hand-holding-usd"></i></div>
            <div class="cs-kpi-val" style="color:#92400e; font-size:{{ $stats['creances'] > 0 ? '1.1rem' : '1.5rem' }}">
                {{ $stats['creances'] > 0 ? number_format($stats['creances'],0,',',' ').' F' : '0' }}
            </div>
            <div class="cs-kpi-lbl" style="color:#92400e">Créances (FCFA)</div>
        </div>
        <div class="cs-kpi">
            <div class="cs-kpi-ico" style="background:linear-gradient(135deg,#e0f2fe,#bae6fd); color:#0369a1"><i class="fas fa-calendar-check"></i></div>
            <div class="cs-kpi-val" style="color:#0c4a6e; font-size:{{ $stats['derniere_visite'] ? '1rem' : '1.5rem' }}">
                {{ $stats['derniere_visite'] ? \Carbon\Carbon::parse($stats['derniere_visite'])->format('d/m/Y') : '—' }}
            </div>
            <div class="cs-kpi-lbl" style="color:#0c4a6e">Dernière visite</div>
        </div>
    </div>

    <!-- BODY -->
    <div class="cs-body">

        <!-- GAUCHE -->
        <div>

            <!-- Infos contact -->
            <div class="cs-card">
                <div class="cs-card-hd">
                    <span class="cs-card-ico" style="background:linear-gradient(135deg,#6366f1,#4f46e5)"><i class="fas fa-user"></i></span>
                    <h6>Informations du client</h6>
                    <a href="{{ route('clients.edit', $client) }}" style="margin-left:auto; font-size:10px; color:#6366f1; font-weight:700; text-decoration:none">
                        <i class="fas fa-edit mr-1"></i>Modifier →
                    </a>
                </div>
                <div class="cs-card-bd">
                    <div class="info-grid">
                        <div><div class="info-lbl">Nom complet</div><div class="info-val">{{ $client->nom_complet }}</div></div>
                        <div><div class="info-lbl">Type</div><div class="info-val">{{ $client->type_label }}</div></div>
                        <div>
                            <div class="info-lbl">Téléphone</div>
                            <div class="info-val">
                                @if($client->telephone)
                                    <a href="tel:{{ $client->telephone }}"><i class="fas fa-phone" style="font-size:10px; margin-right:4px"></i>{{ $client->telephone }}</a>
                                @else —
                                @endif
                            </div>
                        </div>
                        <div>
                            <div class="info-lbl">Email</div>
                            <div class="info-val">
                                @if($client->email)
                                    <a href="mailto:{{ $client->email }}"><i class="fas fa-envelope" style="font-size:10px; margin-right:4px"></i>{{ $client->email }}</a>
                                @else —
                                @endif
                            </div>
                        </div>
                        <div><div class="info-lbl">IFU</div><div class="info-val">{{ $client->ifu ?? '—' }}</div></div>
                        <div><div class="info-lbl">Adresse</div><div class="info-val">{{ $client->adresse ?? '—' }}</div></div>
                    </div>
                </div>
            </div>

            <!-- Factures -->
            <div class="cs-card">
                <div class="cs-card-hd">
                    <span class="cs-card-ico" style="background:linear-gradient(135deg,#7c3aed,#a855f7)"><i class="fas fa-receipt"></i></span>
                    <h6>Historique des factures</h6>
                    <span style="margin-left:auto; background:#ede9fe; color:#7c3aed; padding:2px 10px; border-radius:20px; font-size:11px; font-weight:700">
                        {{ $stats['nb_factures'] }}
                    </span>
                </div>
                @if($factures->count())
                <div class="table-responsive">
                    <table class="cs-table">
                        <thead>
                            <tr>
                                <th>Facture</th>
                                <th>Date</th>
                                <th class="text-right">Total</th>
                                <th>Paiement</th>
                                <th>Statut</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($factures as $f)
                                <tr>
                                    <td style="font-weight:700; color:#6366f1">{{ $f->numero }}</td>
                                    <td style="color:#64748b; font-size:11px; white-space:nowrap">{{ $f->created_at->format('d/m/Y H:i') }}</td>
                                    <td class="text-right" style="font-weight:800">{{ number_format($f->total,0,',',' ') }} F</td>
                                    <td>
                                        <span style="font-size:11px; color:#374151; font-weight:600">{{ $f->mode_paiement_label }}</span>
                                    </td>
                                    <td>
                                        <span class="cs-badge {{ $f->statut==='payee'?'csb-paye':($f->statut==='en_cours'?'csb-credit':'csb-annule') }}">
                                            {{ $f->statut_label }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('factures.show', $f) }}" style="font-size:10px; color:#6366f1; font-weight:700; text-decoration:none">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div style="padding:12px 20px; border-top:1px solid #f1f5f9; background:#fafbff">
                    @include('partials.pagination', ['paginator' => $factures])
                </div>
                @else
                    <div class="cs-empty"><i class="fas fa-receipt"></i><p>Aucune facture pour ce client</p></div>
                @endif
            </div>
        </div>

        <!-- DROITE SIDEBAR -->
        <div>
            <!-- Fiche résumé -->
            <div class="cs-card">
                <div class="cs-card-hd">
                    <span class="cs-card-ico" style="background:linear-gradient(135deg,#6366f1,#4f46e5)"><i class="fas fa-id-card"></i></span>
                    <h6>Fiche rapide</h6>
                </div>
                <div class="cs-card-bd" style="padding:14px 18px">
                    <div class="cs-info-pill">
                        <div class="cip-ico" style="background:#ede9fe; color:#6366f1"><i class="fas fa-user"></i></div>
                        <div><div class="cip-lbl">Client depuis</div><div class="cip-val">{{ $client->created_at->format('d/m/Y') }}</div></div>
                    </div>
                    <div class="cs-info-pill">
                        <div class="cip-ico" style="background:#d1fae5; color:#059669"><i class="fas fa-coins"></i></div>
                        <div>
                            <div class="cip-lbl">Total dépensé</div>
                            <div class="cip-val">{{ number_format($stats['total_depense'],0,',',' ') }} FCFA</div>
                        </div>
                    </div>
                    @if($stats['creances'] > 0)
                    <div class="cs-info-pill">
                        <div class="cip-ico" style="background:#fef3c7; color:#d97706"><i class="fas fa-exclamation-triangle"></i></div>
                        <div>
                            <div class="cip-lbl">Créances en cours</div>
                            <div class="cip-val" style="color:#d97706">{{ number_format($stats['creances'],0,',',' ') }} FCFA</div>
                        </div>
                    </div>
                    @endif
                    <div class="cs-info-pill">
                        <div class="cip-ico" style="background:#ede9fe; color:#7c3aed"><i class="fas fa-receipt"></i></div>
                        <div>
                            <div class="cip-lbl">Nombre de factures</div>
                            <div class="cip-val">{{ $stats['nb_factures'] }} facture(s)</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="cs-card">
                <div class="cs-card-hd">
                    <span class="cs-card-ico" style="background:linear-gradient(135deg,#64748b,#475569)"><i class="fas fa-bolt"></i></span>
                    <h6>Actions</h6>
                </div>
                <div class="cs-card-bd" style="padding:12px 16px; display:flex; flex-direction:column; gap:8px">
                    <a href="{{ route('clients.edit', $client) }}"
                       style="display:flex;align-items:center;gap:8px;padding:10px 14px;border-radius:10px;background:linear-gradient(135deg,#d97706,#f59e0b);color:#fff;text-decoration:none;font-size:12px;font-weight:700">
                        <i class="fas fa-edit"></i> Modifier ce client
                    </a>
                    <form action="{{ route('clients.toggleActif', $client) }}" method="POST">
                        @csrf @method('PATCH')
                        <button type="submit"
                           style="display:flex;align-items:center;gap:8px;padding:10px 14px;border-radius:10px;width:100%;border:1px solid {{ $client->actif ? '#fecaca' : '#d1fae5' }};background:{{ $client->actif ? '#fef2f2' : '#f0fdf4' }};color:{{ $client->actif ? '#dc2626' : '#059669' }};font-size:12px;font-weight:700;cursor:pointer">
                            <i class="fas {{ $client->actif ? 'fa-ban' : 'fa-check-circle' }}"></i>
                            {{ $client->actif ? 'Désactiver' : 'Activer' }} le client
                        </button>
                    </form>
                    <button onclick="confirmDelete('del-cs2-{{ $client->id }}')"
                       style="display:flex;align-items:center;gap:8px;padding:10px 14px;border-radius:10px;background:#fef2f2;border:1px solid #fecaca;color:#dc2626;font-size:12px;font-weight:700;cursor:pointer;width:100%">
                        <i class="fas fa-trash"></i> Supprimer ce client
                    </button>
                    <form id="del-cs2-{{ $client->id }}" action="{{ route('clients.destroy', $client) }}" method="POST" class="d-none">
                        @csrf @method('DELETE')
                    </form>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection