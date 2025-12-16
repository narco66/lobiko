@extends('layouts.app')

@section('title', 'Demande d\'urgence')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="mb-4">
                <p class="text-uppercase text-danger fw-semibold small mb-1">Urgence</p>
                <h1 class="h3 fw-bold mb-2">Déclarer une urgence</h1>
                <p class="text-muted mb-0">Partagez vos coordonnées et la nature de l'urgence pour accélérer la prise en charge.</p>
            </div>
            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('services.emergency.request.submit') }}" class="row g-3">
                        @csrf
                        <div class="col-md-6">
                            <label class="form-label">Nom complet</label>
                            <input type="text" name="full_name" class="form-control @error('full_name') is-invalid @enderror" value="{{ old('full_name') }}" required>
                            @error('full_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Téléphone</label>
                            <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone') }}" required>
                            @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Ville</label>
                            <input type="text" name="city" class="form-control @error('city') is-invalid @enderror" value="{{ old('city') }}" placeholder="Ville / quartier">
                            @error('city') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Localisation précise</label>
                            <input type="text" name="location" class="form-control @error('location') is-invalid @enderror" value="{{ old('location') }}" placeholder="Adresse, point de repère">
                            @error('location') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Type d'urgence</label>
                            <select name="emergency_type" class="form-select @error('emergency_type') is-invalid @enderror" required>
                                <option value="">Sélectionnez</option>
                                <option value="medicale" @selected(old('emergency_type')==='medicale')>Médicale</option>
                                <option value="traumatique" @selected(old('emergency_type')==='traumatique')>Traumatique</option>
                                <option value="obstetricale" @selected(old('emergency_type')==='obstetricale')>Obstétricale</option>
                                <option value="autre" @selected(old('emergency_type')==='autre')>Autre</option>
                            </select>
                            @error('emergency_type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label">Description</label>
                            <textarea name="notes" class="form-control @error('notes') is-invalid @enderror" rows="3" placeholder="Symptômes, blessures, contexte...">{{ old('notes') }}</textarea>
                            @error('notes') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-12 d-flex justify-content-end">
                            <a href="{{ route('services.emergency') }}" class="btn btn-outline-secondary me-2">Retour</a>
                            <button type="submit" class="btn btn-gradient">Envoyer la demande</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
