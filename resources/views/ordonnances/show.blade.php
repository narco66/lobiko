@extends('layouts.app')

@section('title', 'Détail Ordonnance - ' . $ordonnance->numero_ordonnance)

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h3 mb-0">
                    <i class="fas fa-prescription"></i> Ordonnance {{ $ordonnance->numero_ordonnance }}
                </h1>
                <div>
                    <a href="{{ route('ordonnances.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Retour
                    </a>
                    @can('update', $ordonnance)
                        @if($ordonnance->statut != 'dispensee')
                        <a href="{{ route('ordonnances.edit', $ordonnance) }}" class="btn btn-warning">
                            <i class="fas fa-edit"></i> Modifier
                        </a>
                        @endif
                    @endcan
                    <a href="{{ route('ordonnances.pdf', $ordonnance) }}" class="btn btn-danger" target="_blank">
                        <i class="fas fa-file-pdf"></i> PDF
                    </a>
                    <a href="{{ route('ordonnances.print', $ordonnance) }}" class="btn btn-light" target="_blank">
                        <i class="fas fa-print"></i> Imprimer
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Informations principales -->
        <div class="col-lg-8">
            <!-- En-tête de l'ordonnance -->
            <div class="card shadow mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-file-medical"></i> Ordonnance Médicale
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <h6 class="font-weight-bold">Praticien</h6>
                            <p class="mb-1">
                                <strong>Dr. {{ $ordonnance->praticien->nom }} {{ $ordonnance->praticien->prenom }}</strong><br>
                                {{ $ordonnance->praticien->specialite }}<br>
                                @if($ordonnance->structure)
                                {{ $ordonnance->structure->nom }}<br>
                                {{ $ordonnance->structure->adresse }}
                                @endif
                            </p>
                        </div>
                        <div class="col-md-6 text-md-right">
                            <h6 class="font-weight-bold">Date et Numéro</h6>
                            <p class="mb-1">
                                <strong>N° {{ $ordonnance->numero_ordonnance }}</strong><br>
                                Date : {{ $ordonnance->date_ordonnance->format('d/m/Y H:i') }}<br>
                                {!! $ordonnance->type_ordonnance_badge !!}
                                {!! $ordonnance->statut_badge !!}
                            </p>
                        </div>
                    </div>

                    <hr>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <h6 class="font-weight-bold">Patient</h6>
                            <p class="mb-1">
                                <strong>{{ $ordonnance->patient->nom }} {{ $ordonnance->patient->prenom }}</strong><br>
                                Né(e) le : {{ $ordonnance->patient->date_naissance ? $ordonnance->patient->date_naissance->format('d/m/Y') : 'N/A' }}<br>
                                Tél : {{ $ordonnance->patient->telephone }}<br>
                                @if($ordonnance->patient->dossierMedical)
                                    @if($ordonnance->patient->dossierMedical->allergies)
                                    <span class="text-danger">
                                        <i class="fas fa-exclamation-triangle"></i>
                                        Allergies : {{ implode(', ', $ordonnance->patient->dossierMedical->allergies) }}
                                    </span>
                                    @endif
                                @endif
                            </p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="font-weight-bold">Diagnostic</h6>
                            <p class="mb-1">
                                <strong>{{ $ordonnance->diagnostic }}</strong><br>
                                @if($ordonnance->observations)
                                Observations : {{ $ordonnance->observations }}
                                @endif
                            </p>
                        </div>
                    </div>

                    @if($ordonnance->metadata && isset($ordonnance->metadata['constantes']))
                    <div class="row mb-3">
                        <div class="col-12">
                            <h6 class="font-weight-bold">Constantes vitales</h6>
                            <p class="mb-1">
                                @foreach($ordonnance->metadata['constantes'] as $key => $value)
                                    <span class="badge badge-secondary mr-2">
                                        {{ ucfirst($key) }} : {{ $value }}
                                    </span>
                                @endforeach
                            </p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Médicaments prescrits -->
            <div class="card shadow mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-pills"></i> Médicaments Prescrits ({{ $ordonnance->lignes->count() }})
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="thead-light">
                                <tr>
                                    <th width="5%">#</th>
                                    <th width="25%">Médicament</th>
                                    <th width="20%">Posologie</th>
                                    <th width="15%">Quantité</th>
                                    <th width="15%">Durée</th>
                                    <th width="10%">Prix</th>
                                    <th width="10%">Statut</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($ordonnance->lignes as $index => $ligne)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        <strong>{{ $ligne->nom_commercial }}</strong><br>
                                        <small class="text-muted">
                                            DCI : {{ $ligne->dci }}<br>
                                            {{ $ligne->dosage }} - {{ $ligne->forme }}
                                        </small>
                                        @if($ligne->urgence)
                                            {!! $ligne->urgence_badge !!}
                                        @endif
                                        @if($ligne->substitution_autorisee)
                                            {!! $ligne->substitution_badge !!}
                                        @endif
                                    </td>
                                    <td>
                                        {{ $ligne->posologie }}<br>
                                        @if($ligne->voie_administration)
                                            <small>Voie : {{ $ligne->voie_administration }}</small><br>
                                        @endif
                                        @if($ligne->instructions_speciales)
                                            <small class="text-info">
                                                <i class="fas fa-info-circle"></i> {{ $ligne->instructions_speciales }}
                                            </small>
                                        @endif
                                    </td>
                                    <td>
                                        {{ $ligne->quantite }} unité(s)
                                        @if($ligne->dispensee)
                                            <br><small class="text-success">
                                                Dispensé : {{ $ligne->quantite_dispensee }}
                                            </small>
                                        @endif
                                    </td>
                                    <td>
                                        @if($ligne->duree_traitement)
                                            {{ $ligne->duree_traitement }} {{ $ligne->unite_duree }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        {{ number_format($ligne->prix_unitaire, 0, ',', ' ') }} FCFA
                                    </td>
                                    <td>
                                        {!! $ligne->statut_dispensation !!}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="5" class="text-right">Total</th>
                                    <th colspan="2">
                                        {{ number_format($ordonnance->montant_total, 0, ',', ' ') }} FCFA
                                    </th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    @if($ordonnance->renouvelable)
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        Cette ordonnance est renouvelable {{ $ordonnance->nombre_renouvellements }} fois.
                        ({{ $ordonnance->renouvellements_effectues }} renouvellement(s) effectué(s))
                    </div>
                    @endif
                </div>
            </div>

            <!-- Actions -->
            <div class="card shadow">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-cogs"></i> Actions
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @if($ordonnance->peutEtreRenouvelee())
                        <div class="col-md-4 mb-3">
                            <form action="{{ route('ordonnances.renouveler', $ordonnance) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-success btn-block"
                                        onclick="return confirm('Renouveler cette ordonnance ?')">
                                    <i class="fas fa-redo"></i> Renouveler l'ordonnance
                                </button>
                            </form>
                        </div>
                        @endif

                        @can('dispenser', $ordonnance)
                            @if($ordonnance->statut == 'active')
                            <div class="col-md-4 mb-3">
                                <button type="button" class="btn btn-primary btn-block"
                                        onclick="dispenserOrdonnance()">
                                    <i class="fas fa-pills"></i> Dispenser les médicaments
                                </button>
                            </div>
                            @endif
                        @endcan

                        <div class="col-md-4 mb-3">
                            <a href="{{ route('commandes.create', ['ordonnance_id' => $ordonnance->id]) }}"
                               class="btn btn-info btn-block">
                                <i class="fas fa-shopping-cart"></i> Commander en pharmacie
                            </a>
                        </div>

                        @can('delete', $ordonnance)
                            @if($ordonnance->statut != 'dispensee')
                            <div class="col-md-4 mb-3">
                                <form action="{{ route('ordonnances.destroy', $ordonnance) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-block"
                                            onclick="return confirm('Supprimer cette ordonnance ?')">
                                        <i class="fas fa-trash"></i> Supprimer l'ordonnance
                                    </button>
                                </form>
                            </div>
                            @endif
                        @endcan

                        <div class="col-md-4 mb-3">
                            <button type="button" class="btn btn-warning btn-block"
                                    onclick="duplicateOrdonnance()">
                                <i class="fas fa-copy"></i> Dupliquer l'ordonnance
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Panneau latéral -->
        <div class="col-lg-4">
            <!-- QR Code et Signature -->
            <div class="card shadow mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-qrcode"></i> Vérification
                    </h5>
                </div>
                <div class="card-body text-center">
                    @if($ordonnance->qr_code)
                    <img src="{{ $ordonnance->qr_code }}" alt="QR Code" class="img-fluid mb-3" style="max-width: 200px;">
                    <p class="small text-muted">
                        Scannez ce QR code pour vérifier l'authenticité
                    </p>
                    @endif

                    <hr>

                    <p class="mb-1">
                        <strong>Signature numérique :</strong>
                    </p>
                    <p class="small text-monospace" style="word-break: break-all;">
                        {{ substr($ordonnance->signature_numerique, 0, 32) }}...
                    </p>
                </div>
            </div>

            <!-- Validité -->
            <div class="card shadow mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-calendar-check"></i> Validité
                    </h5>
                </div>
                <div class="card-body">
                    <p class="mb-2">
                        <strong>Date d'émission :</strong><br>
                        {{ $ordonnance->date_ordonnance->format('d/m/Y à H:i') }}
                    </p>

                    @if($ordonnance->date_expiration)
                    <p class="mb-2">
                        <strong>Date d'expiration :</strong><br>
                        {{ $ordonnance->date_expiration->format('d/m/Y') }}
                    </p>

                    @if($ordonnance->estValide())
                    <div class="alert alert-success mb-0">
                        <i class="fas fa-check-circle"></i> Ordonnance valide
                        <br>
                        <small>Encore {{ $ordonnance->date_expiration->diffInDays() }} jour(s)</small>
                    </div>
                    @else
                    <div class="alert alert-danger mb-0">
                        <i class="fas fa-times-circle"></i> Ordonnance expirée
                    </div>
                    @endif
                    @else
                    <div class="alert alert-info mb-0">
                        <i class="fas fa-infinity"></i> Validité illimitée
                    </div>
                    @endif
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
                    @if($ordonnance->consultation)
                    <p class="mb-2">
                        <strong>Consultation associée :</strong><br>
                        <a href="{{ route('consultations.show', $ordonnance->consultation) }}">
                            #{{ $ordonnance->consultation->numero_consultation }}
                        </a>
                        <br>
                        <small>{{ $ordonnance->consultation->date_consultation->format('d/m/Y') }}</small>
                    </p>
                    @endif

                    @if($ordonnance->dispensations && $ordonnance->dispensations->count() > 0)
                    <p class="mb-2">
                        <strong>Dispensations :</strong>
                    </p>
                    <ul class="list-unstyled">
                        @foreach($ordonnance->dispensations as $dispensation)
                        <li class="mb-1">
                            <small>
                                <i class="fas fa-check"></i>
                                {{ $dispensation->date_dispensation->format('d/m/Y') }}
                                - {{ $dispensation->pharmacie->nom }}
                            </small>
                        </li>
                        @endforeach
                    </ul>
                    @endif

                    @if($ordonnance->commandes && $ordonnance->commandes->count() > 0)
                    <p class="mb-2">
                        <strong>Commandes associées :</strong>
                    </p>
                    <ul class="list-unstyled">
                        @foreach($ordonnance->commandes as $commande)
                        <li class="mb-1">
                            <a href="{{ route('commandes.show', $commande) }}">
                                #{{ $commande->numero_commande }}
                            </a>
                            {!! $commande->statut_badge !!}
                        </li>
                        @endforeach
                    </ul>
                    @endif
                </div>
            </div>

            <!-- Interactions et alertes -->
            @php
                $interactions = $ordonnance->verifierInteractionsMedicamenteuses();
                $contrIndications = $ordonnance->verifierContrIndications();
            @endphp

            @if(count($interactions) > 0 || count($contrIndications) > 0)
            <div class="card shadow border-danger">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-exclamation-triangle"></i> Alertes
                    </h5>
                </div>
                <div class="card-body">
                    @if(count($interactions) > 0)
                    <h6 class="text-danger">Interactions médicamenteuses :</h6>
                    <ul>
                        @foreach($interactions as $interaction)
                        <li>{{ $interaction }}</li>
                        @endforeach
                    </ul>
                    @endif

                    @if(count($contrIndications) > 0)
                    <h6 class="text-danger">Contre-indications :</h6>
                    <ul>
                        @foreach($contrIndications as $ci)
                        <li>
                            <strong>{{ $ci['produit'] }}</strong> : {{ $ci['detail'] }}
                        </li>
                        @endforeach
                    </ul>
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal Dispensation -->
<div class="modal fade" id="dispenserModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('ordonnances.dispenser', $ordonnance) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Dispenser l'ordonnance</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Veuillez indiquer les quantités dispensées pour chaque médicament :</p>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Médicament</th>
                                <th>Quantité prescrite</th>
                                <th>Quantité à dispenser</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($ordonnance->lignes->where('dispensee', false) as $ligne)
                            <tr>
                                <td>
                                    {{ $ligne->nom_commercial }}<br>
                                    <small>{{ $ligne->dosage }} - {{ $ligne->forme }}</small>
                                </td>
                                <td>{{ $ligne->quantite }}</td>
                                <td>
                                    <input type="hidden" name="lignes[{{ $loop->index }}][id]" value="{{ $ligne->id }}">
                                    <input type="number"
                                           name="lignes[{{ $loop->index }}][quantite_dispensee]"
                                           class="form-control"
                                           min="1"
                                           max="{{ $ligne->quantite }}"
                                           value="{{ $ligne->quantite }}"
                                           required>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Confirmer la dispensation</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function dispenserOrdonnance() {
    $('#dispenserModal').modal('show');
}

function duplicateOrdonnance() {
    if (confirm('Dupliquer cette ordonnance ?')) {
        window.location.href = "{{ route('ordonnances.duplicate', $ordonnance) }}";
    }
}
</script>
@endpush
