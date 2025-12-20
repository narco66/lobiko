@php
use Illuminate\Support\Str;
@endphp

@extends('layouts.app')

@section('title', 'Pharmacies')

@section('content')
<div class="container py-4">
    <x-lobiko.page-header
        title="Pharmacies"
        subtitle="Gestion des officines et partenaires"
        :actions="[['type' => 'primary', 'url' => route('admin.pharmacies.create'), 'label' => 'Nouvelle pharmacie', 'icon' => 'plus']]"
    />

    <x-lobiko.ui.flash />

    <div class="card shadow-sm mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.pharmacies.index') }}" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label">Rechercher</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" name="search" class="form-control" placeholder="Nom, licence, adresse..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Statut</label>
                    <select name="statut" class="form-select">
                        <option value="">Tous</option>
                        <option value="active" {{ request('statut') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('statut') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        <option value="suspendue" {{ request('statut') == 'suspendue' ? 'selected' : '' }}>Suspendue</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Services</label>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="service_garde" value="1" {{ request('service_garde') ? 'checked' : '' }}>
                        <label class="form-check-label">Service de garde</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="livraison_disponible" value="1" {{ request('livraison_disponible') ? 'checked' : '' }}>
                        <label class="form-check-label">Livraison disponible</label>
                    </div>
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary w-100"><i class="fas fa-filter me-1"></i>Filtrer</button>
                    <a href="{{ route('admin.pharmacies.index') }}" class="btn btn-outline-secondary w-100"><i class="fas fa-redo me-1"></i>Réinitialiser</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <x-lobiko.tables.datatable>
                <x-slot name="head">
                    <th>Pharmacie</th>
                    <th>Licence</th>
                    <th>Responsable</th>
                    <th>Contact</th>
                    <th>Services</th>
                    <th>Stock</th>
                    <th>Statut</th>
                    <th class="text-end">Actions</th>
                </x-slot>
                @forelse($pharmacies as $pharmacie)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="avatar-sm bg-primary bg-opacity-10 rounded-circle me-3">
                                    <i class="fas fa-clinic-medical text-primary fs-6"></i>
                                </div>
                                <div>
                                    <div class="fw-semibold">{{ $pharmacie->nom_pharmacie }}</div>
                                    <small class="text-muted"><i class="fas fa-map-marker-alt me-1"></i>{{ Str::limit($pharmacie->adresse_complete, 40) }}</small>
                                </div>
                            </div>
                        </td>
                        <td><span class="badge bg-secondary">{{ $pharmacie->numero_licence }}</span></td>
                        <td>{{ $pharmacie->nom_responsable }}</td>
                        <td>
                            <div><i class="fas fa-phone text-muted me-1"></i>{{ $pharmacie->telephone_pharmacie }}</div>
                            @if($pharmacie->email_pharmacie)
                                <div><i class="fas fa-envelope text-muted me-1"></i>{{ $pharmacie->email_pharmacie }}</div>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex gap-1">
                                @if($pharmacie->service_garde)<span class="badge bg-warning" title="Service de garde"><i class="fas fa-moon"></i></span>@endif
                                @if($pharmacie->livraison_disponible)<span class="badge bg-info" title="Livraison disponible"><i class="fas fa-truck"></i></span>@endif
                                @if($pharmacie->paiement_mobile_money)<span class="badge bg-success" title="Mobile Money"><i class="fas fa-mobile-alt"></i></span>@endif
                                @if($pharmacie->paiement_carte)<span class="badge bg-primary" title="Carte bancaire"><i class="fas fa-credit-card"></i></span>@endif
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark">{{ $pharmacie->stocks_count ?? 0 }} produits</span>
                            @if(($pharmacie->alertes_count ?? 0) > 0)
                                <span class="badge bg-danger">{{ $pharmacie->alertes_count }} alertes</span>
                            @endif
                        </td>
                        <td>
                            <x-lobiko.ui.badge-status :status="$pharmacie->statut ?? 'inactive'"></x-lobiko.ui.badge-status>
                        </td>
                        <td class="text-end">
                            <a href="{{ route('admin.pharmacies.show', $pharmacie) }}" class="btn btn-sm btn-outline-primary"><i class="fas fa-eye"></i></a>
                            <a href="{{ route('admin.pharmacies.stocks', $pharmacie) }}" class="btn btn-sm btn-outline-success"><i class="fas fa-boxes"></i></a>
                            <a href="{{ route('admin.pharmacies.dashboard', $pharmacie) }}" class="btn btn-sm btn-outline-info"><i class="fas fa-chart-line"></i></a>
                            @can('update', $pharmacie)
                                <a href="{{ route('admin.pharmacies.edit', $pharmacie) }}" class="btn btn-sm btn-outline-secondary"><i class="fas fa-pen"></i></a>
                            @endcan
                            @can('delete', $pharmacie)
                                <form action="{{ route('admin.pharmacies.destroy', $pharmacie) }}" method="POST" class="d-inline" onsubmit="return confirm('Supprimer cette pharmacie ?');">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                                </form>
                            @endcan
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8">
                            <x-lobiko.ui.empty-state
                                title="Aucune pharmacie"
                                description="Ajoutez une pharmacie pour commencer."
                                :action="['label' => 'Nouvelle pharmacie', 'href' => route('admin.pharmacies.create'), 'icon' => 'fas fa-plus']"
                            />
                        </td>
                    </tr>
                @endforelse
            </x-lobiko.tables.datatable>
        </div>
    </div>

    <div class="mt-3">
        {{ $pharmacies->withQueryString()->links() }}
    </div>
</div>
@endsection
