@extends('layouts.app')
@section('title','Nouvelle spécialité')

@section('content')
<div class="container py-4">
    <x-lobiko.page-header
        title="Nouvelle spécialité"
        subtitle="Ajouter une spécialité"
        :breadcrumbs="[
            ['label' => 'Dashboard', 'href' => route('dashboard')],
            ['label' => 'Spécialités', 'href' => route('admin.specialties.index')],
            ['label' => 'Créer']
        ]"
    />
    <x-lobiko.ui.flash />
    <form method="POST" action="{{ route('admin.specialties.store') }}">
        @csrf
        <div class="card mb-3 shadow-sm">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 mb-3"><x-lobiko.forms.input name="code" label="Code" :value="old('code')" required /></div>
                    <div class="col-md-8 mb-3"><x-lobiko.forms.input name="libelle" label="Libellé" :value="old('libelle')" required /></div>
                </div>
                <div class="mb-3">
                    <x-lobiko.forms.textarea name="description" label="Description" :value="old('description')" rows="3" />
                </div>
                <div class="mb-3">
                    <x-lobiko.forms.select name="actif" label="Actif" :options="[1=>'Oui',0=>'Non']" :value="old('actif',1)" />
                </div>
            </div>
        </div>
        <div class="d-flex justify-content-between align-items-center">
            <a href="{{ route('admin.specialties.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left me-2"></i>Retour</a>
            <div class="d-flex gap-2">
                <button class="btn btn-outline-secondary" type="reset">Réinitialiser</button>
                <button class="btn btn-primary" type="submit"><i class="fas fa-save me-2"></i>Enregistrer</button>
            </div>
        </div>
    </form>
</div>
@endsection
