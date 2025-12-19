@extends('layouts.app')
@section('title', 'Nouvelle structure')
@section('content')
<div class="container py-4">
    <x-lobiko.page-header title="Nouvelle structure" subtitle="Créer une structure médicale" />
    <x-lobiko.ui.flash />

    <form method="POST" action="{{ route('admin.structures.store') }}">
        @csrf
        <div class="card mb-3">
            <div class="card-body">
                <x-lobiko.forms.input name="code_structure" label="Code" required />
                <x-lobiko.forms.input name="nom_structure" label="Nom" required />
                <x-lobiko.forms.select name="type_structure" label="Type" :options="[
                    'cabinet'=>'Cabinet','clinique'=>'Clinique','hopital'=>'Hôpital','pharmacie'=>'Pharmacie',
                    'laboratoire'=>'Laboratoire','centre_imagerie'=>'Centre imagerie','centre_specialise'=>'Centre spécialisé'
                ]" />
                <x-lobiko.forms.input name="adresse_rue" label="Adresse" required />
                <div class="row">
                    <div class="col-md-6"><x-lobiko.forms.input name="adresse_quartier" label="Quartier" required /></div>
                    <div class="col-md-6"><x-lobiko.forms.input name="adresse_ville" label="Ville" required /></div>
                </div>
                <x-lobiko.forms.input name="adresse_pays" label="Pays" value="Gabon" />
                <div class="row">
                    <div class="col-md-6"><x-lobiko.forms.input name="latitude" label="Latitude" required /></div>
                    <div class="col-md-6"><x-lobiko.forms.input name="longitude" label="Longitude" required /></div>
                </div>
                <x-lobiko.forms.input name="telephone_principal" label="Téléphone principal" required />
                <x-lobiko.forms.input name="email" label="Email" type="email" required />
                <x-lobiko.forms.select name="statut" label="Statut" :options="[
                    'actif'=>'Actif','suspendu'=>'Suspendu','ferme'=>'Fermé','en_validation'=>'En validation'
                ]" />
                <x-lobiko.forms.input name="responsable_id" label="Responsable (User ID)" required />
            </div>
        </div>
        <div class="d-flex gap-2">
            <x-lobiko.buttons.primary type="submit">Enregistrer</x-lobiko.buttons.primary>
            <x-lobiko.buttons.secondary type="reset">Annuler</x-lobiko.buttons.secondary>
        </div>
    </form>
</div>
@endsection
