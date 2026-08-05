@extends('layouts.app')

@section('title', 'Factures Crédit')
@section('page-title', 'Factures à Crédit')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('factures.index') }}">Factures</a></li>
    <li class="breadcrumb-item active">Crédits en attente</li>
@endsection

@section('content')

{{-- Total restant --}}
@if($totalCredit > 0)
<div class="alert alert-warning d-flex justify-content-between align-items-center">
    <div>
        <i class="fas fa-exclamation-triangle mr-2"></i>
        <strong>Total restant à encaisser :</strong>
    </div>
    <span class="h4 mb-0 text-danger font-weight-bold">
        {{ number_format($totalCredit, 0, ',', ' ') }} FCFA
    </span>
</div>
@endif

<div class="row mb-3">

    <div class="col-md-4">
        <div class="card border-warning shadow-sm">
            <div class="card-body text-center">
                <small class="text-muted">Total Crédit</small>
                <h4 class="text-warning font-weight-bold">
                    {{ number_format($factures->sum('total'),0,',',' ') }} FCFA
                </h4>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card border-success shadow-sm">
            <div class="card-body text-center">
                <small class="text-muted">Déjà payé</small>
                <h4 class="text-success font-weight-bold">
                    {{ number_format($factures->sum('montant_paye'),0,',',' ') }} FCFA
                </h4>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card border-danger shadow-sm">
            <div class="card-body text-center">
                <small class="text-muted">Reste à encaisser</small>
                <h4 class="text-danger font-weight-bold">
                    {{ number_format($totalCredit,0,',',' ') }} FCFA
                </h4>
            </div>
        </div>
    </div>

</div>

