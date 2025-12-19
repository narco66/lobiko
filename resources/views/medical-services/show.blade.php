@extends('layouts.app')
@section('title', $service->libelle)
@section('content')
<div class="container py-4">
    <x-lobiko.page-header title="{{ $service->libelle }}" subtitle="Détail du service médical" />
    <x-lobiko.ui.flash />

    <div class="row">
        <div class="col-md-8">
            <div class="card mb-3">
                <div class="card-body">
                    <p><strong>Code :</strong> {{ $service->code }}</p>
                    <p><strong>Libellé :</strong> {{ $service->libelle }}</p>
                    <p><strong>Description :</strong> {{ $service->description ?? '—' }}</p>
                    <p><strong>Actif :</strong> <x-lobiko.ui.badge-status :status="$service->actif ? 'actif' : 'inactif'" /></p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card mb-3">
                <div class="card-body">
                    <h6>Actions</h6>
                    <a href="{{ route('admin.services.edit', $service) }}" class="btn btn-primary w-100 mb-2">Modifier</a>
                    <form action="{{ route('admin.services.destroy', $service) }}" method="POST" onsubmit="return confirm('Supprimer ce service ?');">
                        @csrf @method('DELETE')
                        <button class="btn btn-outline-danger w-100">Supprimer</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
