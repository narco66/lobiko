@extends('layouts.app')

@section('title', 'Structures médicales')
@section('content')
<div class="container py-4">
    <x-lobiko.page-header title="Structures médicales" subtitle="Gestion des établissements" :actions="[
        ['type' => 'primary', 'url' => route('admin.structures.create'), 'label' => 'Créer', 'icon' => 'plus']
    ]"/>

    <x-lobiko.ui.flash />

    <x-lobiko.tables.datatable>
        <x-slot name="head">
            <th>Code</th>
            <th>Nom</th>
            <th>Type</th>
            <th>Ville</th>
            <th>Statut</th>
            <th class="text-end">Actions</th>
        </x-slot>
        @foreach($structures as $structure)
            <tr>
                <td>{{ $structure->code_structure }}</td>
                <td>{{ $structure->nom_structure }}</td>
                <td>{{ ucfirst($structure->type_structure) }}</td>
                <td>{{ $structure->adresse_ville }}</td>
                <td><x-lobiko.ui.badge-status :status="$structure->statut" /></td>
                <td class="text-end">
                    <a href="{{ route('admin.structures.show', $structure) }}" class="btn btn-sm btn-outline-primary"><i class="fas fa-eye"></i></a>
                    <a href="{{ route('admin.structures.edit', $structure) }}" class="btn btn-sm btn-outline-secondary"><i class="fas fa-pen"></i></a>
                    <form action="{{ route('admin.structures.destroy', $structure) }}" method="POST" class="d-inline" onsubmit="return confirm('Supprimer cette structure ?');">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                    </form>
                </td>
            </tr>
        @endforeach
    </x-lobiko.tables.datatable>

    {{ $structures->links() }}
</div>
@endsection
