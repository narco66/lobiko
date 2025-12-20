@extends('layouts.app')
@section('title', 'Nouvelle pharmacie')

@section('content')
<div class="container py-4">
    <x-lobiko.page-header
        title="Nouvelle pharmacie"
        subtitle="Créer une officine partenaire"
        :breadcrumbs="[
            ['label' => 'Dashboard', 'href' => route('dashboard')],
            ['label' => 'Pharmacies', 'href' => route('admin.pharmacies.index')],
            ['label' => 'Créer']
        ]"
    />
    <x-lobiko.ui.flash />

    <form method="POST" action="{{ route('admin.pharmacies.store') }}">
        @csrf

        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-id-card me-2"></i>Identité & rattachement</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <x-lobiko.forms.input name="numero_licence" label="Numéro de licence" :value="old('numero_licence')" required />
                    </div>
                    <div class="col-md-4 mb-3">
                        <x-lobiko.forms.input name="nom_pharmacie" label="Nom de la pharmacie" :value="old('nom_pharmacie')" required />
                    </div>
                    <div class="col-md-4 mb-3">
                        <x-lobiko.forms.select
                            name="structure_medicale_id"
                            label="Structure médicale"
                            :options="$structures->pluck('nom_structure','id')->toArray()"
                            :value="old('structure_medicale_id')"
                            placeholder="Sélectionner"
                            required
                        />
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <x-lobiko.forms.input name="nom_responsable" label="Responsable" :value="old('nom_responsable')" required />
                    </div>
                    <div class="col-md-6 mb-3">
                        <x-lobiko.forms.select name="statut" label="Statut" :options="['active'=>'Active','inactive'=>'Inactive','suspendue'=>'Suspendue']" :value="old('statut','active')" />
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0"><i class="fas fa-phone me-2"></i>Contact & localisation</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <x-lobiko.forms.input name="telephone_pharmacie" label="Téléphone" :value="old('telephone_pharmacie')" required />
                    </div>
                    <div class="col-md-4 mb-3">
                        <x-lobiko.forms.input name="email_pharmacie" type="email" label="Email" :value="old('email_pharmacie')" />
                    </div>
                    <div class="col-md-4 mb-3">
                        <x-lobiko.forms.input name="adresse_complete" label="Adresse complète" :value="old('adresse_complete')" required />
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <x-lobiko.forms.input name="latitude" type="number" step="any" label="Latitude" :value="old('latitude')" />
                    </div>
                    <div class="col-md-6 mb-3">
                        <x-lobiko.forms.input name="longitude" type="number" step="any" label="Longitude" :value="old('longitude')" />
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0"><i class="fas fa-truck me-2"></i>Services & paiements</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 mb-3 form-check">
                        <input class="form-check-input" type="checkbox" value="1" id="service_garde" name="service_garde" {{ old('service_garde') ? 'checked' : '' }}>
                        <label class="form-check-label" for="service_garde">Service de garde</label>
                    </div>
                    <div class="col-md-4 mb-3 form-check">
                        <input class="form-check-input" type="checkbox" value="1" id="livraison_disponible" name="livraison_disponible" {{ old('livraison_disponible') ? 'checked' : '' }}>
                        <label class="form-check-label" for="livraison_disponible">Livraison disponible</label>
                    </div>
                    <div class="col-md-4 mb-3 form-check">
                        <input class="form-check-input" type="checkbox" value="1" id="paiement_mobile_money" name="paiement_mobile_money" {{ old('paiement_mobile_money', true) ? 'checked' : '' }}>
                        <label class="form-check-label" for="paiement_mobile_money">Mobile Money</label>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4 mb-3 form-check">
                        <input class="form-check-input" type="checkbox" value="1" id="paiement_carte" name="paiement_carte" {{ old('paiement_carte') ? 'checked' : '' }}>
                        <label class="form-check-label" for="paiement_carte">Carte bancaire</label>
                    </div>
                    <div class="col-md-4 mb-3 form-check">
                        <input class="form-check-input" type="checkbox" value="1" id="paiement_especes" name="paiement_especes" {{ old('paiement_especes', true) ? 'checked' : '' }}>
                        <label class="form-check-label" for="paiement_especes">Espèces</label>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <x-lobiko.forms.input name="rayon_livraison_km" type="number" step="0.1" label="Rayon livraison (km)" :value="old('rayon_livraison_km')" />
                    </div>
                    <div class="col-md-4 mb-3">
                        <x-lobiko.forms.input name="frais_livraison_base" type="number" step="0.01" label="Frais livraison base" :value="old('frais_livraison_base', 0)" />
                    </div>
                    <div class="col-md-4 mb-3">
                        <x-lobiko.forms.input name="frais_livraison_par_km" type="number" step="0.01" label="Frais par km" :value="old('frais_livraison_par_km', 0)" />
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-between align-items-center mb-4">
            <a href="{{ route('admin.pharmacies.index') }}" class="btn btn-secondary">
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
