@extends('layouts.app')
@section('title', 'Nouveau patient')

@section('content')
<div class="container py-4">
    <x-lobiko.page-header
        title="Nouveau patient"
        subtitle="Ajouter un patient dans LOBIKO"
        :breadcrumbs="[
            ['label' => 'Dashboard', 'href' => route('dashboard')],
            ['label' => 'Patients', 'href' => route('patients.index')],
            ['label' => 'Creer']
        ]"
    />

    <x-lobiko.ui.flash />

    <form method="POST" action="{{ route('patients.store') }}" class="needs-validation" novalidate>
        @csrf

        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-user me-2"></i>Identite</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="prenom" class="form-label">Prenom <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('prenom') is-invalid @enderror" id="prenom" name="prenom" value="{{ old('prenom') }}" required placeholder="Jean">
                        @error('prenom')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="nom" class="form-label">Nom <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('nom') is-invalid @enderror" id="nom" name="nom" value="{{ old('nom') }}" required placeholder="Dupont">
                        @error('nom')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="date_naissance" class="form-label">Date de naissance <span class="text-danger">*</span></label>
                        <input type="date" class="form-control @error('date_naissance') is-invalid @enderror" id="date_naissance" name="date_naissance" value="{{ old('date_naissance') }}" required>
                        @error('date_naissance')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="sexe" class="form-label">Sexe <span class="text-danger">*</span></label>
                        <select class="form-select @error('sexe') is-invalid @enderror" id="sexe" name="sexe" required>
                            <option value="">-- Selectionner --</option>
                            <option value="M" {{ old('sexe') === 'M' ? 'selected' : '' }}>Homme</option>
                            <option value="F" {{ old('sexe') === 'F' ? 'selected' : '' }}>Femme</option>
                        </select>
                        @error('sexe')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="statut_compte" class="form-label">Statut</label>
                        <select class="form-select @error('statut_compte') is-invalid @enderror" id="statut_compte" name="statut_compte">
                            <option value="actif" {{ old('statut_compte', 'actif') === 'actif' ? 'selected' : '' }}>Actif</option>
                            <option value="en_attente" {{ old('statut_compte') === 'en_attente' ? 'selected' : '' }}>En attente</option>
                            <option value="suspendu" {{ old('statut_compte') === 'suspendu' ? 'selected' : '' }}>Suspendu</option>
                        </select>
                        @error('statut_compte')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0"><i class="fas fa-phone me-2"></i>Contact et compte</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="telephone" class="form-label">Telephone <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('telephone') is-invalid @enderror" id="telephone" name="telephone" value="{{ old('telephone') }}" required placeholder="+241 01 23 45 67">
                        @error('telephone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required placeholder="patient@email.com">
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="password" class="form-label">Mot de passe <span class="text-danger">*</span></label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="password_confirmation" class="form-label">Confirmation <span class="text-danger">*</span></label>
                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3 form-check">
                        <input class="form-check-input" type="checkbox" value="1" id="notifications_email" name="notifications_email" {{ old('notifications_email', true) ? 'checked' : '' }}>
                        <label class="form-check-label" for="notifications_email">Notifications email</label>
                    </div>
                    <div class="col-md-4 mb-3 form-check">
                        <input class="form-check-input" type="checkbox" value="1" id="notifications_sms" name="notifications_sms" {{ old('notifications_sms') ? 'checked' : '' }}>
                        <label class="form-check-label" for="notifications_sms">Notifications SMS</label>
                    </div>
                    <div class="col-md-4 mb-3 form-check">
                        <input class="form-check-input" type="checkbox" value="1" id="notifications_push" name="notifications_push" {{ old('notifications_push', true) ? 'checked' : '' }}>
                        <label class="form-check-label" for="notifications_push">Notifications push</label>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0"><i class="fas fa-map-marker-alt me-2"></i>Adresse</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="adresse_rue" class="form-label">Rue / voie</label>
                        <input type="text" class="form-control @error('adresse_rue') is-invalid @enderror" id="adresse_rue" name="adresse_rue" value="{{ old('adresse_rue') }}" placeholder="Ex: Avenue de la Paix">
                        @error('adresse_rue')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="adresse_quartier" class="form-label">Quartier</label>
                        <input type="text" class="form-control @error('adresse_quartier') is-invalid @enderror" id="adresse_quartier" name="adresse_quartier" value="{{ old('adresse_quartier') }}" placeholder="Ex: Glass">
                        @error('adresse_quartier')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="adresse_ville" class="form-label">Ville <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('adresse_ville') is-invalid @enderror" id="adresse_ville" name="adresse_ville" value="{{ old('adresse_ville', 'Libreville') }}" required>
                        @error('adresse_ville')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="adresse_pays" class="form-label">Pays <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('adresse_pays') is-invalid @enderror" id="adresse_pays" name="adresse_pays" value="{{ old('adresse_pays', 'Gabon') }}" required>
                        @error('adresse_pays')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-between align-items-center mb-4">
            <a href="{{ route('patients.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Retour
            </a>
            <div class="d-flex gap-2">
                <button type="reset" class="btn btn-outline-secondary">Reinitialiser</button>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>Enregistrer</button>
            </div>
        </div>
    </form>
</div>
@endsection
