@extends('layouts.app')
@section('title','Modifier le service')

@section('content')
<div class="container py-4">
    <x-lobiko.page-header
        title="Modifier le service"
        subtitle="{{ $service->libelle }}"
        :breadcrumbs="[
            ['label' => 'Dashboard', 'href' => route('dashboard')],
            ['label' => 'Services médicaux', 'href' => route('admin.services.index')],
            ['label' => 'Edition']
        ]"
    />
    <x-lobiko.ui.flash />
    <form method="POST" action="{{ route('admin.services.update', $service) }}">
        @csrf @method('PUT')
        <div class="card mb-3 shadow-sm">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 mb-3"><x-lobiko.forms.input name="code" label="Code" :value="old('code', $service->code)" required /></div>
                    <div class="col-md-8 mb-3"><x-lobiko.forms.input name="libelle" label="Libellé" :value="old('libelle', $service->libelle)" required /></div>
                </div>
                <div class="mb-3">
                    <x-lobiko.forms.textarea name="description" label="Description" :value="old('description', $service->description)" rows="3" />
                </div>
                <div class="mb-3">
                    <x-lobiko.forms.select name="actif" label="Actif" :options="[1=>'Oui',0=>'Non']" :value="old('actif', $service->actif)" />
                </div>
            </div>
        </div>
        <div class="d-flex justify-content-between align-items-center">
            <a href="{{ route('admin.services.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left me-2"></i>Annuler</a>
            <div class="d-flex gap-2">
                <button class="btn btn-primary" type="submit"><i class="fas fa-save me-2"></i>Mettre à jour</button>
            </div>
        </div>
    </form>
</div>
@endsection
