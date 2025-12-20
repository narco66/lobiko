@extends('layouts.app')
@section('title','Service '.$service->libelle)

@section('content')
<div class="container py-4">
    <x-lobiko.page-header
        title="Service médical"
        subtitle="{{ $service->libelle }}"
        :breadcrumbs="[
            ['label' => 'Services médicaux', 'href' => route('admin.services.index')],
            ['label' => $service->libelle]
        ]"
        :actions="[
            ['type'=>'secondary','url'=>route('admin.services.edit', $service),'label'=>'Modifier','icon'=>'pen']
        ]"
    />
    <x-lobiko.ui.flash />

    <div class="card shadow-sm">
        <div class="card-body">
            <p class="mb-1"><strong>Code :</strong> {{ $service->code }}</p>
            <p class="mb-1"><strong>Libellé :</strong> {{ $service->libelle }}</p>
            <p class="mb-1"><strong>Statut :</strong> <x-lobiko.ui.badge-status :status="$service->actif ? 'actif' : 'inactif'"/></p>
            <p class="mb-0"><strong>Description :</strong> {{ $service->description ?? 'N/A' }}</p>
        </div>
    </div>
</div>
@endsection
