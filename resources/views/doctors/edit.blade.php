@extends('layouts.app')
@section('title', 'Modifier le médecin')

@section('content')
<div class="container py-4">
    <x-lobiko.page-header
        title="Modifier le médecin"
        subtitle="{{ $doctor->full_name }}"
        :breadcrumbs="[
            ['label' => 'Dashboard', 'href' => route('dashboard')],
            ['label' => 'Médecins', 'href' => route('admin.doctors.index')],
            ['label' => 'Édition']
        ]"
    />
    <x-lobiko.ui.flash />

    <form method="POST" action="{{ route('admin.doctors.update', $doctor) }}">
        @csrf
        @method('PUT')

        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-id-card me-2"></i>Identité</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <x-lobiko.forms.input name="matricule" label="Matricule" :value="old('matricule', $doctor->matricule)" required />
                    </div>
                    <div class="col-md-4 mb-3">
                        <x-lobiko.forms.input name="nom" label="Nom" :value="old('nom', $doctor->nom)" required />
                    </div>
                    <div class="col-md-4 mb-3">
                        <x-lobiko.forms.input name="prenom" label="Prénom" :value="old('prenom', $doctor->prenom)" required />
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
                        <x-lobiko.forms.input name="telephone" label="Téléphone" :value="old('telephone', $doctor->telephone)" />
                    </div>
                    <div class="col-md-6 mb-3">
                        <x-lobiko.forms.input name="email" type="email" label="Email" :value="old('email', $doctor->email)" />
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <x-lobiko.forms.input name="user_id" label="Utilisateur (optionnel, UUID)" :value="old('user_id', $doctor->user_id)" />
                    </div>
                    <div class="col-md-6 mb-3">
                        <x-lobiko.forms.select name="statut" label="Statut" :options="['actif'=>'Actif','suspendu'=>'Suspendu','en_validation'=>'En validation']" :value="old('statut', $doctor->statut)" />
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
                            :value="old('specialty_id', $doctor->specialty_id)"
                            placeholder="Choisir"
                        />
                    </div>
                    <div class="col-md-6 mb-3">
                        <x-lobiko.forms.select
                            name="specialties[]"
                            label="Spécialités (multi)"
                            :options="$specialties->pluck('libelle','id')->toArray()"
                            :value="old('specialties', $doctor->specialties->pluck('id')->toArray())"
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
                            :value="old('structures', $doctor->structures->pluck('id')->toArray())"
                            multiple
                        />
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-between align-items-center mb-4">
            <a href="{{ route('admin.doctors.show', $doctor) }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Annuler
            </a>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>Mettre à jour</button>
            </div>
        </div>
    </form>
</div>
@endsection
