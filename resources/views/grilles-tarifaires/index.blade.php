@extends('layouts.app')
@section('title', 'Grilles tarifaires')

@section('content')
<div class="container py-4">
    <x-lobiko.page-header
        title="Grilles tarifaires"
        subtitle="Tarifs par type de client et zone"
        :actions="[['type' => 'primary', 'url' => route('admin.grilles-tarifaires.create'), 'label' => 'Nouvelle grille', 'icon' => 'plus']]"
    />
    <x-lobiko.ui.flash />

    <div class="card shadow-sm mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.grilles-tarifaires.index') }}" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label">Rechercher</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" name="search" class="form-control" placeholder="Nom, type client, zone..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Zone</label>
                    <select name="zone" class="form-select">
                        <option value="">Toutes</option>
                        <option value="urbain" {{ request('zone') === 'urbain' ? 'selected' : '' }}>Urbain</option>
                        <option value="rural" {{ request('zone') === 'rural' ? 'selected' : '' }}>Rural</option>
                        <option value="periurbain" {{ request('zone') === 'periurbain' ? 'selected' : '' }}>Périurbain</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Type client</label>
                    <select name="type_client" class="form-select">
                        <option value="">Tous</option>
                        <option value="public" {{ request('type_client') === 'public' ? 'selected' : '' }}>Public</option>
                        <option value="prive" {{ request('type_client') === 'prive' ? 'selected' : '' }}>Privé</option>
                        <option value="assure" {{ request('type_client') === 'assure' ? 'selected' : '' }}>Assuré</option>
                        <option value="indigent" {{ request('type_client') === 'indigent' ? 'selected' : '' }}>Indigent</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary w-100"><i class="fas fa-filter me-1"></i>Filtrer</button>
                    <a href="{{ route('admin.grilles-tarifaires.index') }}" class="btn btn-outline-secondary w-100"><i class="fas fa-redo me-1"></i>Réinitialiser</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <x-lobiko.tables.datatable>
                <x-slot name="head">
                    <th>Nom</th>
                    <th>Type client</th>
                    <th>Zone</th>
                    <th>Structure</th>
                    <th>Validité</th>
                    <th>Statut</th>
                    <th class="text-end">Actions</th>
                </x-slot>
                @forelse($grilles as $grille)
                    <tr>
                        <td class="fw-semibold">{{ $grille->nom_grille }}</td>
                        <td>{{ ucfirst($grille->type_client) }}</td>
                        <td>{{ ucfirst($grille->zone) }}</td>
                        <td>{{ $grille->structure?->nom_structure ?? 'Générale' }}</td>
                        <td>{{ optional($grille->date_debut)->format('d/m/Y') }} - {{ optional($grille->date_fin)->format('d/m/Y') ?? 'N/A' }}</td>
                        <td><x-lobiko.ui.badge-status :status="$grille->actif ? 'actif' : 'suspendu'"/></td>
                        <td class="text-end">
                            <a href="{{ route('admin.grilles-tarifaires.show', $grille) }}" class="btn btn-sm btn-outline-primary"><i class="fas fa-eye"></i></a>
                            <a href="{{ route('admin.grilles-tarifaires.edit', $grille) }}" class="btn btn-sm btn-outline-secondary"><i class="fas fa-pen"></i></a>
                            <form action="{{ route('admin.grilles-tarifaires.destroy', $grille) }}" method="POST" class="d-inline" onsubmit="return confirm('Supprimer cette grille ?');">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7">
                            <x-lobiko.ui.empty-state
                                title="Aucune grille"
                                description="Ajoutez une grille tarifaire."
                                :action="['label' => 'Nouvelle grille', 'href' => route('admin.grilles-tarifaires.create'), 'icon' => 'fas fa-plus']"
                            />
                        </td>
                    </tr>
                @endforelse
            </x-lobiko.tables.datatable>
        </div>
    </div>

    <div class="mt-3">
        {{ $grilles->links() }}
    </div>
</div>
@endsection
