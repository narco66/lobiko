@extends('layouts.app')

@section('title', 'Gestion des Pharmacies')

@section('content')
<div class="container-fluid">
    {{-- En-tête --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h1 class="h3 mb-0">
                                <i class="fas fa-pills text-primary me-2"></i>
                                Gestion des Pharmacies
                            </h1>
                            <p class="text-muted mb-0">{{ $pharmacies->total() }} pharmacies enregistrées</p>
                        </div>
                        @can('create', App\Models\Pharmacie::class)
                        <a href="{{ route('pharmacies.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>
                            Nouvelle Pharmacie
                        </a>
                        @endcan
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Filtres --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <form method="GET" action="{{ route('pharmacies.index') }}" class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Rechercher</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-search"></i>
                                </span>
                                <input type="text"
                                       name="search"
                                       class="form-control"
                                       placeholder="Nom, licence, adresse..."
                                       value="{{ request('search') }}">
                            </div>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">Statut</label>
                            <select name="statut" class="form-select">
                                <option value="">Tous</option>
                                <option value="active" {{ request('statut') == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ request('statut') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                <option value="suspendue" {{ request('statut') == 'suspendue' ? 'selected' : '' }}>Suspendue</option>
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">Services</label>
                            <div class="form-check">
                                <input class="form-check-input"
                                       type="checkbox"
                                       name="service_garde"
                                       value="1"
                                       {{ request('service_garde') ? 'checked' : '' }}>
                                <label class="form-check-label">
                                    Service de garde
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input"
                                       type="checkbox"
                                       name="livraison_disponible"
                                       value="1"
                                       {{ request('livraison_disponible') ? 'checked' : '' }}>
                                <label class="form-check-label">
                                    Livraison disponible
                                </label>
                            </div>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">Localisation</label>
                            <button type="button"
                                    class="btn btn-outline-primary w-100"
                                    onclick="getLocation()">
                                <i class="fas fa-map-marker-alt me-2"></i>
                                Près de moi
                            </button>
                            <input type="hidden" name="latitude" id="latitude" value="{{ request('latitude') }}">
                            <input type="hidden" name="longitude" id="longitude" value="{{ request('longitude') }}">
                            <input type="hidden" name="rayon" id="rayon" value="{{ request('rayon', 10) }}">
                        </div>

                        <div class="col-md-3 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary me-2">
                                <i class="fas fa-filter me-2"></i>
                                Filtrer
                            </button>
                            <a href="{{ route('pharmacies.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-redo me-2"></i>
                                Réinitialiser
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Liste des pharmacies --}}
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Pharmacie</th>
                                    <th>Licence</th>
                                    <th>Responsable</th>
                                    <th>Contact</th>
                                    <th>Services</th>
                                    <th>Stock</th>
                                    <th>Statut</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($pharmacies as $pharmacie)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm bg-primary bg-opacity-10 rounded-circle me-3">
                                                <i class="fas fa-clinic-medical text-primary fs-5"></i>
                                            </div>
                                            <div>
                                                <h6 class="mb-0">{{ $pharmacie->nom_pharmacie }}</h6>
                                                <small class="text-muted">
                                                    <i class="fas fa-map-marker-alt me-1"></i>
                                                    {{ Str::limit($pharmacie->adresse_complete, 40) }}
                                                </small>
                                                @if(isset($pharmacie->distance))
                                                <span class="badge bg-info ms-2">
                                                    {{ number_format($pharmacie->distance, 1) }} km
                                                </span>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary">
                                            {{ $pharmacie->numero_licence }}
                                        </span>
                                    </td>
                                    <td>{{ $pharmacie->nom_responsable }}</td>
                                    <td>
                                        <div>
                                            <i class="fas fa-phone text-muted me-1"></i>
                                            {{ $pharmacie->telephone_pharmacie }}
                                        </div>
                                        @if($pharmacie->email_pharmacie)
                                        <div>
                                            <i class="fas fa-envelope text-muted me-1"></i>
                                            {{ $pharmacie->email_pharmacie }}
                                        </div>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex gap-1">
                                            @if($pharmacie->service_garde)
                                            <span class="badge bg-warning" title="Service de garde">
                                                <i class="fas fa-moon"></i>
                                            </span>
                                            @endif
                                            @if($pharmacie->livraison_disponible)
                                            <span class="badge bg-info" title="Livraison disponible">
                                                <i class="fas fa-truck"></i>
                                            </span>
                                            @endif
                                            @if($pharmacie->paiement_mobile_money)
                                            <span class="badge bg-success" title="Mobile Money">
                                                <i class="fas fa-mobile-alt"></i>
                                            </span>
                                            @endif
                                            @if($pharmacie->paiement_carte)
                                            <span class="badge bg-primary" title="Carte bancaire">
                                                <i class="fas fa-credit-card"></i>
                                            </span>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            <span class="badge bg-light text-dark">
                                                {{ $pharmacie->stocks_count ?? 0 }} produits
                                            </span>
                                        </div>
                                        @if($pharmacie->alertes_count > 0)
                                        <span class="badge bg-danger">
                                            {{ $pharmacie->alertes_count }} alertes
                                        </span>
                                        @endif
                                    </td>
                                    <td>
                                        @switch($pharmacie->statut)
                                            @case('active')
                                                <span class="badge bg-success">
                                                    <i class="fas fa-check-circle me-1"></i>
                                                    Active
                                                </span>
                                                @break
                                            @case('inactive')
                                                <span class="badge bg-secondary">
                                                    <i class="fas fa-pause-circle me-1"></i>
                                                    Inactive
                                                </span>
                                                @break
                                            @case('suspendue')
                                                <span class="badge bg-danger">
                                                    <i class="fas fa-ban me-1"></i>
                                                    Suspendue
                                                </span>
                                                @break
                                        @endswitch
                                    </td>
                                    <td>
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-light"
                                                    type="button"
                                                    data-bs-toggle="dropdown">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li>
                                                    <a class="dropdown-item"
                                                       href="{{ route('pharmacies.show', $pharmacie) }}">
                                                        <i class="fas fa-eye me-2"></i>
                                                        Voir détails
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item"
                                                       href="{{ route('pharmacies.stocks', $pharmacie) }}">
                                                        <i class="fas fa-boxes me-2"></i>
                                                        Gérer stock
                                                    </a>
                                                </li>
                                                @can('update', $pharmacie)
                                                <li>
                                                    <a class="dropdown-item"
                                                       href="{{ route('pharmacies.edit', $pharmacie) }}">
                                                        <i class="fas fa-edit me-2"></i>
                                                        Modifier
                                                    </a>
                                                </li>
                                                @endcan
                                                <li>
                                                    <a class="dropdown-item"
                                                       href="{{ route('pharmacies.dashboard', $pharmacie) }}">
                                                        <i class="fas fa-chart-line me-2"></i>
                                                        Dashboard
                                                    </a>
                                                </li>
                                                @can('delete', $pharmacie)
                                                <li><hr class="dropdown-divider"></li>
                                                <li>
                                                    <form action="{{ route('pharmacies.destroy', $pharmacie) }}"
                                                          method="POST"
                                                          onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette pharmacie ?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="dropdown-item text-danger">
                                                            <i class="fas fa-trash me-2"></i>
                                                            Supprimer
                                                        </button>
                                                    </form>
                                                </li>
                                                @endcan
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center py-5">
                                        <div class="text-muted">
                                            <i class="fas fa-inbox fa-3x mb-3"></i>
                                            <p>Aucune pharmacie trouvée</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Pagination --}}
    @if($pharmacies->hasPages())
    <div class="row mt-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    Affichage de {{ $pharmacies->firstItem() }} à {{ $pharmacies->lastItem() }}
                    sur {{ $pharmacies->total() }} résultats
                </div>
                <div>
                    {{ $pharmacies->withQueryString()->links() }}
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

@push('styles')
<style>
    .avatar-sm {
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
</style>
@endpush

@push('scripts')
<script>
function getLocation() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(position) {
            document.getElementById('latitude').value = position.coords.latitude;
            document.getElementById('longitude').value = position.coords.longitude;
            document.querySelector('form').submit();
        }, function(error) {
            alert('Impossible d\'obtenir votre position: ' + error.message);
        });
    } else {
        alert('La géolocalisation n\'est pas supportée par votre navigateur');
    }
}
</script>
@endpush
@endsection
