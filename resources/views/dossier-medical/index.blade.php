@extends('layouts.app')
@section('title', 'Dossiers médicaux')

@section('content')
<div class="container py-4">
    <x-lobiko.page-header
        title="Dossiers médicaux"
        subtitle="Suivi des DME"
        :actions="[['type' => 'primary', 'url' => route('dossiers-medicaux.create'), 'label' => 'Créer', 'icon' => 'plus']]"
    />

    <div class="card shadow-sm mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('dossiers-medicaux.index') }}" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label">Recherche patient</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="Nom ou email">
                    </div>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Numéro dossier</label>
                    <input type="text" name="numero" value="{{ request('numero') }}" class="form-control" placeholder="DEV-...">
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button class="btn btn-primary w-100" type="submit"><i class="fas fa-filter me-1"></i>Filtrer</button>
                    <a href="{{ route('dossiers-medicaux.index') }}" class="btn btn-outline-secondary w-100"><i class="fas fa-redo me-1"></i>Réinitialiser</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <x-lobiko.tables.datatable>
                <x-slot name="head">
                    <th>Numéro</th>
                    <th>Patient</th>
                    <th>Consultations</th>
                    <th>Dernière consultation</th>
                    <th class="text-end">Actions</th>
                </x-slot>
                @forelse($dossiers as $dossier)
                    <tr>
                        <td>{{ $dossier->numero_dossier }}</td>
                        <td>{{ $dossier->patient?->name ?? '-' }}</td>
                        <td>{{ $dossier->nombre_consultations ?? 0 }}</td>
                        <td>{{ optional($dossier->derniere_consultation)->format('d/m/Y H:i') ?? 'N/A' }}</td>
                        <td class="text-end">
                            <a href="{{ route('dossiers-medicaux.show', $dossier) }}" class="btn btn-sm btn-outline-primary"><i class="fas fa-eye"></i></a>
                            @can('update', $dossier)
                                <a href="{{ route('dossiers-medicaux.edit', $dossier) }}" class="btn btn-sm btn-outline-secondary"><i class="fas fa-pen"></i></a>
                            @endcan
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5">
                            <x-lobiko.ui.empty-state
                                title="Aucun dossier"
                                description="Créez le premier dossier médical."
                                :action="['label' => 'Créer', 'href' => route('dossiers-medicaux.create'), 'icon' => 'fas fa-plus']"
                            />
                        </td>
                    </tr>
                @endforelse
            </x-lobiko.tables.datatable>
        </div>
    </div>

    <div class="mt-3">
        {{ $dossiers->links() }}
    </div>
</div>
@endsection
