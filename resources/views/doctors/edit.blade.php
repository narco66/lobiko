@extends('layouts.app')
@section('title', 'Modifier le médecin')
@section('content')
<div class="container py-4">
    <x-lobiko.page-header title="Modifier le médecin" subtitle="{{ $doctor->full_name }}" />
    <x-lobiko.ui.flash />

    <form method="POST" action="{{ route('admin.doctors.update', $doctor) }}">
        @csrf @method('PUT')
        <div class="card mb-3">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6"><x-lobiko.forms.input name="matricule" label="Matricule" :value="$doctor->matricule" required /></div>
                    <div class="col-md-6"><x-lobiko.forms.input name="user_id" label="User ID (optionnel)" :value="$doctor->user_id" /></div>
                </div>
                <div class="row">
                    <div class="col-md-6"><x-lobiko.forms.input name="nom" label="Nom" :value="$doctor->nom" required /></div>
                    <div class="col-md-6"><x-lobiko.forms.input name="prenom" label="Prénom" :value="$doctor->prenom" required /></div>
                </div>
                <div class="row">
                    <div class="col-md-6"><x-lobiko.forms.input name="telephone" label="Téléphone" :value="$doctor->telephone" /></div>
                    <div class="col-md-6"><x-lobiko.forms.input name="email" label="Email" type="email" :value="$doctor->email" /></div>
                </div>
                <x-lobiko.forms.select name="specialty_id" label="Spécialité principale" :options="$specialties->pluck('libelle','id')->toArray()" :selected="$doctor->specialty_id" />
                <x-lobiko.forms.select name="specialties[]" label="Spécialités (multi)" :options="$specialties->pluck('libelle','id')->toArray()" :selected="$doctor->specialties->pluck('id')->toArray()" multiple />
                <x-lobiko.forms.select name="structures[]" label="Structures" :options="$structures->pluck('nom_structure','id')->toArray()" :selected="$doctor->structures->pluck('id')->toArray()" multiple />
                <x-lobiko.forms.select name="statut" label="Statut" :options="['actif'=>'Actif','suspendu'=>'Suspendu','en_validation'=>'En validation']" :selected="$doctor->statut" />
            </div>
        </div>
        <div class="d-flex gap-2">
            <x-lobiko.buttons.primary type="submit">Mettre à jour</x-lobiko.buttons.primary>
            <x-lobiko.buttons.secondary type="reset">Annuler</x-lobiko.buttons.secondary>
        </div>
    </form>
</div>
@endsection
