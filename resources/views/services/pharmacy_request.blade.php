@extends('layouts.app')

@section('title', 'Demande pharmacie')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="mb-4">
                <p class="text-uppercase text-primary fw-semibold small mb-1">Pharmacie</p>
                <h1 class="h3 fw-bold mb-2">Demande de préparation / livraison</h1>
                <p class="text-muted mb-0">Indiquez vos coordonnées et choisissez retrait ou livraison.</p>
            </div>
            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('services.pharmacy.request.submit') }}" class="row g-3">
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
                            <label class="form-label">Email (optionnel)</label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}">
                            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Code ordonnance (optionnel)</label>
                            <input type="text" name="prescription_code" class="form-control @error('prescription_code') is-invalid @enderror" value="{{ old('prescription_code') }}" placeholder="Ex: ORD-1234">
                            @error('prescription_code') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Mode</label>
                            <select name="delivery_mode" class="form-select @error('delivery_mode') is-invalid @enderror" required>
                                <option value="">Sélectionnez</option>
                                <option value="retrait" @selected(old('delivery_mode')==='retrait')>Retrait en pharmacie</option>
                                <option value="livraison" @selected(old('delivery_mode')==='livraison')>Livraison</option>
                            </select>
                            @error('delivery_mode') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Adresse (si livraison)</label>
                            <input type="text" name="address" class="form-control @error('address') is-invalid @enderror" value="{{ old('address') }}" placeholder="Quartier, ville">
                            @error('address') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label">Notes</label>
                            <textarea name="notes" class="form-control @error('notes') is-invalid @enderror" rows="3" placeholder="Produits spécifiques, créneau souhaité...">{{ old('notes') }}</textarea>
                            @error('notes') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-12 d-flex justify-content-end">
                            <a href="{{ route('services.pharmacy') }}" class="btn btn-outline-secondary me-2">Retour</a>
                            <button type="submit" class="btn btn-gradient">Envoyer la demande</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
