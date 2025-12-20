@extends('layouts.app')
@section('title', 'Nouvel assureur')

@section('content')
<div class="container py-4">
    <x-lobiko.page-header
        title="Nouvel assureur"
        subtitle="Créer une compagnie ou mutuelle"
        :breadcrumbs="[
            ['label' => 'Dashboard', 'href' => route('dashboard')],
            ['label' => 'Assurances', 'href' => route('admin.assurances.index')],
            ['label' => 'Créer']
        ]"
    />
    <x-lobiko.ui.flash />

    <form method="POST" action="{{ route('admin.assurances.store') }}">
        @csrf

        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-building me-2"></i>Identité</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <x-lobiko.forms.input name="code_assureur" label="Code assureur" :value="old('code_assureur')" required />
                    </div>
                    <div class="col-md-4 mb-3">
                        <x-lobiko.forms.input name="nom_assureur" label="Nom légal" :value="old('nom_assureur')" required />
                    </div>
                    <div class="col-md-4 mb-3">
                        <x-lobiko.forms.input name="nom_commercial" label="Nom commercial" :value="old('nom_commercial')" />
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <x-lobiko.forms.select
                            name="type"
                            label="Type"
                            :options="['prive'=>'Privé','public'=>'Public','mutuelle'=>'Mutuelle','internationale'=>'Internationale']"
                            :value="old('type')"
                            placeholder="Choisir"
                            required
                        />
                    </div>
                    <div class="col-md-4 mb-3">
                        <x-lobiko.forms.input name="numero_agrement" label="Numéro d'agrément" :value="old('numero_agrement')" required />
                    </div>
                    <div class="col-md-4 mb-3">
                        <x-lobiko.forms.input name="numero_fiscal" label="Numéro fiscal" :value="old('numero_fiscal')" />
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <x-lobiko.forms.input name="registre_commerce" label="Registre de commerce" :value="old('registre_commerce')" />
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0"><i class="fas fa-phone me-2"></i>Contact</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <x-lobiko.forms.input name="adresse" label="Adresse" :value="old('adresse')" required />
                    </div>
                    <div class="col-md-3 mb-3">
                        <x-lobiko.forms.input name="ville" label="Ville" :value="old('ville')" required />
                    </div>
                    <div class="col-md-3 mb-3">
                        <x-lobiko.forms.input name="pays" label="Pays" :value="old('pays','Gabon')" required />
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <x-lobiko.forms.input name="telephone" label="Téléphone" :value="old('telephone')" required />
                    </div>
                    <div class="col-md-4 mb-3">
                        <x-lobiko.forms.input name="email" type="email" label="Email" :value="old('email')" required />
                    </div>
                    <div class="col-md-4 mb-3">
                        <x-lobiko.forms.input name="site_web" label="Site web" :value="old('site_web')" />
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <x-lobiko.forms.input name="email_medical" type="email" label="Email médical" :value="old('email_medical')" />
                    </div>
                    <div class="col-md-4 mb-3">
                        <x-lobiko.forms.input name="telephone_medical" label="Téléphone médical" :value="old('telephone_medical')" />
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0"><i class="fas fa-cog me-2"></i>Paramètres</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 mb-3 form-check">
                        <input class="form-check-input" type="checkbox" value="1" id="tiers_payant" name="tiers_payant" {{ old('tiers_payant', true) ? 'checked' : '' }}>
                        <label class="form-check-label" for="tiers_payant">Tiers payant</label>
                    </div>
                    <div class="col-md-3 mb-3 form-check">
                        <input class="form-check-input" type="checkbox" value="1" id="pec_temps_reel" name="pec_temps_reel" {{ old('pec_temps_reel') ? 'checked' : '' }}>
                        <label class="form-check-label" for="pec_temps_reel">PEC temps réel</label>
                    </div>
                    <div class="col-md-3 mb-3 form-check">
                        <input class="form-check-input" type="checkbox" value="1" id="actif" name="actif" {{ old('actif', true) ? 'checked' : '' }}>
                        <label class="form-check-label" for="actif">Actif</label>
                    </div>
                    <div class="col-md-3 mb-3 form-check">
                        <input class="form-check-input" type="checkbox" value="1" id="partenaire" name="partenaire" {{ old('partenaire') ? 'checked' : '' }}>
                        <label class="form-check-label" for="partenaire">Partenaire</label>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <x-lobiko.forms.input name="delai_remboursement" type="number" label="Délai remboursement (jours)" :value="old('delai_remboursement', 30)" />
                    </div>
                    <div class="col-md-4 mb-3">
                        <x-lobiko.forms.input name="date_partenariat" type="date" label="Date partenariat" :value="old('date_partenariat')" />
                    </div>
                    <div class="col-md-4 mb-3">
                        <x-lobiko.forms.input name="fin_partenariat" type="date" label="Fin partenariat" :value="old('fin_partenariat')" />
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-between align-items-center mb-4">
            <a href="{{ route('admin.assurances.index') }}" class="btn btn-secondary">
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
