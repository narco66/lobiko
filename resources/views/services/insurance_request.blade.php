@extends('layouts.app')

@section('title', 'Demande assurance')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="mb-4">
                <p class="text-uppercase text-primary fw-semibold small mb-1">Assurance santé</p>
                <h1 class="h3 fw-bold mb-2">Demande de prise en charge / remboursement</h1>
                <p class="text-muted mb-0">Renseignez votre contrat pour lancer une préautorisation ou un remboursement.</p>
            </div>
            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('services.insurance.request.submit') }}" class="row g-3">
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
                            <label class="form-label">Numéro de police</label>
                            <input type="text" name="policy_number" class="form-control @error('policy_number') is-invalid @enderror" value="{{ old('policy_number') }}" required>
                            @error('policy_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Assureur</label>
                            <input type="text" name="insurer" class="form-control @error('insurer') is-invalid @enderror" value="{{ old('insurer') }}" placeholder="Nom de la compagnie">
                            @error('insurer') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Type de demande</label>
                            <select name="request_type" class="form-select @error('request_type') is-invalid @enderror" required>
                                <option value="">Sélectionnez</option>
                                <option value="preautorisation" @selected(old('request_type')==='preautorisation')>Préautorisation / PEC</option>
                                <option value="remboursement" @selected(old('request_type')==='remboursement')>Remboursement</option>
                                <option value="information" @selected(old('request_type')==='information')>Information</option>
                            </select>
                            @error('request_type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label">Notes</label>
                            <textarea name="notes" class="form-control @error('notes') is-invalid @enderror" rows="3" placeholder="Acte concerné, établissement, date...">{{ old('notes') }}</textarea>
                            @error('notes') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-12 d-flex justify-content-end">
                            <a href="{{ route('services.insurance') }}" class="btn btn-outline-secondary me-2">Retour</a>
                            <button type="submit" class="btn btn-gradient">Envoyer la demande</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
