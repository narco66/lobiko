@extends('layouts.app')
@section('title', 'Nouveau médecin')

@section('content')
<div class="container py-4">
    <x-lobiko.page-header
        title="Nouveau médecin"
        subtitle="Créer un compte praticien"
        :breadcrumbs="[
            ['label' => 'Dashboard', 'href' => route('dashboard')],
            ['label' => 'Médecins', 'href' => route('admin.doctors.index')],
            ['label' => 'Créer']
        ]"
    />
    <x-lobiko.ui.flash />

    <form method="POST" action="{{ route('admin.doctors.store') }}">
        @csrf

        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-id-card me-2"></i>Identité</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <x-lobiko.forms.input name="matricule" label="Matricule" :value="old('matricule')" required />
                    </div>
                    <div class="col-md-4 mb-3">
                        <x-lobiko.forms.input name="nom" label="Nom" :value="old('nom')" required />
                    </div>
                    <div class="col-md-4 mb-3">
                        <x-lobiko.forms.input name="prenom" label="Prénom" :value="old('prenom')" required />
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0"><i class="fas fa-envelope me-2"></i>Contact</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <x-lobiko.forms.input name="telephone" label="Téléphone" :value="old('telephone')" placeholder="+241 01 23 45 67" />
                    </div>
                    <div class="col-md-6 mb-3">
                        <x-lobiko.forms.input name="email" type="email" label="Email" :value="old('email')" placeholder="medecin@email.com" />
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <x-lobiko.forms.input name="user_id" label="Utilisateur (optionnel, UUID)" :value="old('user_id')" />
                    </div>
                    <div class="col-md-6 mb-3">
                        <x-lobiko.forms.select name="statut" label="Statut" :options="['actif'=>'Actif','suspendu'=>'Suspendu','en_validation'=>'En validation']" :value="old('statut','en_validation')" />
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0"><i class="fas fa-stethoscope me-2"></i>Affectations</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <x-lobiko.forms.select
                            name="specialty_id"
                            label="Spécialité principale"
                            :options="$specialties->pluck('libelle','id')->toArray()"
                            :value="old('specialty_id')"
                            placeholder="Choisir"
                        />
                    </div>
                    <div class="col-md-6 mb-3">
                        <x-lobiko.forms.select
                            name="specialties[]"
                            label="Spécialités (multi)"
                            :options="$specialties->pluck('libelle','id')->toArray()"
                            :value="old('specialties', [])"
                            multiple
                        />
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <x-lobiko.forms.select
                            name="structures[]"
                            label="Structures"
                            :options="$structures->pluck('nom_structure','id')->toArray()"
                            :value="old('structures', [])"
                            multiple
                        />
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-between align-items-center mb-4">
            <a href="{{ route('admin.doctors.index') }}" class="btn btn-secondary">
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
