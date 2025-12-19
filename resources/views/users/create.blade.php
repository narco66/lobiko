@extends('layouts.app')

@section('title', 'Créer un utilisateur')

@section('content')
<div class="container py-4">
    <x-lobiko.page-header title="Créer un utilisateur" :breadcrumbs="[['label' => 'Utilisateurs', 'href' => route('users.index')], ['label' => 'Créer']]" />

    <form method="POST" action="{{ route('users.store') }}">
        @csrf
        <div class="row g-3">
            <div class="col-lg-6">
                <x-ui.panel title="Identité">
                    <x-lobiko.forms.input name="nom" label="Nom" :value="old('nom')" required />
                    <x-lobiko.forms.input name="prenom" label="Prénom" :value="old('prenom')" required />
                    <x-lobiko.forms.input name="email" type="email" label="Email" :value="old('email')" required />
                    <x-lobiko.forms.input name="telephone" label="Téléphone" :value="old('telephone')" required />
                    <x-lobiko.forms.input name="date_naissance" type="date" label="Date de naissance" :value="old('date_naissance')" required />
                    <x-lobiko.forms.input name="adresse_ville" label="Ville" :value="old('adresse_ville', 'Libreville')" required />
                    <x-lobiko.forms.input name="adresse_pays" label="Pays" :value="old('adresse_pays', 'Gabon')" required />
                </x-ui.panel>
            </div>
            <div class="col-lg-6">
                <x-ui.panel title="Compte">
                    <x-lobiko.forms.input name="password" type="password" label="Mot de passe" required />
                    <x-lobiko.forms.input name="password_confirmation" type="password" label="Confirmation" required />
                    <x-lobiko.forms.select name="sexe" label="Sexe" :options="['M' => 'Homme', 'F' => 'Femme']" :value="old('sexe')" required placeholder="Choisir" />
                    <x-lobiko.forms.select name="statut_compte" label="Statut" :options="['actif' => 'Actif', 'suspendu' => 'Suspendu']" :value="old('statut_compte', 'actif')" />
                    <x-lobiko.forms.select name="roles[]" label="Rôles" :options="$roles->toArray()" multiple />
                </x-ui.panel>
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2 mt-3">
            <x-lobiko.buttons.secondary href="{{ route('users.index') }}">Annuler</x-lobiko.buttons.secondary>
            <x-lobiko.buttons.primary type="submit" icon="fas fa-save">Enregistrer</x-lobiko.buttons.primary>
        </div>
    </form>
</div>
@endsection
