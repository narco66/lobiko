@extends('layouts.app')
@section('title', 'Nouveau médecin')
@section('content')
<div class="container py-4">
    <x-lobiko.page-header title="Nouveau médecin" subtitle="Créer un compte praticien" />
    <x-lobiko.ui.flash />

    <form method="POST" action="{{ route('admin.doctors.store') }}">
        @csrf
        <div class="card mb-3">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6"><x-lobiko.forms.input name="matricule" label="Matricule" required /></div>
                    <div class="col-md-6"><x-lobiko.forms.input name="user_id" label="User ID (optionnel)" /></div>
                </div>
                <div class="row">
                    <div class="col-md-6"><x-lobiko.forms.input name="nom" label="Nom" required /></div>
                    <div class="col-md-6"><x-lobiko.forms.input name="prenom" label="Prénom" required /></div>
                </div>
                <div class="row">
                    <div class="col-md-6"><x-lobiko.forms.input name="telephone" label="Téléphone" /></div>
                    <div class="col-md-6"><x-lobiko.forms.input name="email" label="Email" type="email" /></div>
                </div>
                <x-lobiko.forms.select name="specialty_id" label="Spécialité principale" :options="$specialties->pluck('libelle','id')->toArray()" />
                <x-lobiko.forms.select name="specialties[]" label="Spécialités (multi)" :options="$specialties->pluck('libelle','id')->toArray()" multiple />
                <x-lobiko.forms.select name="structures[]" label="Structures" :options="$structures->pluck('nom_structure','id')->toArray()" multiple />
                <x-lobiko.forms.select name="statut" label="Statut" :options="['actif'=>'Actif','suspendu'=>'Suspendu','en_validation'=>'En validation']" />
            </div>
        </div>
        <div class="d-flex gap-2">
            <x-lobiko.buttons.primary type="submit">Enregistrer</x-lobiko.buttons.primary>
            <x-lobiko.buttons.secondary type="reset">Annuler</x-lobiko.buttons.secondary>
        </div>
    </form>
</div>
@endsection
