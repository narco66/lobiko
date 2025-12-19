@extends('layouts.app')

@section('title', 'Créer une consultation')

@section('content')
<div class="container py-4">
    <x-lobiko.page-header title="Créer une consultation" :breadcrumbs="[
        ['label' => 'Consultations', 'href' => route('consultations.index')],
        ['label' => 'Créer']
    ]" />

    <form method="POST" action="{{ route('consultations.store') }}">
        @csrf

        <div class="row g-3">
            <div class="col-lg-6">
                <x-ui.panel title="Informations patient">
                    <x-lobiko.forms.input name="patient_id" label="Patient (UUID)" :value="old('patient_id', $patient->id ?? '')" required />
                    <x-lobiko.forms.select name="structure_id" label="Structure" :options="$structures->pluck('nom_structure','id')->toArray()" :value="old('structure_id')" required placeholder="Choisir une structure" />
                </x-ui.panel>
            </div>
            <div class="col-lg-6">
                <x-ui.panel title="Détails consultation">
                    <x-lobiko.forms.input name="date_consultation" type="date" label="Date" :value="old('date_consultation', now()->toDateString())" required />
                    <x-lobiko.forms.select name="type" label="Type" :options="['initial' => 'Initiale', 'controle' => 'Contrôle', 'urgence' => 'Urgence', 'suivi' => 'Suivi']" :value="old('type')" required placeholder="Choisir" />
                    <x-lobiko.forms.select name="modalite" label="Modalité" :options="['presentiel' => 'Présentiel', 'teleconsultation' => 'Téléconsultation']" :value="old('modalite')" required placeholder="Choisir" />
                    <x-lobiko.forms.textarea name="motif_consultation" label="Motif" :value="old('motif_consultation')" rows="3" required />
                </x-ui.panel>
            </div>
        </div>

        <div class="row g-3 mt-3">
            <div class="col-lg-6">
                <x-ui.panel title="Diagnostic">
                    <x-lobiko.forms.input name="diagnostic_principal" label="Diagnostic principal" :value="old('diagnostic_principal')" />
                    <x-lobiko.forms.input name="code_cim10" label="Code CIM10" :value="old('code_cim10')" />
                </x-ui.panel>
            </div>
            <div class="col-lg-6">
                <x-ui.panel title="Conduite à tenir">
                    <x-lobiko.forms.textarea name="conduite_a_tenir" label="Plan" :value="old('conduite_a_tenir')" rows="4" />
                    <x-lobiko.forms.textarea name="recommandations" label="Recommandations" :value="old('recommandations')" rows="3" />
                </x-ui.panel>
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2 mt-3">
            <x-lobiko.buttons.secondary href="{{ route('consultations.index') }}">Annuler</x-lobiko.buttons.secondary>
            <x-lobiko.buttons.primary type="submit" icon="fas fa-save">Enregistrer</x-lobiko.buttons.primary>
        </div>
    </form>
</div>
@endsection
