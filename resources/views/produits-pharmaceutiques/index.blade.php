@php
use Illuminate\Support\Str;
@endphp
@extends('layouts.app')
@section('title', 'Produits pharmaceutiques')

@section('content')
<div class="container py-4">
    <x-lobiko.page-header
        title="Produits pharmaceutiques"
        subtitle="Gestion du catalogue"
        :actions="[['type' => 'primary', 'url' => route('admin.produits-pharmaceutiques.create'), 'label' => 'Nouveau produit', 'icon' => 'plus']]"
    />
    <x-lobiko.ui.flash />

    <div class="card shadow-sm mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.produits-pharmaceutiques.index') }}" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label">Rechercher</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" name="search" class="form-control" placeholder="Code, DCI, nom, classe..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Disponible</label>
                    <select name="disponible" class="form-select">
                        <option value="">Tous</option>
                        <option value="1" {{ request('disponible') === '1' ? 'selected' : '' }}>Oui</option>
                        <option value="0" {{ request('disponible') === '0' ? 'selected' : '' }}>Non</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Générique</label>
                    <select name="generique" class="form-select">
                        <option value="">Tous</option>
                        <option value="1" {{ request('generique') === '1' ? 'selected' : '' }}>Oui</option>
                        <option value="0" {{ request('generique') === '0' ? 'selected' : '' }}>Non</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary w-100"><i class="fas fa-filter me-1"></i>Filtrer</button>
                    <a href="{{ route('admin.produits-pharmaceutiques.index') }}" class="btn btn-outline-secondary w-100"><i class="fas fa-redo me-1"></i>Réinitialiser</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <x-lobiko.tables.datatable>
                <x-slot name="head">
                    <th>Code</th>
                    <th>Nom commercial</th>
                    <th>DCI</th>
                    <th>Classe</th>
                    <th>Prix unité</th>
                    <th>Statut</th>
                    <th class="text-end">Actions</th>
                </x-slot>
                @forelse($produits as $produit)
                    <tr>
                        <td class="fw-semibold">{{ $produit->code_produit }}</td>
                        <td>{{ $produit->nom_commercial }}</td>
                        <td>{{ $produit->dci }}</td>
                        <td>{{ $produit->classe_therapeutique }}</td>
                        <td>{{ number_format($produit->prix_unitaire ?? 0, 0, ',', ' ') }} FCFA</td>
                        <td>
                            <x-lobiko.ui.badge-status :status="$produit->disponible ? 'actif' : 'suspendu'"></x-lobiko.ui.badge-status>
                            @if($produit->generique)
                                <span class="badge bg-info ms-1">Générique</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <a href="{{ route('admin.produits-pharmaceutiques.show', $produit) }}" class="btn btn-sm btn-outline-primary"><i class="fas fa-eye"></i></a>
                            <a href="{{ route('admin.produits-pharmaceutiques.edit', $produit) }}" class="btn btn-sm btn-outline-secondary"><i class="fas fa-pen"></i></a>
                            <form action="{{ route('admin.produits-pharmaceutiques.destroy', $produit) }}" method="POST" class="d-inline" onsubmit="return confirm('Supprimer ce produit ?');">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7">
                            <x-lobiko.ui.empty-state
                                title="Aucun produit"
                                description="Ajoutez un produit pharmaceutique."
                                :action="['label' => 'Nouveau produit', 'href' => route('admin.produits-pharmaceutiques.create'), 'icon' => 'fas fa-plus']"
                            />
                        </td>
                    </tr>
                @endforelse
            </x-lobiko.tables.datatable>
        </div>
    </div>

    <div class="mt-3">
        {{ $produits->links() }}
    </div>
</div>
@endsection
