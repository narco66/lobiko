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
                        <div class="col-12">
                            <div class="alert alert-info d-flex align-items-center gap-2 mb-1">
                                <i class="fas fa-circle-info"></i>
                                <div>
                                    Vérification des droits, préautorisation/tiers payant, simulation du reste à charge et dépôt de justificatifs.
                                </div>
                            </div>
                        </div>
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
                            <label class="form-label">Bénéficiaire</label>
                            <input type="text" name="beneficiary" class="form-control @error('beneficiary') is-invalid @enderror" value="{{ old('beneficiary') }}" placeholder="Nom de l'assuré/bénéficiaire">
                            @error('beneficiary') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Numéro de police</label>
                            <input type="text" name="policy_number" class="form-control @error('policy_number') is-invalid @enderror" value="{{ old('policy_number') }}" required>
                            @error('policy_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Numéro de contrat (si différent)</label>
                            <input type="text" name="contract_number" class="form-control @error('contract_number') is-invalid @enderror" value="{{ old('contract_number') }}">
                            @error('contract_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Assureur</label>
                            <input type="text" name="insurer" class="form-control @error('insurer') is-invalid @enderror" value="{{ old('insurer') }}" placeholder="Nom de la compagnie">
                            @error('insurer') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Validité du contrat</label>
                            <input type="date" name="contract_valid_until" class="form-control @error('contract_valid_until') is-invalid @enderror" value="{{ old('contract_valid_until') }}">
                            @error('contract_valid_until') <div class="invalid-feedback">{{ $message }}</div> @enderror
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
                        <div class="col-md-6">
                            <label class="form-label">Plafond restant (optionnel)</label>
                            <input type="number" step="0.01" name="plafond_remaining" class="form-control @error('plafond_remaining') is-invalid @enderror" value="{{ old('plafond_remaining') }}" placeholder="Ex: 150000">
                            @error('plafond_remaining') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Exclusions connues</label>
                            <input type="text" name="exclusions" class="form-control @error('exclusions') is-invalid @enderror" value="{{ old('exclusions') }}" placeholder="Actes exclus, carences...">
                            @error('exclusions') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Délai de carence (jours)</label>
                            <input type="number" name="waiting_period_days" class="form-control @error('waiting_period_days') is-invalid @enderror" value="{{ old('waiting_period_days') }}">
                            @error('waiting_period_days') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Préautorisation / référence (si déjà obtenue)</label>
                            <input type="text" name="preauthorization_ref" class="form-control @error('preauthorization_ref') is-invalid @enderror" value="{{ old('preauthorization_ref') }}">
                            @error('preauthorization_ref') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tiers payant</label>
                            <select name="tiers_payant" class="form-select @error('tiers_payant') is-invalid @enderror">
                                <option value="0" @selected(old('tiers_payant')==='0')>Non</option>
                                <option value="1" @selected(old('tiers_payant')==='1')>Oui</option>
                            </select>
                            @error('tiers_payant') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Montant estimé de l’acte (FCFA)</label>
                            <input type="number" step="0.01" name="simulated_total" class="form-control @error('simulated_total') is-invalid @enderror" value="{{ old('simulated_total') }}">
                            @error('simulated_total') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Taux de prise en charge (%)</label>
                            <input type="number" name="coverage_rate" class="form-control @error('coverage_rate') is-invalid @enderror" value="{{ old('coverage_rate') }}" min="0" max="100">
                            @error('coverage_rate') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label">Notes</label>
                            <textarea name="notes" class="form-control @error('notes') is-invalid @enderror" rows="3" placeholder="Acte concerné, établissement, date...">{{ old('notes') }}</textarea>
                            @error('notes') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label">Pièces justificatives (optionnel)</label>
                            <input type="file" name="attachments[]" class="form-control @error('attachments.*') is-invalid @enderror" multiple>
                            <div class="form-text">Ordonnance, devis, pièce d’identité, carte d’assurance… (max 4 Mo par fichier)</div>
                            @error('attachments.*') <div class="invalid-feedback">{{ $message }}</div> @enderror
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
