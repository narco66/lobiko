@extends('layouts.app')

@section('title', 'Prises en Charge')

@section('content')
<div class="container-fluid">
    <!-- Header avec statistiques -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h3 mb-0">
                    <i class="fas fa-shield-alt"></i> Prises en Charge (PEC)
                </h1>
                @can('pec.create')
                <a href="{{ route('pec.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Nouvelle PEC
                </a>
                @endcan
            </div>
        </div>
    </div>

    <!-- Cartes de statistiques -->
    <div class="row mb-4">
        <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($stats['total'] ?? 0) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                En attente
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($stats['en_attente'] ?? 0) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-hourglass-half fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Acceptées
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($stats['acceptees'] ?? 0) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                Refusées
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($stats['refusees'] ?? 0) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-times-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Taux Accept.
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $stats['taux_acceptation'] ?? 0 }}%
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-percentage fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
            <div class="card border-left-secondary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">
                                Montant Total
                            </div>
                            <div class="h6 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($stats['montant_total'] ?? 0, 0, ',', ' ') }} F
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-coins fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtres -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-filter"></i> Filtres
            </h6>
            <button type="button" class="btn btn-sm btn-secondary" onclick="resetFilters()">
                <i class="fas fa-redo"></i> Réinitialiser
            </button>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('pec.index') }}" id="filterForm">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label for="statut">Statut</label>
                        <select name="statut" id="statut" class="form-control">
                            <option value="">Tous</option>
                            <option value="en_attente" {{ request('statut') == 'en_attente' ? 'selected' : '' }}>
                                En attente
                            </option>
                            <option value="acceptee" {{ request('statut') == 'acceptee' ? 'selected' : '' }}>
                                Acceptée
                            </option>
                            <option value="refusee" {{ request('statut') == 'refusee' ? 'selected' : '' }}>
                                Refusée
                            </option>
                            <option value="utilisee" {{ request('statut') == 'utilisee' ? 'selected' : '' }}>
                                Utilisée
                            </option>
                            <option value="expiree" {{ request('statut') == 'expiree' ? 'selected' : '' }}>
                                Expirée
                            </option>
                            <option value="annulee" {{ request('statut') == 'annulee' ? 'selected' : '' }}>
                                Annulée
                            </option>
                        </select>
                    </div>

                    @if(!auth()->user()->hasRole('patient'))
                    <div class="col-md-3 mb-3">
                        <label for="patient">Patient</label>
                        <select name="patient" id="patient" class="form-control select2">
                            <option value="">Tous les patients</option>
                            @foreach(\App\Models\User::role('patient')->get() as $patient)
                                <option value="{{ $patient->id }}" {{ request('patient') == $patient->id ? 'selected' : '' }}>
                                    {{ $patient->nom }} {{ $patient->prenom }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @endif

                    @if(!auth()->user()->hasRole('praticien'))
                    <div class="col-md-3 mb-3">
                        <label for="praticien">Praticien</label>
                        <select name="praticien" id="praticien" class="form-control select2">
                            <option value="">Tous les praticiens</option>
                            @foreach(\App\Models\User::role('praticien')->get() as $praticien)
                                <option value="{{ $praticien->id }}" {{ request('praticien') == $praticien->id ? 'selected' : '' }}>
                                    Dr. {{ $praticien->nom }} {{ $praticien->prenom }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @endif

                    <div class="col-md-3 mb-3">
                        <label for="date_debut">Date début</label>
                        <input type="date" name="date_debut" id="date_debut" class="form-control"
                               value="{{ request('date_debut') }}">
                    </div>

                    <div class="col-md-3 mb-3">
                        <label for="date_fin">Date fin</label>
                        <input type="date" name="date_fin" id="date_fin" class="form-control"
                               value="{{ request('date_fin') }}">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="search">Recherche</label>
                        <input type="text" name="search" id="search" class="form-control"
                               placeholder="N° PEC, motif, patient..."
                               value="{{ request('search') }}">
                    </div>

                    <div class="col-md-12">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i> Rechercher
                        </button>
                        @can('pec.export')
                        <button type="button" class="btn btn-success" onclick="exportData()">
                            <i class="fas fa-file-excel"></i> Exporter
                        </button>
                        @endcan
                        @can('pec.dashboard')
                        <a href="{{ route('pec.dashboard') }}" class="btn btn-info">
                            <i class="fas fa-chart-line"></i> Tableau de bord
                        </a>
                        @endcan
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Tableau des PEC -->
    <div class="card shadow">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                Liste des Prises en Charge ({{ $pecs->total() }} résultats)
            </h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="pecTable">
                    <thead>
                        <tr>
                            <th>N° PEC</th>
                            <th>Date demande</th>
                            <th>Patient</th>
                            <th>Type</th>
                            <th>Motif</th>
                            <th>Montant demandé</th>
                            <th>Montant accordé</th>
                            <th>Taux</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pecs as $pec)
                        <tr>
                            <td>
                                <strong>{{ $pec->numero_pec }}</strong>
                                @if($pec->type_pec == 'urgence')
                                    <span class="badge badge-danger">URG</span>
                                @endif
                            </td>
                            <td data-sort="{{ $pec->date_demande->timestamp }}">
                                {{ $pec->date_demande->format('d/m/Y H:i') }}
                                @if($pec->date_reponse)
                                    <br>
                                    <small class="text-muted">
                                        Rép: {{ $pec->date_reponse->format('d/m/Y') }}
                                    </small>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('patients.show', $pec->patient_id) }}">
                                    {{ $pec->patient->nom }} {{ $pec->patient->prenom }}
                                </a>
                                <br>
                                <small class="text-muted">
                                    {{ $pec->contrat->assurance->name ?? 'N/A' }}
                                </small>
                            </td>
                            <td>
                                <span class="badge badge-info">{{ ucfirst($pec->type_pec) }}</span>
                            </td>
                            <td>{{ Str::limit($pec->motif, 30) }}</td>
                            <td class="text-right">
                                {{ number_format($pec->montant_demande, 0, ',', ' ') }} F
                            </td>
                            <td class="text-right">
                                @if($pec->montant_accorde !== null)
                                    {{ number_format($pec->montant_accorde, 0, ',', ' ') }} F
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($pec->taux_pec)
                                    {{ $pec->taux_pec }}%
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>{!! $pec->statut_badge !!}</td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('pec.show', $pec) }}"
                                       class="btn btn-sm btn-info" title="Voir">
                                        <i class="fas fa-eye"></i>
                                    </a>

                                    @can('validate', $pec)
                                        @if($pec->statut == 'en_attente')
                                        <button type="button" class="btn btn-sm btn-success"
                                                title="Accepter"
                                                onclick="accepterPEC({{ $pec->id }})">
                                            <i class="fas fa-check"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-danger"
                                                title="Refuser"
                                                onclick="refuserPEC({{ $pec->id }})">
                                            <i class="fas fa-times"></i>
                                        </button>
                                        @endif
                                    @endcan

                                    <a href="{{ route('pec.pdf', $pec) }}"
                                       class="btn btn-sm btn-secondary" title="PDF" target="_blank">
                                        <i class="fas fa-file-pdf"></i>
                                    </a>

                                    @can('cancel', $pec)
                                        @if(!in_array($pec->statut, ['utilisee', 'annulee']))
                                        <button type="button" class="btn btn-sm btn-warning"
                                                title="Annuler"
                                                onclick="annulerPEC({{ $pec->id }})">
                                            <i class="fas fa-ban"></i>
                                        </button>
                                        @endif
                                    @endcan
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="text-center py-4">
                                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                <p class="text-muted">Aucune prise en charge trouvée</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-4">
                {{ $pecs->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Modal Accepter PEC -->
<div class="modal fade" id="accepterModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="accepterForm" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Accepter la prise en charge</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Montant demandé</label>
                        <input type="text" class="form-control" id="montantDemande" readonly>
                    </div>
                    <div class="form-group">
                        <label>Montant accordé <span class="text-danger">*</span></label>
                        <input type="number" name="montant_accorde" id="montantAccorde"
                               class="form-control" required min="0" step="0.01">
                    </div>
                    <div class="form-group">
                        <label>Commentaire</label>
                        <textarea name="commentaire" class="form-control" rows="3"
                                  placeholder="Commentaire optionnel..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-success">Accepter la PEC</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Refuser PEC -->
<div class="modal fade" id="refuserModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="refuserForm" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Refuser la prise en charge</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Motif du refus <span class="text-danger">*</span></label>
                        <select name="motif_refus" class="form-control" required onchange="toggleAutreMotif(this)">
                            <option value="">Sélectionner un motif</option>
                            <option value="Acte non couvert">Acte non couvert par le contrat</option>
                            <option value="Plafond dépassé">Plafond annuel dépassé</option>
                            <option value="Délai de carence">Délai de carence non respecté</option>
                            <option value="Documents insuffisants">Documents justificatifs insuffisants</option>
                            <option value="Exclusion contractuelle">Exclusion contractuelle</option>
                            <option value="Hors parcours de soins">Non-respect du parcours de soins</option>
                            <option value="Autre">Autre motif</option>
                        </select>
                    </div>
                    <div class="form-group" id="autreMotifDiv" style="display: none;">
                        <label>Préciser le motif</label>
                        <textarea name="autre_motif" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-danger">Refuser la PEC</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Annuler PEC -->
<div class="modal fade" id="annulerModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="annulerForm" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Annuler la prise en charge</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i>
                        Cette action est irréversible. La PEC sera définitivement annulée.
                    </div>
                    <div class="form-group">
                        <label>Motif d'annulation <span class="text-danger">*</span></label>
                        <textarea name="motif_annulation" class="form-control" rows="3"
                                  required placeholder="Expliquer la raison de l'annulation..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Fermer</button>
                    <button type="submit" class="btn btn-warning">Confirmer l'annulation</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Initialiser Select2
    $('.select2').select2({
        theme: 'bootstrap4',
        width: '100%'
    });
});

