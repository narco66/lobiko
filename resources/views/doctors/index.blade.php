@extends('layouts.app')
@section('title', 'Médecins')
@section('content')
<div class="container py-4">
    <x-lobiko.page-header title="Médecins" subtitle="Gestion des praticiens" :actions="[
        ['type'=>'primary','url'=>route('admin.doctors.create'),'label'=>'Créer','icon'=>'plus']
    ]"/>
    <x-lobiko.ui.flash />

    <x-lobiko.tables.datatable>
        <x-slot name="head">
            <th>Matricule</th>
            <th>Nom</th>
            <th>Spécialité</th>
            <th>Statut</th>
            <th class="text-end">Actions</th>
        </x-slot>
        @foreach($doctors as $doctor)
            <tr>
                <td>{{ $doctor->matricule }}</td>
                <td>{{ $doctor->full_name }}</td>
                <td>{{ $doctor->specialty?->libelle ?? '—' }}</td>
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
        @endforeach
    </x-lobiko.tables.datatable>

    {{ $doctors->links() }}
</div>
@endsection