<div class="card">
    <div class="card-header bg-warning text-white d-flex justify-content-between align-items-center">
        <h3 class="card-title mb-0">
            <i class="fas fa-clock mr-2"></i> Factures crédit en attente
            <span class="badge badge-light ml-2">{{ $factures->total() }}</span>
        </h3>
        <a href="{{ route('factures.index') }}" class="btn btn-sm btn-light">
            <i class="fas fa-list mr-1"></i> Toutes les factures
        </a>
    </div>

    <div class="card-body p-0">
        <table class="table table-hover table-striped mb-0">
            <thead class="bg-light">
                <tr>
                    <th>N° Facture</th>
                    <th>Client</th>
                    <th class="text-right">Total</th>
                    <th class="text-right">Payé</th>
                    <th class="text-right">Reste</th>
                    <th style="min-width:130px">Progression</th>
                    <th>Date</th>
                    <th>Ancienneté</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($factures as $facture)
                    @php
                        $jours        = $facture->created_at->diffInDays(now());
                        $montantPaye  = $facture->montant_paye;
                        $resteAPayer  = $facture->reste_a_payer;
                        $pct          = $facture->pourcentage_paye;
                    @endphp
                    <tr>
                        <td>
                            <a href="{{ route('factures.show', $facture) }}"
                               class="font-weight-bold">
                                {{ $facture->numero }}
                            </a>
                            @if($facture->reglements->count() > 0)
                                <br>
                                <small class="text-muted">
                                    <i class="fas fa-history mr-1"></i>
                                    {{ $facture->reglements->count() }} versement(s)
                                </small>
                            @endif
                        </td>
                        <td>
                            <strong>
                                <i class="fas fa-user text-muted mr-1"></i>
                                {{ $facture->client_nom ?? 'Client divers' }}
                            </strong>
                            @if($facture->client_telephone)
                                <br>
                                <small class="text-muted">
                                    <i class="fas fa-phone mr-1"></i>
                                    {{ $facture->client_telephone }}
                                </small>
                            @endif
                        </td>
                        <td class="text-right font-weight-bold">
                            {{ number_format($facture->total, 0, ',', ' ') }} FCFA
                        </td>
                        <td class="text-right font-weight-bold text-success">
                            {{ number_format($montantPaye, 0, ',', ' ') }} FCFA
                        </td>
                        <td class="text-right font-weight-bold text-danger">
                            {{ number_format($resteAPayer, 0, ',', ' ') }} FCFA
                        </td>
                        <td>
                            <div class="progress" style="height:16px; border-radius:8px;">
                                <div class="progress-bar bg-success"
                                     style="width:{{ $pct }}%; border-radius:8px; font-size:10px; line-height:16px;">
                                    {{ $pct > 10 ? $pct . '%' : '' }}
                                </div>
                            </div>
                            <small class="text-muted">{{ $pct }}%</small>
                        </td>
                        <td>{{ $facture->created_at->format('d/m/Y') }}</td>
                        <td>
                            <span class="badge {{
                                $jours > 7  ? 'badge-danger'  :
                                ($jours > 3 ? 'badge-warning' : 'badge-info')
                            }}">
                                {{ $jours == 0 ? "Auj." : $jours . 'j' }}
                            </span>
                        </td>
                        <td class="text-center">
                            {{-- Bouton Payer --}}
                            <button class="btn btn-sm btn-success"
                                    data-toggle="modal"
                                    data-target="#modalRegler{{ $facture->id }}"
                                    title="Enregistrer un versement">
                                <i class="fas fa-money-bill-wave"></i>
                            </button>
                            {{-- Bouton Historique --}}
                            <button class="btn btn-sm btn-info ml-1"
                                    data-toggle="modal"
                                    data-target="#modalHistorique{{ $facture->id }}"
                                    title="Voir historique versements">
                                <i class="fas fa-history"></i>
                            </button>
                            {{-- Bouton Voir facture --}}
                            <a href="{{ route('factures.show', $facture) }}"
                               class="btn btn-sm btn-secondary ml-1" title="Voir facture">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>

                    {{-- ======= MODAL PAIEMENT ======= --}}
                    <div class="modal fade" id="modalRegler{{ $facture->id }}" tabindex="-1">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header bg-success text-white">
                                    <h5 class="modal-title">
                                        <i class="fas fa-money-bill-wave mr-2"></i>
                                        Versement — {{ $facture->numero }}
                                    </h5>
                                    <button type="button" class="close text-white"
                                            data-dismiss="modal">
                                        <span>&times;</span>
                                    </button>
                                </div>
                                <form action="{{ route('factures.regler', $facture) }}"
                                      method="POST">
                                    @csrf
                                    <div class="modal-body">

                                        {{-- Résumé 3 cases --}}
                                        <div class="row text-center mb-4">
                                            <div class="col-4">
                                                <div class="p-2 rounded bg-light border">
                                                    <small class="text-muted d-block">Total</small>
                                                    <strong>
                                                        {{ number_format($facture->total, 0, ',', ' ') }}
                                                    </strong>
                                                </div>
                                            </div>
                                            <div class="col-4">
                                                <div class="p-2 rounded bg-success text-white">
                                                    <small class="d-block" style="opacity:.8">Déjà payé</small>
                                                    <strong>
                                                        {{ number_format($montantPaye, 0, ',', ' ') }}
                                                    </strong>
                                                </div>
                                            </div>
                                            <div class="col-4">
                                                <div class="p-2 rounded bg-danger text-white">
                                                    <small class="d-block" style="opacity:.8">Reste dû</small>
                                                    <strong>
                                                        {{ number_format($resteAPayer, 0, ',', ' ') }}
                                                    </strong>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="font-weight-bold">
                                                Montant versé (FCFA)
                                                <span class="text-danger">*</span>
                                            </label>
                                            <input type="number"
                                                   name="montant"
                                                   class="form-control form-control-lg
                                                          text-center font-weight-bold"
                                                   min="1"
                                                   max="{{ $resteAPayer }}"
                                                   value="{{ $resteAPayer }}"
                                                   required>
                                            <small class="text-muted">
                                                Vous pouvez saisir un montant partiel.
                                                Max : {{ number_format($resteAPayer, 0, ',', ' ') }} FCFA
                                            </small>
                                        </div>

                                        <div class="form-group">
                                            <label class="font-weight-bold">Mode de paiement</label>
                                            <select name="mode_paiement"
                                                    class="form-control" required>
                                                <option value="espece">💵 Espèce</option>
                                                <option value="mobile_money">📱 Mobile Money</option>
                                                <option value="carte">💳 Carte bancaire</option>
                                            </select>
                                        </div>

                                        <div class="form-group mb-0">
                                            <label class="font-weight-bold">
                                                Notes <small class="text-muted">(optionnel)</small>
                                            </label>
                                            <input type="text" name="notes"
                                                   class="form-control"
                                                   placeholder="Ex: 1er versement...">
                                        </div>

                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary"
                                                data-dismiss="modal">
                                            <i class="fas fa-times mr-1"></i> Annuler
                                        </button>
                                        <button type="submit" class="btn btn-success btn-lg">
                                            <i class="fas fa-check mr-1"></i>
                                            Enregistrer le versement
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    {{-- ======= MODAL HISTORIQUE ======= --}}
                    <div class="modal fade" id="modalHistorique{{ $facture->id }}" tabindex="-1">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header bg-info text-white">
                                    <h5 class="modal-title">
                                        <i class="fas fa-history mr-2"></i>
                                        Historique — {{ $facture->numero }}
                                    </h5>
                                    <button type="button" class="close text-white"
                                            data-dismiss="modal">
                                        <span>&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body p-0">

                                    {{-- Barre progression --}}
                                    <div class="p-3 bg-light border-bottom">
                                        <div class="d-flex justify-content-between mb-1">
                                            <small class="font-weight-bold">Progression du règlement</small>
                                            <small class="font-weight-bold text-success">{{ $pct }}%</small>
                                        </div>
                                        <div class="progress" style="height:14px; border-radius:8px;">
                                            <div class="progress-bar bg-success"
                                                 style="width:{{ $pct }}%;
                                                        border-radius:8px;
                                                        font-size:10px;
                                                        line-height:14px;">
                                            </div>
                                        </div>
                                        <div class="row text-center mt-2">
                                            <div class="col-4">
                                                <small class="text-muted d-block">Total</small>
                                                <strong>{{ number_format($facture->total, 0, ',', ' ') }}</strong>
                                            </div>
                                            <div class="col-4">
                                                <small class="text-success d-block">Payé</small>
                                                <strong class="text-success">
                                                    {{ number_format($montantPaye, 0, ',', ' ') }}
                                                </strong>
                                            </div>
                                            <div class="col-4">
                                                <small class="text-danger d-block">Reste</small>
                                                <strong class="text-danger">
                                                    {{ number_format($resteAPayer, 0, ',', ' ') }}
                                                </strong>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Liste versements --}}
                                    @if($facture->reglements->count() > 0)
                                        <table class="table table-sm mb-0">
                                            <thead class="bg-light">
                                                <tr>
                                                    <th>#</th>
                                                    <th>Montant</th>
                                                    <th>Mode</th>
                                                    <th>Notes</th>
                                                    <th>Par</th>
                                                    <th>Date</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($facture->reglements->sortBy('created_at') as $i => $reg)
                                                    <tr>
                                                        <td>
                                                            <span class="badge badge-secondary">
                                                                {{ $i + 1 }}
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <strong class="text-success">
                                                                {{ number_format($reg->montant, 0, ',', ' ') }}
                                                                FCFA
                                                            </strong>
                                                        </td>
                                                        <td>
                                                            <small>{{ $reg->mode_paiement_label }}</small>
                                                        </td>
                                                        <td>
                                                            <small class="text-muted">
                                                                {{ $reg->notes ?? '—' }}
                                                            </small>
                                                        </td>
                                                        <td>
                                                            <small>{{ $reg->user->prenom }}</small>
                                                        </td>
                                                        <td>
                                                            <small>
                                                                {{ $reg->created_at->format('d/m/Y H:i') }}
                                                            </small>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                            <tfoot class="bg-light">
                                                <tr>
                                                    <td colspan="1">
                                                        <strong>Total versé</strong>
                                                    </td>
                                                    <td>
                                                        <strong class="text-success">
                                                            {{ number_format($montantPaye, 0, ',', ' ') }} FCFA
                                                        </strong>
                                                    </td>
                                                    <td colspan="4"></td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    @else
                                        <div class="text-center text-muted py-4">
                                            <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                                            Aucun versement enregistré
                                        </div>
                                    @endif
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary"
                                            data-dismiss="modal">
                                        Fermer
                                    </button>
                                    <button class="btn btn-success"
                                            data-dismiss="modal"
                                            data-toggle="modal"
                                            data-target="#modalRegler{{ $facture->id }}">
                                        <i class="fas fa-plus mr-1"></i>
                                        Nouveau versement
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                @empty
                    <tr>
                        <td colspan="9" class="text-center py-5 text-muted">
                            <i class="fas fa-check-circle fa-3x text-success mb-3 d-block"></i>
                            <strong>Aucune facture crédit en attente !</strong>
                            <br>
                            <small>Tous les crédits sont réglés.</small>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer">
        @include('partials.pagination', ['paginator' => $factures])
    </div>
</div>

@endsection