@extends('layouts.app')
@section('title', $structure->nom_structure)
@section('content')
<div class="container py-4">
    <x-lobiko.page-header title="{{ $structure->nom_structure }}" subtitle="Structure médicale" />
    <x-lobiko.ui.flash />

    <div class="row">
        <div class="col-md-8">
            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="card-title mb-3">Informations</h5>
                    <p><strong>Code :</strong> {{ $structure->code_structure }}</p>
                    <p><strong>Type :</strong> {{ ucfirst($structure->type_structure) }}</p>
                    <p><strong>Adresse :</strong> {{ $structure->adresse_rue }}, {{ $structure->adresse_quartier }}, {{ $structure->adresse_ville }} ({{ $structure->adresse_pays }})</p>
                    <p><strong>Contact :</strong> {{ $structure->telephone_principal }} | {{ $structure->email }}</p>
                    <p><strong>Statut :</strong> <x-lobiko.ui.badge-status :status="$structure->statut" /></p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card mb-3">
                <div class="card-body">
                    <h6>Actions</h6>
                    <a href="{{ route('admin.structures.edit', $structure) }}" class="btn btn-primary w-100 mb-2">Modifier</a>
                    <form action="{{ route('admin.structures.destroy', $structure) }}" method="POST" onsubmit="return confirm('Supprimer ?');">
                        @csrf @method('DELETE')
                        <button class="btn btn-outline-danger w-100">Supprimer</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
