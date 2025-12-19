@extends('layouts.app')

@section('title', 'Créer un dossier médical')

@section('content')
<div class="container py-4">
    <x-lobiko.page-header title="Créer un dossier médical" :breadcrumbs="[
        ['label' => 'Tableau de bord', 'href' => route('dashboard')],
        ['label' => 'Dossiers médicaux', 'href' => route('dossiers-medicaux.index')],
        ['label' => 'Créer']
    ]" />

    <form method="POST" action="{{ route('dossiers-medicaux.store') }}">
        @csrf

        <div class="row g-3">
            <div class="col-lg-6">
                <x-ui.panel title="Informations patient">
                    <x-lobiko.forms.input name="patient_id" label="Patient (UUID)" :value="old('patient_id')" required />
                    <x-lobiko.forms.input name="numero_dossier" label="Numéro dossier" :value="old('numero_dossier')" required />
                </x-ui.panel>
            </div>
            <div class="col-lg-6">
                <x-ui.panel title="Données médicales">
                    <div class="row g-3">
                        <div class="col-md-4"><x-lobiko.forms.input name="tension_habituelle_sys" type="number" step="0.01" label="Tension systolique" :value="old('tension_habituelle_sys')" /></div>
                        <div class="col-md-4"><x-lobiko.forms.input name="tension_habituelle_dia" type="number" step="0.01" label="Tension diastolique" :value="old('tension_habituelle_dia')" /></div>
                        <div class="col-md-4"><x-lobiko.forms.input name="poids_habituel" type="number" step="0.01" label="Poids (kg)" :value="old('poids_habituel')" /></div>
                        <div class="col-md-4"><x-lobiko.forms.input name="taille_cm" type="number" step="0.01" label="Taille (cm)" :value="old('taille_cm')" /></div>
                        <div class="col-md-4"><x-lobiko.forms.input name="imc" type="number" step="0.01" label="IMC" :value="old('imc')" /></div>
                    </div>
                </x-ui.panel>
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2 mt-3">
            <x-lobiko.buttons.secondary href="{{ route('dossiers-medicaux.index') }}">Annuler</x-lobiko.buttons.secondary>
            <x-lobiko.buttons.primary type="submit" icon="fas fa-save">Enregistrer</x-lobiko.buttons.primary>
        </div>
    </form>
</div>
@endsection
