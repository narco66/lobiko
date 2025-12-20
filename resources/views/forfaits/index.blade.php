@extends('layouts.app')
@section('title', 'Forfaits / packs')

@section('content')
<div class="container py-4">
    <x-lobiko.page-header
        title="Forfaits / packs"
        subtitle="Offres et programmes de soins"
        :actions="[['type' => 'primary', 'url' => route('admin.forfaits.create'), 'label' => 'Nouveau forfait', 'icon' => 'plus']]"
    />
    <x-lobiko.ui.flash />

    <div class="card shadow-sm mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.forfaits.index') }}" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label">Rechercher</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" name="search" class="form-control" placeholder="Code, nom, catégorie" value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Catégorie</label>
                    <input type="text" name="categorie" class="form-control" placeholder="Ex: suivi_grossesse" value="{{ request('categorie') }}">
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary w-100"><i class="fas fa-filter me-1"></i>Filtrer</button>
                    <a href="{{ route('admin.forfaits.index') }}" class="btn btn-outline-secondary w-100"><i class="fas fa-redo me-1"></i>Réinitialiser</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <x-lobiko.tables.datatable>
                <x-slot name="head">
                    <th>Code</th>
                    <th>Nom</th>
                    <th>Catégorie</th>
                    <th>Prix</th>
                    <th>Durée</th>
                    <th>Statut</th>
                    <th class="text-end">Actions</th>
                </x-slot>
                @forelse($forfaits as $forfait)
                    <tr>
                        <td class="fw-semibold">{{ $forfait->code_forfait }}</td>
                        <td>{{ $forfait->nom_forfait }}</td>
                        <td>{{ $forfait->categorie }}</td>
                        <td>{{ number_format($forfait->prix_forfait ?? 0, 0, ',', ' ') }} FCFA</td>
                        <td>{{ $forfait->duree_validite ?? '-' }} j</td>
                        <td><x-lobiko.ui.badge-status :status="$forfait->actif ? 'actif' : 'suspendu'"/></td>
                        <td class="text-end">
                            <a href="{{ route('admin.forfaits.show', $forfait) }}" class="btn btn-sm btn-outline-primary"><i class="fas fa-eye"></i></a>
                            <a href="{{ route('admin.forfaits.edit', $forfait) }}" class="btn btn-sm btn-outline-secondary"><i class="fas fa-pen"></i></a>
                            <form action="{{ route('admin.forfaits.destroy', $forfait) }}" method="POST" class="d-inline" onsubmit="return confirm('Supprimer ce forfait ?');">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7">
                            <x-lobiko.ui.empty-state
                                title="Aucun forfait"
                                description="Ajoutez un forfait ou pack."
                                :action="['label' => 'Nouveau forfait', 'href' => route('admin.forfaits.create'), 'icon' => 'fas fa-plus']"
                            />
                        </td>
                    </tr>
                @endforelse
            </x-lobiko.tables.datatable>
        </div>
    </div>

    <div class="mt-3">
        {{ $forfaits->links() }}
    </div>
</div>
@endsection
