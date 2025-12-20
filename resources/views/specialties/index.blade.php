@extends('layouts.app')
@section('title','Spécialités')

@section('content')
<div class="container py-4">
    <x-lobiko.page-header
        title="Spécialités"
        subtitle="Catalogue des spécialités"
        :actions="[['type'=>'primary','url'=>route('admin.specialties.create'),'label'=>'Créer','icon'=>'plus']]"
    />
    <x-lobiko.ui.flash />

    <div class="card shadow-sm mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.specialties.index') }}" class="row g-3 align-items-end">
                <div class="col-md-6">
                    <label class="form-label">Recherche</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="Code ou libellé">
                    </div>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Actif</label>
                    <select name="actif" class="form-select">
                        <option value="">Toutes</option>
                        <option value="1" @selected(request('actif')==='1')>Oui</option>
                        <option value="0" @selected(request('actif')==='0')>Non</option>
                    </select>
                </div>
                <div class="col-md-3 text-end">
                    <button class="btn btn-primary" type="submit"><i class="fas fa-filter me-1"></i>Filtrer</button>
                    <a href="{{ route('admin.specialties.index') }}" class="btn btn-outline-secondary ms-2"><i class="fas fa-redo me-1"></i>Réinitialiser</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <x-lobiko.tables.datatable>
                <x-slot name="head">
                    <th>Code</th><th>Libellé</th><th>Actif</th><th class="text-end">Actions</th>
                </x-slot>
                @forelse($specialties as $specialty)
                    <tr>
                        <td>{{ $specialty->code }}</td>
                        <td>{{ $specialty->libelle }}</td>
                        <td><x-lobiko.ui.badge-status :status="$specialty->actif ? 'actif' : 'inactif'"/></td>
                        <td class="text-end">
                            <a href="{{ route('admin.specialties.edit', $specialty) }}" class="btn btn-sm btn-outline-secondary"><i class="fas fa-pen"></i></a>
                            <form action="{{ route('admin.specialties.destroy', $specialty) }}" method="POST" class="d-inline" onsubmit="return confirm('Supprimer ?');">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4">
                            <x-lobiko.ui.empty-state
                                title="Aucune spécialité"
                                description="Ajoutez une spécialité."
                                :action="['label' => 'Créer', 'href' => route('admin.specialties.create'), 'icon' => 'fas fa-plus']"
                            />
                        </td>
                    </tr>
                @endforelse
            </x-lobiko.tables.datatable>
        </div>
    </div>

    <div class="mt-3">
        {{ $specialties->links() }}
    </div>
</div>
@endsection
