@extends('layouts.app')

@section('title', 'Patients')
@section('content')
<div class="container py-4">
    <x-lobiko.page-header
        title="Patients"
        subtitle="Gestion des patients"
        :actions="[['type' => 'primary', 'url' => route('patients.create'), 'label' => 'Nouveau patient', 'icon' => 'plus']]"
    />

    <x-lobiko.ui.flash />

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <div class="text-muted small">Total : {{ $patients->total() }}</div>
                </div>
                <div class="d-flex gap-2">
                    <form method="GET" action="{{ route('patients.index') }}" class="d-flex gap-2">
                        <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-sm" placeholder="Nom, email, tel...">
                        <button class="btn btn-outline-secondary btn-sm" type="submit"><i class="fas fa-search me-1"></i>Filtrer</button>
                    </form>
                </div>
            </div>

            <x-lobiko.tables.datatable>
                <x-slot name="head">
                    <th>Nom</th>
                    <th>Email</th>
                    <th>Telephone</th>
                    <th>Ville</th>
                    <th>Statut</th>
                    <th class="text-end">Actions</th>
                </x-slot>
                @forelse($patients as $patient)
                    <tr>
                        <td class="fw-semibold">{{ $patient->prenom }} {{ $patient->nom }}</td>
                        <td>{{ $patient->email }}</td>
                        <td>{{ $patient->telephone }}</td>
                        <td>{{ $patient->adresse_ville }}</td>
                        <td><x-lobiko.ui.badge-status :status="$patient->statut_compte ?? 'actif'" /></td>
                        <td class="text-end">
                            <a href="{{ route('patients.show', $patient) }}" class="btn btn-sm btn-outline-primary"><i class="fas fa-eye"></i></a>
                            @can('update', $patient)
                                <a href="{{ route('patients.edit', $patient) }}" class="btn btn-sm btn-outline-secondary"><i class="fas fa-pen"></i></a>
                            @endcan
                            @can('delete', $patient)
                                <form action="{{ route('patients.destroy', $patient) }}" method="POST" class="d-inline" onsubmit="return confirm('Archiver ce patient ?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                                </form>
                            @endcan
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6">
                            <x-lobiko.ui.empty-state
                                title="Aucun patient"
                                description="Ajoutez un premier patient pour commencer."
                                :action="['label' => 'Nouveau patient', 'href' => route('patients.create'), 'icon' => 'fas fa-plus']"
                            />
                        </td>
                    </tr>
                @endforelse
            </x-lobiko.tables.datatable>

            <div class="mt-3">
                {{ $patients->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
