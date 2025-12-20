@extends('layouts.app')
@section('title', 'Modifier la facture')

@php
    $statuts = [
        'en_attente' => 'En attente',
        'partiel' => 'Partiel',
        'paye' => 'Payee',
        'impaye' => 'Impayee',
        'annule' => 'Annulee',
        'rembourse' => 'Rembourse',
    ];
    $types = [
        'consultation' => 'Consultation',
        'pharmacie' => 'Pharmacie',
        'hospitalisation' => 'Hospitalisation',
        'analyse' => 'Analyse',
        'imagerie' => 'Imagerie',
        'autre' => 'Autre',
    ];
    $natures = [
        'normale' => 'Normale',
        'avoir' => 'Avoir',
        'rectificative' => 'Rectificative',
    ];
@endphp

@section('content')
<div class="container py-4">
    <x-lobiko.page-header
        title="Modifier la facture"
        subtitle="{{ $facture->numero_facture ?? $facture->id }}"
        :breadcrumbs="[
            ['label' => 'Dashboard', 'href' => route('dashboard')],
            ['label' => 'Factures', 'href' => route('admin.factures.index')],
            ['label' => 'Edition']
        ]"
    />
    <x-lobiko.ui.flash />

    <form method="POST" action="{{ route('admin.factures.update', $facture) }}">
        @csrf
        @method('PUT')

        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-file-invoice-dollar me-2"></i>Informations principales</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <x-lobiko.forms.select
                            name="patient_id"
                            label="Patient"
                            :options="$patients->pluck('name', 'id')->toArray()"
                            :value="old('patient_id', $facture->patient_id)"
                            placeholder="Choisir un patient"
                            required
                        />
                    </div>
                    <div class="col-md-6 mb-3">
                        <x-lobiko.forms.select
                            name="praticien_id"
                            label="Praticien"
                            :options="$praticiens->pluck('name', 'id')->toArray()"
                            :value="old('praticien_id', $facture->praticien_id)"
                            placeholder="Choisir un praticien"
                            required
                        />
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <x-lobiko.forms.input name="numero_facture" label="Numero" :value="old('numero_facture', $facture->numero_facture)" />
                    </div>
                    <div class="col-md-4 mb-3">
                        <x-lobiko.forms.input name="montant_final" type="number" step="0.01" label="Montant total" :value="old('montant_final', $facture->montant_final)" required />
                    </div>
                    <div class="col-md-4 mb-3">
                        <x-lobiko.forms.select name="statut_paiement" label="Statut" :options="$statuts" :value="old('statut_paiement', $facture->statut_paiement)" />
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <x-lobiko.forms.select name="type" label="Type" :options="$types" :value="old('type', $facture->type)" required />
                    </div>
                    <div class="col-md-4 mb-3">
                        <x-lobiko.forms.select name="nature" label="Nature" :options="$natures" :value="old('nature', $facture->nature)" required />
                    </div>
                    <div class="col-md-4 mb-3">
                        <x-lobiko.forms.input name="date_facture" type="date" label="Date de facture" :value="old('date_facture', optional($facture->date_facture)->format('Y-m-d') ?? now()->format('Y-m-d'))" required />
                    </div>
                </div>
                <div class="mb-3">
                    <x-lobiko.forms.textarea name="notes_internes" label="Notes internes" :value="old('notes_internes', $facture->notes_internes)" rows="3" />
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-between align-items-center mb-4">
            <a href="{{ route('admin.factures.show', $facture) }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Annuler
            </a>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>Mettre a jour</button>
            </div>
        </div>
    </form>
</div>
@endsection
