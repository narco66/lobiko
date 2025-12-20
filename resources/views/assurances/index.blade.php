@extends('layouts.app')
@section('title', 'Assurances')
@section('content')
<div class="container py-4">
    <x-lobiko.page-header
        title="Assurances"
        subtitle="Compagnies et partenaires"
        :actions="[['type' => 'primary', 'url' => route('admin.assurances.create'), 'label' => 'Nouvel assureur', 'icon' => 'plus']]"
    />
    <x-lobiko.ui.flash />

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <form method="GET" action="{{ route('admin.assurances.index') }}" class="d-flex gap-2">
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-sm" placeholder="Nom, code, ville, email">
                    <button class="btn btn-outline-secondary btn-sm" type="submit"><i class="fas fa-search me-1"></i>Filtrer</button>
                </form>
                <div class="text-muted small">Total : {{ $assurances->total() }}</div>
            </div>

            <x-lobiko.tables.datatable>
                <x-slot name="head">
                    <th>Code</th>
                    <th>Nom</th>
                    <th>Type</th>
                    <th>Ville</th>
                    <th>Statut</th>
                    <th class="text-end">Actions</th>
                </x-slot>
                @forelse($assurances as $assurance)
                    <tr>
                        <td class="fw-semibold">{{ $assurance->code_assureur }}</td>
                        <td>{{ $assurance->nom_assureur }}</td>
                        <td>{{ ucfirst($assurance->type) }}</td>
                        <td>{{ $assurance->ville }}</td>
                        <td>
                            <x-lobiko.ui.badge-status :status="$assurance->actif ? 'actif' : 'suspendu'" />
                            @if($assurance->partenaire)
                                <span class="badge bg-info ms-1">Partenaire</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <a href="{{ route('admin.assurances.show', $assurance) }}" class="btn btn-sm btn-outline-primary"><i class="fas fa-eye"></i></a>
                            <a href="{{ route('admin.assurances.edit', $assurance) }}" class="btn btn-sm btn-outline-secondary"><i class="fas fa-pen"></i></a>
                            <form action="{{ route('admin.assurances.destroy', $assurance) }}" method="POST" class="d-inline" onsubmit="return confirm('Archiver cet assureur ?');">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6">
                            <x-lobiko.ui.empty-state
                                title="Aucune assurance"
                                description="Ajoutez une compagnie ou mutuelle pour commencer."
                                :action="['label' => 'Nouvel assureur', 'href' => route('admin.assurances.create'), 'icon' => 'fas fa-plus']"
                            />
                        </td>
                    </tr>
                @endforelse
            </x-lobiko.tables.datatable>

            <div class="mt-3">
                {{ $assurances->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
