@extends('layouts.app')
@section('title', 'Fournisseurs pharmaceutiques')

@section('content')
<div class="container py-4">
    <x-lobiko.page-header
        title="Fournisseurs"
        subtitle="Catalogue des fournisseurs pharmaceutiques"
        :actions="[['type' => 'primary', 'url' => route('admin.fournisseurs-pharmaceutiques.create'), 'label' => 'Nouveau fournisseur', 'icon' => 'plus']]"
    />
    <x-lobiko.ui.flash />

    <div class="card shadow-sm mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.fournisseurs-pharmaceutiques.index') }}" class="row g-3 align-items-end">
                <div class="col-md-5">
                    <label class="form-label">Rechercher</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" name="search" class="form-control" placeholder="Nom, licence, téléphone, email..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Statut</label>
                    <select name="statut" class="form-select">
                        <option value="">Tous</option>
                        <option value="actif" {{ request('statut') === 'actif' ? 'selected' : '' }}>Actif</option>
                        <option value="inactif" {{ request('statut') === 'inactif' ? 'selected' : '' }}>Inactif</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary w-100"><i class="fas fa-filter me-1"></i>Filtrer</button>
                    <a href="{{ route('admin.fournisseurs-pharmaceutiques.index') }}" class="btn btn-outline-secondary w-100"><i class="fas fa-redo me-1"></i>Réinitialiser</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <x-lobiko.tables.datatable>
                <x-slot name="head">
                    <th>Nom</th>
                    <th>Licence</th>
                    <th>Contact</th>
                    <th>Catégories</th>
                    <th>Statut</th>
                    <th class="text-end">Actions</th>
                </x-slot>
                @forelse($fournisseurs as $fournisseur)
                    <tr>
                        <td class="fw-semibold">{{ $fournisseur->nom_fournisseur }}</td>
                        <td><span class="badge bg-secondary">{{ $fournisseur->numero_licence }}</span></td>
                        <td>
                            <div><i class="fas fa-phone text-muted me-1"></i>{{ $fournisseur->telephone }}</div>
                            @if($fournisseur->email)
                                <div><i class="fas fa-envelope text-muted me-1"></i>{{ $fournisseur->email }}</div>
                            @endif
                        </td>
                        <td>{{ $fournisseur->categories_produits ? implode(', ', $fournisseur->categories_produits) : '-' }}</td>
                        <td><x-lobiko.ui.badge-status :status="$fournisseur->statut === 'actif' ? 'actif' : 'suspendu'"/></td>
                        <td class="text-end">
                            <a href="{{ route('admin.fournisseurs-pharmaceutiques.show', $fournisseur) }}" class="btn btn-sm btn-outline-primary"><i class="fas fa-eye"></i></a>
                            <a href="{{ route('admin.fournisseurs-pharmaceutiques.edit', $fournisseur) }}" class="btn btn-sm btn-outline-secondary"><i class="fas fa-pen"></i></a>
                            <form action="{{ route('admin.fournisseurs-pharmaceutiques.destroy', $fournisseur) }}" method="POST" class="d-inline" onsubmit="return confirm('Supprimer ce fournisseur ?');">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6">
                            <x-lobiko.ui.empty-state
                                title="Aucun fournisseur"
                                description="Ajoutez un fournisseur pharmaceutique."
                                :action="['label' => 'Nouveau fournisseur', 'href' => route('admin.fournisseurs-pharmaceutiques.create'), 'icon' => 'fas fa-plus']"
                            />
                        </td>
                    </tr>
                @endforelse
            </x-lobiko.tables.datatable>
        </div>
    </div>

    <div class="mt-3">
        {{ $fournisseurs->links() }}
    </div>
</div>
@endsection
