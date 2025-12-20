@extends('layouts.app')
@section('title', 'Nouveau devis')

@php
    $statuts = [
        'brouillon' => 'Brouillon',
        'emis' => 'Emis',
        'envoye' => 'Envoye',
        'accepte' => 'Accepte',
        'refuse' => 'Refuse',
        'expire' => 'Expire',
        'converti' => 'Converti',
    ];
@endphp

@section('content')
<div class="container py-4">
    <x-lobiko.page-header
        title="Nouveau devis"
        subtitle="Creer un devis"
        :breadcrumbs="[
            ['label' => 'Dashboard', 'href' => route('dashboard')],
            ['label' => 'Devis', 'href' => route('admin.devis.index')],
            ['label' => 'Creer']
        ]"
    />
    <x-lobiko.ui.flash />

    <form method="POST" action="{{ route('admin.devis.store') }}">
        @csrf

        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-file-signature me-2"></i>Informations</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <x-lobiko.forms.select
                            name="patient_id"
                            label="Patient"
                            :options="$patients->pluck('name', 'id')->toArray()"
                            :value="old('patient_id')"
                            placeholder="Choisir un patient"
                            required
                        />
                    </div>
                    <div class="col-md-6 mb-3">
                        <x-lobiko.forms.select
                            name="praticien_id"
                            label="Praticien"
                            :options="$praticiens->pluck('name', 'id')->toArray()"
                            :value="old('praticien_id')"
                            placeholder="Choisir un praticien"
                            required
                        />
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <x-lobiko.forms.input name="numero_devis" label="Numero (optionnel)" :value="old('numero_devis')" />
                    </div>
                    <div class="col-md-4 mb-3">
                        <x-lobiko.forms.input name="montant_final" type="number" step="0.01" label="Montant total" :value="old('montant_final')" required />
                    </div>
                    <div class="col-md-4 mb-3">
                        <x-lobiko.forms.select name="statut" label="Statut" :options="$statuts" :value="old('statut', 'brouillon')" />
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <x-lobiko.forms.input name="date_emission" type="date" label="Date d'emission" :value="old('date_emission', now()->format('Y-m-d'))" required />
                    </div>
                    <div class="col-md-4 mb-3">
                        <x-lobiko.forms.input name="date_validite" type="date" label="Date de validite" :value="old('date_validite')" />
                    </div>
                    <div class="col-md-4 mb-3">
                        <x-lobiko.forms.input name="duree_validite" type="number" label="Duree (jours)" :value="old('duree_validite', 30)" />
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <x-lobiko.forms.input name="montant_remise" type="number" step="0.01" label="Remise" :value="old('montant_remise', 0)" />
                    </div>
                    <div class="col-md-6 mb-3">
                        <x-lobiko.forms.input name="montant_assurance" type="number" step="0.01" label="Part assurance" :value="old('montant_assurance', 0)" />
                    </div>
                </div>
                <div class="mb-3">
                    <x-lobiko.forms.textarea name="notes_internes" label="Notes internes" :value="old('notes_internes')" rows="3" />
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-between align-items-center mb-4">
            <a href="{{ route('admin.devis.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Retour
            </a>
            <div class="d-flex gap-2">
                <button type="reset" class="btn btn-outline-secondary">Reinitialiser</button>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>Enregistrer</button>
            </div>
        </div>
    </form>
</div>
@endsection