function accepterPEC(pecId) {
    // Récupérer les infos de la PEC via AJAX
    $.get(`/pec/${pecId}`, function(data) {
        $('#montantDemande').val(formatMontant(data.montant_demande));
        $('#montantAccorde').val(data.montant_demande).attr('max', data.montant_demande);
        $('#accepterForm').attr('action', `/pec/${pecId}/accepter`);
        $('#accepterModal').modal('show');
    });
}

function refuserPEC(pecId) {
    $('#refuserForm').attr('action', `/pec/${pecId}/refuser`);
    $('#refuserModal').modal('show');
}

function annulerPEC(pecId) {
    $('#annulerForm').attr('action', `/pec/${pecId}/annuler`);
    $('#annulerModal').modal('show');
}

function toggleAutreMotif(select) {
    if (select.value === 'Autre') {
        $('#autreMotifDiv').show();
        $('[name="autre_motif"]').attr('required', true);
    } else {
        $('#autreMotifDiv').hide();
        $('[name="autre_motif"]').attr('required', false);
    }
}

function formatMontant(montant) {
    return new Intl.NumberFormat('fr-FR').format(montant) + ' FCFA';
}

function exportData() {
    const params = new URLSearchParams(window.location.search);
    window.location.href = "{{ route('pec.export') }}?" + params.toString();
}

function resetFilters() {
    window.location.href = "{{ route('pec.index') }}";
}
</script>
@endpush

@push('styles')
<style>
.border-left-primary {
    border-left: 4px solid #4e73df !important;
}
.border-left-success {
    border-left: 4px solid #1cc88a !important;
}
.border-left-info {
    border-left: 4px solid #36b9cc !important;
}
.border-left-warning {
    border-left: 4px solid #f6c23e !important;
}
.border-left-danger {
    border-left: 4px solid #e74a3b !important;
}
.border-left-secondary {
    border-left: 4px solid #858796 !important;
}
</style>
@endpush
