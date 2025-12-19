@extends('layouts.app')
@section('title','Services médicaux')
@section('content')
<div class="container py-4">
    <x-lobiko.page-header title="Services médicaux" :actions="[
        ['type'=>'primary','url'=>route('admin.services.create'),'label'=>'Créer','icon'=>'plus']
    ]"/>
    <x-lobiko.ui.flash />

    <x-lobiko.tables.datatable>
        <x-slot name="head">
            <th>Code</th><th>Libellé</th><th>Actif</th><th class="text-end">Actions</th>
        </x-slot>
        @foreach($services as $service)
            <tr>
                <td>{{ $service->code }}</td>
                <td>{{ $service->libelle }}</td>
                <td><x-lobiko.ui.badge-status :status="$service->actif ? 'actif' : 'inactif'" /></td>
                <td class="text-end">
                    <a href="{{ route('admin.services.show', $service) }}" class="btn btn-sm btn-outline-primary"><i class="fas fa-eye"></i></a>
                    <a href="{{ route('admin.services.edit', $service) }}" class="btn btn-sm btn-outline-secondary"><i class="fas fa-pen"></i></a>
                    <form action="{{ route('admin.services.destroy', $service) }}" method="POST" class="d-inline" onsubmit="return confirm('Supprimer ?');">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                    </form>
                </td>
            </tr>
        @endforeach
    </x-lobiko.tables.datatable>
    {{ $services->links() }}
</div>
@endsection
