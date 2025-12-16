@extends('layouts.app')

@section('title', 'Détail PEC - ' . $pec->numero_pec)

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h3 mb-0">
                    <i class="fas fa-shield-alt"></i> Prise en Charge {{ $pec->numero_pec }}
                </h1>
                <div>
                    <a href="{{ route('pec.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Retour
                    </a>
                    <a href="{{ route('pec.pdf', $pec) }}" class="btn btn-danger" target="_blank">
                        <i class="fas fa-file-pdf"></i> PDF
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Informations principales -->
        <div class="col-lg-8">
            <!-- En-tête de la PEC -->
            <div class="card shadow mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-file-medical"></i> Demande de Prise en Charge
                    </h5>
                </div>
                <div class="card-body">
                    <!-- Statut et dates -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <p class="mb-2">
                                <strong>Statut actuel :</strong> {!! $pec->statut_badge !!}
                            </p>
                            <p class="mb-2">
                                <strong>Date de demande :</strong> {{ $pec->date_demande->format('d/m/Y H:i') }}
                            </p>
                            @if($pec->date_reponse)
                            <p class="mb-2">
                                <strong>Date de réponse :</strong> {{ $pec->date_reponse->format('d/m/Y H:i') }}
                            </p>
                            <p class="mb-2">
                                <strong>Délai de traitement :</strong> {{ $pec->delai_traitement }}
                            </p>
                            @endif
                        </div>
                        <div class="col-md-6 text-md-right">
                            <h4 class="text-primary">{{ $pec->numero_pec }}</h4>
                            <p class="mb-2">
                                <span class="badge badge-info">{{ ucfirst($pec->type_pec) }}</span>
                                @if($pec->type_pec == 'urgence')
                                    <span class="badge badge-danger">URGENT</span>
                                @endif
                            </p>
                            @if($pec->date_expiration)
                            <p class="mb-0">
                                <strong>Valide jusqu'au :</strong><br>
                                {{ $pec->date_expiration->format('d/m/Y') }}
                                @if($pec->estExpiree())
                                    <span class="badge badge-secondary">Expirée</span>
                                @elseif($pec->date_expiration->diffInDays() <= 7)
                                    <span class="badge badge-warning">{{ $pec->date_expiration->diffInDays() }}j restants</span>
                                @endif
                            </p>
                            @endif
                        </div>
                    </div>

                    <hr>

                    <!-- Informations patient -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <h6 class="font-weight-bold">Patient</h6>
                            <p class="mb-1">
                                <strong>{{ $pec->patient->nom }} {{ $pec->patient->prenom }}</strong><br>
                                Né(e) le : {{ $pec->patient->date_naissance ? $pec->patient->date_naissance->format('d/m/Y') : 'N/A' }}<br>
                                Tél : {{ $pec->patient->telephone }}<br>
                                Email : {{ $pec->patient->email }}
                            </p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="font-weight-bold">Contrat d'assurance</h6>
                            <p class="mb-1">
                                <strong>{{ $pec->contrat->assurance->name }}</strong><br>
                                N° contrat : {{ $pec->contrat->numero_contrat }}<br>
                                Type : {{ ucfirst($pec->contrat->type_contrat) }}<br>
                                Taux de couverture : {{ $pec->contrat->taux_couverture }}%
                            </p>
                        </div>
                    </div>

                    <!-- Informations praticien/structure -->
                    @if($pec->praticien || $pec->structure)
                    <div class="row mb-3">
                        @if($pec->praticien)
                        <div class="col-md-6">
                            <h6 class="font-weight-bold">Praticien</h6>
                            <p class="mb-1">
                                <strong>Dr. {{ $pec->praticien->nom }} {{ $pec->praticien->prenom }}</strong><br>
                                {{ $pec->praticien->specialite }}<br>
                                Tél : {{ $pec->praticien->telephone }}
                            </p>
                        </div>
                        @endif
                        @if($pec->structure)
                        <div class="col-md-6">
                            <h6 class="font-weight-bold">Structure médicale</h6>
                            <p class="mb-1">
                                <strong>{{ $pec->structure->nom }}</strong><br>
                                {{ $pec->structure->type_structure }}<br>
                                {{ $pec->structure->adresse }}
                            </p>
                        </div>
                        @endif
                    </div>
                    @endif

                    <!-- Motif de la demande -->
                    <div class="row mb-3">
                        <div class="col-12">
                            <h6 class="font-weight-bold">Motif de la demande</h6>
                            <p class="mb-0">{{ $pec->motif }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Informations financières -->
            <div class="card shadow mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-coins"></i> Informations Financières
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="text-center p-3 border rounded">
                                <h6 class="text-muted">Montant demandé</h6>
                                <h4 class="text-primary">{{ number_format($pec->montant_demande, 0, ',', ' ') }} FCFA</h4>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center p-3 border rounded">
                                <h6 class="text-muted">Montant accordé</h6>
                                <h4 class="text-success">
                                    @if($pec->montant_accorde !== null)
                                        {{ number_format($pec->montant_accorde, 0, ',', ' ') }} FCFA
                                    @else
                                        <span class="text-muted">En attente</span>
                                    @endif
                                </h4>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center p-3 border rounded">
                                <h6 class="text-muted">Reste à charge</h6>
                                <h4 class="text-warning">
                                    @if($pec->montant_accorde !== null)
                                        {{ number_format($pec->calculerResteACharge(), 0, ',', ' ') }} FCFA
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </h4>
                            </div>
                        </div>
                    </div>

                    @if($pec->taux_pec)
                    <div class="row mt-3">
                        <div class="col-12">
                            <div class="progress" style="height: 25px;">
                                <div class="progress-bar bg-success" role="progressbar"
                                     style="width: {{ $pec->taux_pec }}%"
                                     aria-valuenow="{{ $pec->taux_pec }}"
                                     aria-valuemin="0" aria-valuemax="100">
                                    Taux de couverture : {{ $pec->taux_pec }}%
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Documents liés -->
            @if($pec->devis || $pec->facture || !empty($pec->justificatifs))
            <div class="card shadow mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-paperclip"></i> Documents Associés
                    </h5>
                </div>
                <div class="card-body">
                    <div class="list-group">
                        @if($pec->devis)
                        <a href="{{ route('devis.show', $pec->devis) }}" class="list-group-item list-group-item-action">
                            <div class="d-flex w-100 justify-content-between">
                                <h6 class="mb-1">
                                    <i class="fas fa-file-invoice"></i> Devis #{{ $pec->devis->numero_devis }}
                                </h6>
                                <small>{{ $pec->devis->date_devis->format('d/m/Y') }}</small>
                            </div>
                            <p class="mb-1">Montant : {{ number_format($pec->devis->montant_total, 0, ',', ' ') }} FCFA</p>
                            <small>{!! $pec->devis->statut_badge !!}</small>
                        </a>
                        @endif

                        @if($pec->facture)
                        <a href="{{ route('factures.show', $pec->facture) }}" class="list-group-item list-group-item-action">
                            <div class="d-flex w-100 justify-content-between">
                                <h6 class="mb-1">
                                    <i class="fas fa-file-invoice-dollar"></i> Facture #{{ $pec->facture->numero_facture }}
                                </h6>
                                <small>{{ $pec->facture->date_facture->format('d/m/Y') }}</small>
                            </div>
                            <p class="mb-1">Montant : {{ number_format($pec->facture->montant_total, 0, ',', ' ') }} FCFA</p>
                            <small>{!! $pec->facture->statut_badge !!}</small>
                        </a>
                        @endif

                        @if($pec->justificatifs && count($pec->justificatifs) > 0)
                        <div class="list-group-item">
                            <h6 class="mb-2">
                                <i class="fas fa-file-alt"></i> Justificatifs ({{ count($pec->justificatifs) }})
                            </h6>
                            <div class="row">
                                @foreach($pec->justificatifs as $justificatif)
                                <div class="col-md-6 mb-2">
                                    @if(isset($justificatif['type']))
                                    <a href="{{ Storage::url($justificatif['fichier'] ?? '') }}"
                                       target="_blank" class="btn btn-sm btn-outline-primary btn-block">
                                        <i class="fas fa-download"></i>
                                        {{ ucfirst($justificatif['type']) }}
                                    </a>
                                    @endif
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @endif

            <!-- Commentaire assurance -->
            @if($pec->commentaire_assurance)
            <div class="card shadow mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-comment-medical"></i> Commentaire de l'Assurance
                    </h5>
                </div>
                <div class="card-body">
                    <blockquote class="blockquote mb-0">
                        <p>{{ $pec->commentaire_assurance }}</p>
                        @if($pec->date_reponse)
                        <footer class="blockquote-footer">
                            Répondu le {{ $pec->date_reponse->format('d/m/Y à H:i') }}
                        </footer>
                        @endif
                    </blockquote>
                </div>
            </div>
            @endif

            <!-- Actions -->
            @if($pec->statut == 'en_attente' || !in_array($pec->statut, ['utilisee', 'annulee']))
            <div class="card shadow">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-cogs"></i> Actions
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @can('validate', $pec)
                            @if($pec->statut == 'en_attente')
                            <div class="col-md-6 mb-3">
                                <button type="button" class="btn btn-success btn-block"
                                        onclick="accepterPEC()">
                                    <i class="fas fa-check"></i> Accepter la PEC
                                </button>
                            </div>
                            <div class="col-md-6 mb-3">
                                <button type="button" class="btn btn-danger btn-block"
                                        onclick="refuserPEC()">
                                    <i class="fas fa-times"></i> Refuser la PEC
                                </button>
                            </div>
                            @endif
                        @endcan

                        @can('cancel', $pec)
                            @if(!in_array($pec->statut, ['utilisee', 'annulee']))
                            <div class="col-md-6 mb-3">
                                <button type="button" class="btn btn-warning btn-block"
                                        onclick="annulerPEC()">
                                    <i class="fas fa-ban"></i> Annuler la PEC
                                </button>
                            </div>
                            @endif
                        @endcan

                        @if($pec->statut == 'acceptee' && !$pec->facture)
                        <div class="col-md-6 mb-3">
                            <a href="{{ route('factures.create', ['pec_id' => $pec->id]) }}"
                               class="btn btn-info btn-block">
                                <i class="fas fa-file-invoice-dollar"></i> Créer une facture
                            </a>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Panneau latéral -->
        <div class="col-lg-4">
            <!-- Informations contrat -->
            <div class="card shadow mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-file-contract"></i> Contrat d'Assurance
                    </h5>
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-5">Assureur :</dt>
                        <dd class="col-sm-7">{{ $pec->contrat->assurance->name }}</dd>

                        <dt class="col-sm-5">N° Contrat :</dt>
                        <dd class="col-sm-7">{{ $pec->contrat->numero_contrat }}</dd>

                        <dt class="col-sm-5">Type :</dt>
                        <dd class="col-sm-7">{{ ucfirst($pec->contrat->type_contrat) }}</dd>

                        <dt class="col-sm-5">Statut :</dt>
                        <dd class="col-sm-7">{!! $pec->contrat->statut_badge !!}</dd>

                        <dt class="col-sm-5">Validité :</dt>
                        <dd class="col-sm-7">
                            Du {{ $pec->contrat->date_debut->format('d/m/Y') }}<br>
                            Au {{ $pec->contrat->date_fin->format('d/m/Y') }}
                        </dd>

                        <dt class="col-sm-5">Plafond annuel :</dt>
                        <dd class="col-sm-7">{{ number_format($pec->contrat->plafond_annuel, 0, ',', ' ') }} FCFA</dd>

                        <dt class="col-sm-5">Plafond restant :</dt>
                        <dd class="col-sm-7">
                            {{ number_format($pec->contrat->montantRestant(), 0, ',', ' ') }} FCFA
                            <div class="progress mt-1" style="height: 10px;">
                                <div class="progress-bar" role="progressbar"
                                     style="width: {{ $pec->contrat->tauxRestant() }}%">
                                </div>
                            </div>
                        </dd>
                    </dl>

                    <a href="{{ route('contrats-assurance.show', $pec->contrat) }}"
                       class="btn btn-sm btn-primary btn-block">
                        Voir le contrat complet
                    </a>
                </div>
            </div>

            <!-- Historique -->
            <div class="card shadow mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-history"></i> Historique
                    </h5>
                </div>
                <div class="card-body">
                    <ul class="timeline">
                        <li>
                            <span class="badge badge-primary">{{ $pec->date_demande->format('d/m/Y H:i') }}</span>
                            <p class="mb-1">Demande créée</p>
                        </li>

                        @if($pec->date_reponse)
                        <li>
                            <span class="badge badge-info">{{ $pec->date_reponse->format('d/m/Y H:i') }}</span>
                            <p class="mb-1">
                                @if($pec->statut == 'acceptee')
                                    PEC acceptée
                                @elseif($pec->statut == 'refusee')
                                    PEC refusée
                                @else
                                    Réponse reçue
                                @endif
                            </p>
                        </li>
                        @endif

                        @if($pec->statut == 'utilisee' && isset($pec->metadata['date_utilisation']))
                        <li>
                            <span class="badge badge-success">{{ \Carbon\Carbon::parse($pec->metadata['date_utilisation'])->format('d/m/Y') }}</span>
                            <p class="mb-1">PEC utilisée</p>
                        </li>
                        @endif

                        @if($pec->statut == 'annulee' && isset($pec->metadata['date_annulation']))
                        <li>
                            <span class="badge badge-warning">{{ \Carbon\Carbon::parse($pec->metadata['date_annulation'])->format('d/m/Y') }}</span>
                            <p class="mb-1">PEC annulée</p>
                        </li>
                        @endif
                    </ul>
                </div>
            </div>

            <!-- Litiges -->
            @if($pec->litiges && $pec->litiges->count() > 0)
            <div class="card shadow mb-4 border-danger">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-exclamation-triangle"></i> Litiges
                    </h5>
                </div>
                <div class="card-body">
                    @foreach($pec->litiges as $litige)
                    <div class="alert alert-danger">
                        <h6>{{ $litige->titre }}</h6>
                        <p class="mb-1">{{ Str::limit($litige->description, 100) }}</p>
                        <small>Créé le {{ $litige->created_at->format('d/m/Y') }}</small>
                        <br>
                        <a href="{{ route('litiges.show', $litige) }}" class="btn btn-sm btn-danger mt-2">
                            Voir le litige
                        </a>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Modals pour les actions -->
@include('pec.modals.accepter', ['pec' => $pec])
@include('pec.modals.refuser', ['pec' => $pec])
@include('pec.modals.annuler', ['pec' => $pec])
@endsection

@push('scripts')
<script>
function accepterPEC() {
    $('#accepterModal').modal('show');
}

function refuserPEC() {
    $('#refuserModal').modal('show');
}

function annulerPEC() {
    $('#annulerModal').modal('show');
}
</script>
@endpush

@push('styles')
<style>
.timeline {
    list-style: none;
    padding: 0;
    position: relative;
}

.timeline:before {
    content: '';
    position: absolute;
    top: 0;
    left: 10px;
    height: 100%;
    width: 2px;
    background: #e9ecef;
}

.timeline li {
    position: relative;
    padding-left: 40px;
    margin-bottom: 20px;
}

.timeline li:before {
    content: '';
    position: absolute;
    left: 6px;
    top: 5px;
    width: 10px;
    height: 10px;
    border-radius: 50%;
    background: #007bff;
}
</style>
@endpush
