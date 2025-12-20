@extends('layouts.app')
@section('title', 'Modifier le patient')

@section('content')
<div class="container py-4">
    <x-lobiko.page-header
        title="Modifier le patient"
        subtitle="{{ $patient->prenom }} {{ $patient->nom }}"
        :breadcrumbs="[
            ['label' => 'Dashboard', 'href' => route('dashboard')],
            ['label' => 'Patients', 'href' => route('patients.index')],
            ['label' => 'Edition']
        ]"
    />

    <x-lobiko.ui.flash />

    <form method="POST" action="{{ route('patients.update', $patient) }}" class="needs-validation" novalidate>
        @csrf
        @method('PUT')

        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-user me-2"></i>Identite</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="prenom" class="form-label">Prenom <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('prenom') is-invalid @enderror" id="prenom" name="prenom" value="{{ old('prenom', $patient->prenom) }}" required>
                        @error('prenom')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="nom" class="form-label">Nom <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('nom') is-invalid @enderror" id="nom" name="nom" value="{{ old('nom', $patient->nom) }}" required>
                        @error('nom')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="date_naissance" class="form-label">Date de naissance <span class="text-danger">*</span></label>
                        <input type="date" class="form-control @error('date_naissance') is-invalid @enderror" id="date_naissance" name="date_naissance" value="{{ old('date_naissance', optional($patient->date_naissance)->format('Y-m-d')) }}" required>
                        @error('date_naissance')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="sexe" class="form-label">Sexe <span class="text-danger">*</span></label>
                        <select class="form-select @error('sexe') is-invalid @enderror" id="sexe" name="sexe" required>
                            <option value="">-- Selectionner --</option>
                            <option value="M" {{ old('sexe', $patient->sexe) === 'M' ? 'selected' : '' }}>Homme</option>
                            <option value="F" {{ old('sexe', $patient->sexe) === 'F' ? 'selected' : '' }}>Femme</option>
                        </select>
                        @error('sexe')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="statut_compte" class="form-label">Statut</label>
                        <select class="form-select @error('statut_compte') is-invalid @enderror" id="statut_compte" name="statut_compte">
                            <option value="actif" {{ old('statut_compte', $patient->statut_compte ?? 'actif') === 'actif' ? 'selected' : '' }}>Actif</option>
                            <option value="en_attente" {{ old('statut_compte', $patient->statut_compte) === 'en_attente' ? 'selected' : '' }}>En attente</option>
                            <option value="suspendu" {{ old('statut_compte', $patient->statut_compte) === 'suspendu' ? 'selected' : '' }}>Suspendu</option>
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
                        <input type="text" class="form-control @error('telephone') is-invalid @enderror" id="telephone" name="telephone" value="{{ old('telephone', $patient->telephone) }}" required>
                        @error('telephone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $patient->email) }}" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="password" class="form-label">Nouveau mot de passe (optionnel)</label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password">
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="password_confirmation" class="form-label">Confirmation</label>
                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3 form-check">
                        <input class="form-check-input" type="checkbox" value="1" id="notifications_email" name="notifications_email" {{ old('notifications_email', $patient->notifications_email) ? 'checked' : '' }}>
                        <label class="form-check-label" for="notifications_email">Notifications email</label>
                    </div>
                    <div class="col-md-4 mb-3 form-check">
                        <input class="form-check-input" type="checkbox" value="1" id="notifications_sms" name="notifications_sms" {{ old('notifications_sms', $patient->notifications_sms) ? 'checked' : '' }}>
                        <label class="form-check-label" for="notifications_sms">Notifications SMS</label>
                    </div>
                    <div class="col-md-4 mb-3 form-check">
                        <input class="form-check-input" type="checkbox" value="1" id="notifications_push" name="notifications_push" {{ old('notifications_push', $patient->notifications_push) ? 'checked' : '' }}>
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
                        <input type="text" class="form-control @error('adresse_rue') is-invalid @enderror" id="adresse_rue" name="adresse_rue" value="{{ old('adresse_rue', $patient->adresse_rue) }}">
                        @error('adresse_rue')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="adresse_quartier" class="form-label">Quartier</label>
                        <input type="text" class="form-control @error('adresse_quartier') is-invalid @enderror" id="adresse_quartier" name="adresse_quartier" value="{{ old('adresse_quartier', $patient->adresse_quartier) }}">
                        @error('adresse_quartier')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="adresse_ville" class="form-label">Ville <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('adresse_ville') is-invalid @enderror" id="adresse_ville" name="adresse_ville" value="{{ old('adresse_ville', $patient->adresse_ville ?? 'Libreville') }}" required>
                        @error('adresse_ville')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="adresse_pays" class="form-label">Pays <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('adresse_pays') is-invalid @enderror" id="adresse_pays" name="adresse_pays" value="{{ old('adresse_pays', $patient->adresse_pays ?? 'Gabon') }}" required>
                        @error('adresse_pays')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-between align-items-center mb-4">
            <a href="{{ route('patients.show', $patient) }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Annuler
            </a>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>Mettre a jour</button>
            </div>
        </div>
    </form>
</div>
@endsection
