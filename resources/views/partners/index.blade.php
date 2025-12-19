@extends('layouts.app')

@section('title', 'Partenaires')

@section('content')
<div class="container py-4">
    <x-lobiko.page-header
        title="Partenaires"
        :breadcrumbs="[['label' => 'Dashboard', 'href' => route('dashboard')], ['label' => 'Partenaires']]"
        :actions="[
            ['type' => 'primary', 'url' => route('partners.create'), 'label' => 'Créer', 'icon' => 'fas fa-plus']
        ]"
    />

    <x-lobiko.ui.flash />

    <x-lobiko.tables.datatable>
        <x-slot name="head">
            <th>Nom</th>
            <th>Type</th>
            <th>Statut</th>
            <th>Commission</th>
            <th class="text-end">Actions</th>
        </x-slot>
        @foreach($partners as $partner)
            <tr>
                <td>{{ $partner->name }}</td>
                <td>{{ $partner->partner_type }}</td>
                <td><x-lobiko.ui.badge-status :status="$partner->statut" /></td>
                <td>{{ $partner->commission_mode }} {{ $partner->commission_value }}</td>
                <td class="text-end">
                    <a class="btn btn-sm btn-outline-primary" href="{{ route('partners.edit', $partner) }}"><i class="fas fa-pen"></i></a>
                    <form action="{{ route('partners.destroy', $partner) }}" method="POST" class="d-inline" onsubmit="return confirm('Supprimer ?');">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                    </form>
                </td>
            </tr>
        @endforeach
    </x-lobiko.tables.datatable>

    <div class="mt-3">
        {{ $partners->links() }}
    </div>
</div>
@endsection
