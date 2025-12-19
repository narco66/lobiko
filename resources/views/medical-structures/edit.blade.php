@extends('layouts.app')
@section('title', 'Modifier la structure')
@section('content')
<div class="container py-4">
    <x-lobiko.page-header title="Modifier la structure" subtitle="{{ $structure->nom_structure }}" />
    <x-lobiko.ui.flash />

    <form method="POST" action="{{ route('admin.structures.update', $structure) }}">
        @csrf @method('PUT')
        <div class="card mb-3">
            <div class="card-body">
                <x-lobiko.forms.input name="code_structure" label="Code" :value="$structure->code_structure" required />
                <x-lobiko.forms.input name="nom_structure" label="Nom" :value="$structure->nom_structure" required />
                <x-lobiko.forms.select name="type_structure" label="Type" :options="[
                    'cabinet'=>'Cabinet','clinique'=>'Clinique','hopital'=>'Hôpital','pharmacie'=>'Pharmacie',
                    'laboratoire'=>'Laboratoire','centre_imagerie'=>'Centre imagerie','centre_specialise'=>'Centre spécialisé'
                ]" :selected="$structure->type_structure" />
                <x-lobiko.forms.input name="adresse_rue" label="Adresse" :value="$structure->adresse_rue" required />
                <div class="row">
                    <div class="col-md-6"><x-lobiko.forms.input name="adresse_quartier" label="Quartier" :value="$structure->adresse_quartier" required /></div>
                    <div class="col-md-6"><x-lobiko.forms.input name="adresse_ville" label="Ville" :value="$structure->adresse_ville" required /></div>
                </div>
                <x-lobiko.forms.input name="adresse_pays" label="Pays" :value="$structure->adresse_pays" />
                <div class="row">
                    <div class="col-md-6"><x-lobiko.forms.input name="latitude" label="Latitude" :value="$structure->latitude" required /></div>
                    <div class="col-md-6"><x-lobiko.forms.input name="longitude" label="Longitude" :value="$structure->longitude" required /></div>
                </div>
                <x-lobiko.forms.input name="telephone_principal" label="Téléphone principal" :value="$structure->telephone_principal" required />
                <x-lobiko.forms.input name="email" label="Email" type="email" :value="$structure->email" required />
                <x-lobiko.forms.select name="statut" label="Statut" :options="[
                    'actif'=>'Actif','suspendu'=>'Suspendu','ferme'=>'Fermé','en_validation'=>'En validation'
                ]" :selected="$structure->statut" />
                <x-lobiko.forms.input name="responsable_id" label="Responsable (User ID)" :value="$structure->responsable_id" required />
            </div>
        </div>
        <div class="d-flex gap-2">
            <x-lobiko.buttons.primary type="submit">Mettre à jour</x-lobiko.buttons.primary>
            <x-lobiko.buttons.secondary type="reset">Annuler</x-lobiko.buttons.secondary>
        </div>
    </form>
</div>
@endsection
