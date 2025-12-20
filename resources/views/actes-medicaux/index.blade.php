@extends('layouts.app')
@section('title', 'Actes médicaux')

@section('content')
<div class="container py-4">
    <x-lobiko.page-header
        title="Actes médicaux"
        subtitle="Catalogue des actes et tarifs"
        :actions="[['type' => 'primary', 'url' => route('admin.actes-medicaux.create'), 'label' => 'Nouvel acte', 'icon' => 'plus']]"
    />
    <x-lobiko.ui.flash />

    <div class="card shadow-sm mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.actes-medicaux.index') }}" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label">Rechercher</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" name="search" class="form-control" placeholder="Code, libellé, catégorie, spécialité" value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Catégorie</label>
                    <input type="text" name="categorie" class="form-control" placeholder="Ex: consultation" value="{{ request('categorie') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Spécialité</label>
                    <input type="text" name="specialite" class="form-control" placeholder="Ex: cardio" value="{{ request('specialite') }}">
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary w-100"><i class="fas fa-filter me-1"></i>Filtrer</button>
                    <a href="{{ route('admin.actes-medicaux.index') }}" class="btn btn-outline-secondary w-100"><i class="fas fa-redo me-1"></i>Réinitialiser</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <x-lobiko.tables.datatable>
                <x-slot name="head">
                    <th>Code</th>
                    <th>Libellé</th>
                    <th>Catégorie</th>
                    <th>Spécialité</th>
                    <th>Tarif base</th>
                    <th>Statut</th>
                    <th class="text-end">Actions</th>
                </x-slot>
                @forelse($actes as $acte)
                    <tr>
                        <td class="fw-semibold">{{ $acte->code_acte }}</td>
                        <td>{{ $acte->libelle }}</td>
                        <td>{{ $acte->categorie }}</td>
                        <td>{{ $acte->specialite ?? '-' }}</td>
                        <td>{{ number_format($acte->tarif_base ?? 0, 0, ',', ' ') }} FCFA</td>
                        <td><x-lobiko.ui.badge-status :status="$acte->actif ? 'actif' : 'suspendu'"/></td>
                        <td class="text-end">
                            <a href="{{ route('admin.actes-medicaux.show', $acte) }}" class="btn btn-sm btn-outline-primary"><i class="fas fa-eye"></i></a>
                            <a href="{{ route('admin.actes-medicaux.edit', $acte) }}" class="btn btn-sm btn-outline-secondary"><i class="fas fa-pen"></i></a>
                            <form action="{{ route('admin.actes-medicaux.destroy', $acte) }}" method="POST" class="d-inline" onsubmit="return confirm('Supprimer cet acte ?');">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7">
                            <x-lobiko.ui.empty-state
                                title="Aucun acte"
                                description="Ajoutez un acte médical."
                                :action="['label' => 'Nouvel acte', 'href' => route('admin.actes-medicaux.create'), 'icon' => 'fas fa-plus']"
                            />
                        </td>
                    </tr>
                @endforelse
            </x-lobiko.tables.datatable>
        </div>
    </div>

    <div class="mt-3">
        {{ $actes->links() }}
    </div>
</div>
@endsection
