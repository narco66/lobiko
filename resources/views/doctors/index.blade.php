@extends('layouts.app')
@section('title', 'Médecins')
@section('content')
<div class="container py-4">
    <x-lobiko.page-header
        title="Médecins"
        subtitle="Gestion des praticiens"
        :actions="[['type' => 'primary', 'url' => route('admin.doctors.create'), 'label' => 'Nouveau médecin', 'icon' => 'plus']]"
    />
    <x-lobiko.ui.flash />

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <form method="GET" action="{{ route('admin.doctors.index') }}" class="d-flex gap-2">
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-sm" placeholder="Nom, email, téléphone, matricule">
                    <button class="btn btn-outline-secondary btn-sm" type="submit"><i class="fas fa-search me-1"></i>Filtrer</button>
                </form>
                <div class="text-muted small">Total : {{ $doctors->total() }}</div>
            </div>

            <x-lobiko.tables.datatable>
                <x-slot name="head">
                    <th>Matricule</th>
                    <th>Nom</th>
                    <th>Spécialités</th>
                    <th>Structures</th>
                    <th>Statut</th>
                    <th class="text-end">Actions</th>
                </x-slot>
                @forelse($doctors as $doctor)
                    <tr>
                        <td class="fw-semibold">{{ $doctor->matricule }}</td>
                        <td>{{ $doctor->full_name }}</td>
                        <td>{{ $doctor->specialties->pluck('libelle')->join(', ') ?: '-' }}</td>
                        <td>{{ $doctor->structures->pluck('nom_structure')->take(2)->join(', ') }}{{ $doctor->structures->count() > 2 ? ' +' : '' }}</td>
                        <td><x-lobiko.ui.badge-status :status="$doctor->statut" /></td>
                        <td class="text-end">
                            <a href="{{ route('admin.doctors.show', $doctor) }}" class="btn btn-sm btn-outline-primary"><i class="fas fa-eye"></i></a>
                            <a href="{{ route('admin.doctors.edit', $doctor) }}" class="btn btn-sm btn-outline-secondary"><i class="fas fa-pen"></i></a>
                            <form action="{{ route('admin.doctors.destroy', $doctor) }}" method="POST" class="d-inline" onsubmit="return confirm('Supprimer ce médecin ?');">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6">
                            <x-lobiko.ui.empty-state
                                title="Aucun médecin"
                                description="Ajoutez un praticien pour commencer."
                                :action="['label' => 'Nouveau médecin', 'href' => route('admin.doctors.create'), 'icon' => 'fas fa-plus']"
                            />
                        </td>
                    </tr>
                @endforelse
            </x-lobiko.tables.datatable>

            <div class="mt-3">
                {{ $doctors->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
