@extends('layouts.app')
@section('title', 'Créer un dossier médical')

@section('content')
<div class="container py-4">
    <x-lobiko.page-header
        title="Créer un dossier médical"
        subtitle="DME patient"
        :breadcrumbs="[
            ['label' => 'Tableau de bord', 'href' => route('dashboard')],
            ['label' => 'Dossiers médicaux', 'href' => route('dossiers-medicaux.index')],
            ['label' => 'Créer']
        ]"
    />

    <form method="POST" action="{{ route('dossiers-medicaux.store') }}">
        @csrf

        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-user me-2"></i>Patient</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <x-lobiko.forms.select
                            name="patient_id"
                            label="Patient"
                            :options="$patients->pluck('name', 'id')->toArray()"
                            :value="old('patient_id')"
                            placeholder="Sélectionner"
                            required
                        />
                    </div>
                    <div class="col-md-6 mb-3">
                        <x-lobiko.forms.input name="numero_dossier" label="Numéro dossier" :value="old('numero_dossier')" required />
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0"><i class="fas fa-heartbeat me-2"></i>Profil médical</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 mb-3"><x-lobiko.forms.input name="groupe_sanguin" label="Groupe sanguin" :value="old('groupe_sanguin')" /></div>
                    <div class="col-md-3 mb-3"><x-lobiko.forms.input name="rhesus" label="Rhesus" :value="old('rhesus')" /></div>
                    <div class="col-md-3 mb-3"><x-lobiko.forms.input name="poids_habituel" type="number" step="0.01" label="Poids (kg)" :value="old('poids_habituel')" /></div>
                    <div class="col-md-3 mb-3"><x-lobiko.forms.input name="taille_cm" type="number" step="0.01" label="Taille (cm)" :value="old('taille_cm')" /></div>
                </div>
                <div class="row">
                    <div class="col-md-3 mb-3"><x-lobiko.forms.input name="tension_habituelle_sys" type="number" step="0.01" label="Tension sys" :value="old('tension_habituelle_sys')" /></div>
                    <div class="col-md-3 mb-3"><x-lobiko.forms.input name="tension_habituelle_dia" type="number" step="0.01" label="Tension dia" :value="old('tension_habituelle_dia')" /></div>
                    <div class="col-md-3 mb-3"><x-lobiko.forms.input name="imc" type="number" step="0.01" label="IMC" :value="old('imc')" /></div>
                    <div class="col-md-3 mb-3"><x-lobiko.forms.input name="derniere_consultation" type="datetime-local" label="Dernière consultation" :value="old('derniere_consultation')" /></div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3"><x-lobiko.forms.textarea name="allergies" label="Allergies (séparées par virgule)" :value="is_array(old('allergies')) ? implode(',', old('allergies')) : old('allergies')" rows="2" /></div>
                    <div class="col-md-6 mb-3"><x-lobiko.forms.textarea name="antecedents_medicaux" label="Antécédents médicaux" :value="is_array(old('antecedents_medicaux')) ? implode(',', old('antecedents_medicaux')) : old('antecedents_medicaux')" rows="2" /></div>
                </div>
            </div>
        </div>

        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-light">
                <h5 class="mb-0"><i class="fas fa-shield-alt me-2"></i>Contact & confidentialité</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 mb-3"><x-lobiko.forms.input name="contact_urgence_nom" label="Contact d'urgence" :value="old('contact_urgence_nom')" /></div>
                    <div class="col-md-4 mb-3"><x-lobiko.forms.input name="contact_urgence_telephone" label="Téléphone urgence" :value="old('contact_urgence_telephone')" /></div>
                    <div class="col-md-4 mb-3"><x-lobiko.forms.input name="contact_urgence_lien" label="Lien" :value="old('contact_urgence_lien')" /></div>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="partage_autorise" id="partage" value="1" {{ old('partage_autorise', true) ? 'checked' : '' }}>
                    <label class="form-check-label" for="partage">Partage autorisé</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="actif" id="actif" value="1" {{ old('actif', true) ? 'checked' : '' }}>
                    <label class="form-check-label" for="actif">Dossier actif</label>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-between align-items-center mb-4">
            <a href="{{ route('dossiers-medicaux.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Retour
            </a>
            <div class="d-flex gap-2">
                <button type="reset" class="btn btn-outline-secondary">Réinitialiser</button>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>Enregistrer</button>
            </div>
        </div>
    </form>
</div>
@endsection
