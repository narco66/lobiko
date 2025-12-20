@extends('layouts.app')
@section('title', 'Modifier rendez-vous')

@php
    $modalites = ['presentiel' => 'Présentiel', 'teleconsultation' => 'Téléconsultation', 'domicile' => 'Domicile'];
@endphp

@section('content')
<div class="container py-4">
    <x-lobiko.page-header
        title="Modifier le rendez-vous"
        subtitle="{{ $rdv->numero_rdv ?? $rdv->id }}"
        :breadcrumbs="[
            ['label' => 'Dashboard', 'href' => route('dashboard')],
            ['label' => 'Rendez-vous', 'href' => route('appointments.index')],
            ['label' => 'Edition']
        ]"
    />
    <x-lobiko.ui.flash />

    <form method="POST" action="{{ route('appointments.update', $rdv) }}">
        @csrf
        @method('PUT')

        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-user me-2"></i>Participants</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <x-lobiko.forms.select
                            name="patient_id"
                            label="Patient"
                            :options="$patients->toArray()"
                            :value="old('patient_id', $rdv->patient_id)"
                            placeholder="Sélectionner"
                            required
                        />
                    </div>
                    <div class="col-md-4 mb-3">
                        <x-lobiko.forms.select
                            name="professionnel_id"
                            label="Praticien"
                            :options="$practitioners->toArray()"
                            :value="old('professionnel_id', $rdv->professionnel_id)"
                            placeholder="Sélectionner"
                            required
                        />
                    </div>
                    <div class="col-md-4 mb-3">
                        <x-lobiko.forms.select
                            name="structure_id"
                            label="Structure"
                            :options="$structures->pluck('nom_structure','id')->toArray()"
                            :value="old('structure_id', $rdv->structure_id)"
                            placeholder="Optionnel"
                        />
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0"><i class="fas fa-calendar-alt me-2"></i>Détails</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 mb-3"><x-lobiko.forms.input name="date_heure" type="datetime-local" label="Date et heure" :value="old('date_heure', optional($rdv->date_heure)->format('Y-m-d\\TH:i'))" required /></div>
                    <div class="col-md-4 mb-3"><x-lobiko.forms.input name="duree_prevue" type="number" min="0" label="Durée (minutes)" :value="old('duree_prevue', $rdv->duree_prevue)" /></div>
                    <div class="col-md-4 mb-3"><x-lobiko.forms.select name="modalite" label="Modalité" :options="$modalites" :value="old('modalite', $rdv->modalite)" required /></div>
                </div>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <x-lobiko.forms.select
                            name="lieu_type"
                            label="Lieu"
                            :options="['cabinet' => 'Cabinet', 'clinique' => 'Clinique', 'domicile' => 'Domicile', 'visio' => 'Visio']"
                            :value="old('lieu_type', $rdv->lieu_type)"
                            placeholder="Sélectionner"
                        />
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3"><x-lobiko.forms.input name="specialite" label="Spécialité" :value="old('specialite', $rdv->specialite)" /></div>
                    <div class="col-md-6 mb-3"><x-lobiko.forms.input name="statut" label="Statut" :value="old('statut', $rdv->statut)" /></div>
                </div>
                <div class="mb-3">
                    <x-lobiko.forms.textarea name="motif" label="Motif" :value="old('motif', $rdv->motif)" rows="3" />
                </div>
                <div class="mb-3">
                    <x-lobiko.forms.textarea name="notes_patient" label="Notes" :value="old('notes_patient', $rdv->notes_patient)" rows="3" />
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-between align-items-center mb-4">
            <a href="{{ route('appointments.show', $rdv) }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Annuler
            </a>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>Mettre à jour</button>
            </div>
        </div>
    </form>
</div>
@endsection
