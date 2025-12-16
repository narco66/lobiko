@extends('layouts.app')

@section('title', 'Gestion des Ordonnances')

@section('content')
<div class="container-fluid">
    <!-- Header avec statistiques -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h3 mb-0">
                    <i class="fas fa-prescription"></i> Ordonnances
                </h1>
                @can('ordonnances.create')
                <a href="{{ route('ordonnances.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Nouvelle Ordonnance
                </a>
                @endcan
            </div>
        </div>
    </div>

    <!-- Cartes de statistiques -->
    <div class="row mb-4">
        <div class="col-md-3 col-sm-6 mb-3">
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
                            <i class="fas fa-file-medical fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Actives
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($stats['actives'] ?? 0) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Dispensées
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($stats['dispensees'] ?? 0) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-pills fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Expirées
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($stats['expirees'] ?? 0) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtres -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-filter"></i> Filtres
            </h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('ordonnances.index') }}" id="filterForm">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label for="statut">Statut</label>
                        <select name="statut" id="statut" class="form-control">
                            <option value="">Tous</option>
                            <option value="active" {{ request('statut') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="dispensee" {{ request('statut') == 'dispensee' ? 'selected' : '' }}>Dispensée</option>
                            <option value="partiellement_dispensee" {{ request('statut') == 'partiellement_dispensee' ? 'selected' : '' }}>Partiellement dispensée</option>
                            <option value="expiree" {{ request('statut') == 'expiree' ? 'selected' : '' }}>Expirée</option>
                            <option value="annulee" {{ request('statut') == 'annulee' ? 'selected' : '' }}>Annulée</option>
                        </select>
                    </div>

                    @if(auth()->user()->hasRole(['admin', 'praticien']))
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

                    @if(auth()->user()->hasRole(['admin', 'patient']))
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
                               placeholder="N° ordonnance, diagnostic, patient..."
                               value="{{ request('search') }}">
                    </div>

                    <div class="col-md-12">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i> Rechercher
                        </button>
                        <a href="{{ route('ordonnances.index') }}" class="btn btn-secondary">
                            <i class="fas fa-redo"></i> Réinitialiser
                        </a>
                        @can('ordonnances.export')
                        <button type="button" class="btn btn-success" onclick="exportData()">
                            <i class="fas fa-file-excel"></i> Exporter
                        </button>
                        @endcan
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Tableau des ordonnances -->
    <div class="card shadow">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                Liste des Ordonnances ({{ $ordonnances->total() }} résultats)
            </h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="ordonnancesTable">
                    <thead>
                        <tr>
                            <th>N° Ordonnance</th>
                            <th>Date</th>
                            <th>Patient</th>
                            <th>Praticien</th>
                            <th>Diagnostic</th>
                            <th>Médicaments</th>
                            <th>Statut</th>
                            <th>Validité</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($ordonnances as $ordonnance)
                        <tr>
                            <td>
                                <strong>{{ $ordonnance->numero_ordonnance }}</strong>
                                @if($ordonnance->type_ordonnance == 'secure')
                                    <span class="badge badge-danger">Sécurisée</span>
                                @endif
                            </td>
                            <td>{{ $ordonnance->date_ordonnance->format('d/m/Y H:i') }}</td>
                            <td>
                                <a href="{{ route('patients.show', $ordonnance->patient_id) }}">
                                    {{ $ordonnance->patient->nom }} {{ $ordonnance->patient->prenom }}
                                </a>
                            </td>
                            <td>
                                Dr. {{ $ordonnance->praticien->nom }} {{ $ordonnance->praticien->prenom }}
                            </td>
                            <td>{{ Str::limit($ordonnance->diagnostic, 30) }}</td>
                            <td>
                                <span class="badge badge-info">{{ $ordonnance->lignes->count() }} médicament(s)</span>
                            </td>
                            <td>{!! $ordonnance->statut_badge !!}</td>
                            <td>
                                @if($ordonnance->date_expiration)
                                    @if($ordonnance->date_expiration < now())
                                        <span class="text-danger">Expirée</span>
                                    @else
                                        {{ $ordonnance->date_expiration->format('d/m/Y') }}
                                        <small class="text-muted">({{ $ordonnance->date_expiration->diffInDays() }}j)</small>
                                    @endif
                                @else
                                    <span class="text-muted">Illimitée</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('ordonnances.show', $ordonnance) }}"
                                       class="btn btn-sm btn-info" title="Voir">
                                        <i class="fas fa-eye"></i>
                                    </a>

                                    @can('update', $ordonnance)
                                        @if($ordonnance->statut != 'dispensee')
                                        <a href="{{ route('ordonnances.edit', $ordonnance) }}"
                                           class="btn btn-sm btn-warning" title="Modifier">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        @endif
                                    @endcan

                                    <a href="{{ route('ordonnances.pdf', $ordonnance) }}"
                                       class="btn btn-sm btn-secondary" title="PDF" target="_blank">
                                        <i class="fas fa-file-pdf"></i>
                                    </a>

                                    <a href="{{ route('ordonnances.print', $ordonnance) }}"
                                       class="btn btn-sm btn-light" title="Imprimer" target="_blank">
                                        <i class="fas fa-print"></i>
                                    </a>

                                    @if($ordonnance->peutEtreRenouvelee())
                                    <form action="{{ route('ordonnances.renouveler', $ordonnance) }}"
                                          method="POST" style="display: inline;">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success"
                                                title="Renouveler"
                                                onclick="return confirm('Renouveler cette ordonnance ?')">
                                            <i class="fas fa-redo"></i>
                                        </button>
                                    </form>
                                    @endif

                                    @can('dispenser', $ordonnance)
                                        @if($ordonnance->statut == 'active')
                                        <button type="button" class="btn btn-sm btn-primary"
                                                title="Dispenser"
                                                onclick="dispenserOrdonnance({{ $ordonnance->id }})">
                                            <i class="fas fa-pills"></i>
                                        </button>
                                        @endif
                                    @endcan

                                    @can('delete', $ordonnance)
                                        @if($ordonnance->statut != 'dispensee')
                                        <form action="{{ route('ordonnances.destroy', $ordonnance) }}"
                                              method="POST" style="display: inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger"
                                                    title="Supprimer"
                                                    onclick="return confirm('Supprimer cette ordonnance ?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                        @endif
                                    @endcan
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center py-4">
                                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                <p class="text-muted">Aucune ordonnance trouvée</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-4">
                {{ $ordonnances->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Modal Dispensation -->
<div class="modal fade" id="dispenserModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form id="dispenserForm" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Dispenser l'ordonnance</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="dispenserContent">
                    <!-- Contenu chargé dynamiquement -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Valider la dispensation</button>
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

function dispenserOrdonnance(ordonnanceId) {
    // Charger le formulaire de dispensation
    $.get(`/ordonnances/${ordonnanceId}/dispenser-form`, function(data) {
        $('#dispenserContent').html(data);
        $('#dispenserForm').attr('action', `/ordonnances/${ordonnanceId}/dispenser`);
        $('#dispenserModal').modal('show');
    });
}

function exportData() {
    const params = new URLSearchParams(window.location.search);
    window.location.href = "{{ route('ordonnances.export') }}?" + params.toString();
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
</style>
@endpush
