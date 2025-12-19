@extends('layouts.app')
@section('title','Spécialités')
@section('content')
<div class="container py-4">
    <x-lobiko.page-header title="Spécialités" subtitle="Référentiel" :actions="[
        ['type'=>'primary','url'=>route('admin.specialties.create'),'label'=>'Créer','icon'=>'plus']
    ]"/>
    <x-lobiko.ui.flash />

    <x-lobiko.tables.datatable>
        <x-slot name="head">
            <th>Code</th><th>Libellé</th><th>Actif</th><th class="text-end">Actions</th>
        </x-slot>
        @foreach($specialties as $specialty)
            <tr>
                <td>{{ $specialty->code }}</td>
                <td>{{ $specialty->libelle }}</td>
                <td><x-lobiko.ui.badge-status :status="$specialty->actif ? 'actif' : 'inactif'" /></td>
                <td class="text-end">
                    <a href="{{ route('admin.specialties.edit', $specialty) }}" class="btn btn-sm btn-outline-secondary"><i class="fas fa-pen"></i></a>
                    <form action="{{ route('admin.specialties.destroy', $specialty) }}" method="POST" class="d-inline" onsubmit="return confirm('Supprimer ?');">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                    </form>
                </td>
            </tr>
        @endforeach
    </x-lobiko.tables.datatable>
    {{ $specialties->links() }}
</div>
@endsection
