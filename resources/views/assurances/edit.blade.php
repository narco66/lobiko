@extends('layouts.app')
@section('title', 'Modifier l\'assureur')

@section('content')
<div class="container py-4">
    <x-lobiko.page-header
        title="Modifier l'assureur"
        subtitle="{{ $assurance->nom_assureur }}"
        :breadcrumbs="[
            ['label' => 'Dashboard', 'href' => route('dashboard')],
            ['label' => 'Assurances', 'href' => route('admin.assurances.index')],
            ['label' => 'Édition']
        ]"
    />
    <x-lobiko.ui.flash />

    <form method="POST" action="{{ route('admin.assurances.update', $assurance) }}">
        @csrf
        @method('PUT')

        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-building me-2"></i>Identité</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <x-lobiko.forms.input name="code_assureur" label="Code assureur" :value="old('code_assureur', $assurance->code_assureur)" required />
                    </div>
                    <div class="col-md-4 mb-3">
                        <x-lobiko.forms.input name="nom_assureur" label="Nom légal" :value="old('nom_assureur', $assurance->nom_assureur)" required />
                    </div>
                    <div class="col-md-4 mb-3">
                        <x-lobiko.forms.input name="nom_commercial" label="Nom commercial" :value="old('nom_commercial', $assurance->nom_commercial)" />
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <x-lobiko.forms.select
                            name="type"
                            label="Type"
                            :options="['prive'=>'Privé','public'=>'Public','mutuelle'=>'Mutuelle','internationale'=>'Internationale']"
                            :value="old('type', $assurance->type)"
                            placeholder="Choisir"
                            required
                        />
                    </div>
                    <div class="col-md-4 mb-3">
                        <x-lobiko.forms.input name="numero_agrement" label="Numéro d'agrément" :value="old('numero_agrement', $assurance->numero_agrement)" required />
                    </div>
                    <div class="col-md-4 mb-3">
                        <x-lobiko.forms.input name="numero_fiscal" label="Numéro fiscal" :value="old('numero_fiscal', $assurance->numero_fiscal)" />
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <x-lobiko.forms.input name="registre_commerce" label="Registre de commerce" :value="old('registre_commerce', $assurance->registre_commerce)" />
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
                        <x-lobiko.forms.input name="adresse" label="Adresse" :value="old('adresse', $assurance->adresse)" required />
                    </div>
                    <div class="col-md-3 mb-3">
                        <x-lobiko.forms.input name="ville" label="Ville" :value="old('ville', $assurance->ville)" required />
                    </div>
                    <div class="col-md-3 mb-3">
                        <x-lobiko.forms.input name="pays" label="Pays" :value="old('pays', $assurance->pays)" required />
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <x-lobiko.forms.input name="telephone" label="Téléphone" :value="old('telephone', $assurance->telephone)" required />
                    </div>
                    <div class="col-md-4 mb-3">
                        <x-lobiko.forms.input name="email" type="email" label="Email" :value="old('email', $assurance->email)" required />
                    </div>
                    <div class="col-md-4 mb-3">
                        <x-lobiko.forms.input name="site_web" label="Site web" :value="old('site_web', $assurance->site_web)" />
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <x-lobiko.forms.input name="email_medical" type="email" label="Email médical" :value="old('email_medical', $assurance->email_medical)" />
                    </div>
                    <div class="col-md-4 mb-3">
                        <x-lobiko.forms.input name="telephone_medical" label="Téléphone médical" :value="old('telephone_medical', $assurance->telephone_medical)" />
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
                        <input class="form-check-input" type="checkbox" value="1" id="tiers_payant" name="tiers_payant" {{ old('tiers_payant', $assurance->tiers_payant) ? 'checked' : '' }}>
                        <label class="form-check-label" for="tiers_payant">Tiers payant</label>
                    </div>
                    <div class="col-md-3 mb-3 form-check">
                        <input class="form-check-input" type="checkbox" value="1" id="pec_temps_reel" name="pec_temps_reel" {{ old('pec_temps_reel', $assurance->pec_temps_reel) ? 'checked' : '' }}>
                        <label class="form-check-label" for="pec_temps_reel">PEC temps réel</label>
                    </div>
                    <div class="col-md-3 mb-3 form-check">
                        <input class="form-check-input" type="checkbox" value="1" id="actif" name="actif" {{ old('actif', $assurance->actif) ? 'checked' : '' }}>
                        <label class="form-check-label" for="actif">Actif</label>
                    </div>
                    <div class="col-md-3 mb-3 form-check">
                        <input class="form-check-input" type="checkbox" value="1" id="partenaire" name="partenaire" {{ old('partenaire', $assurance->partenaire) ? 'checked' : '' }}>
                        <label class="form-check-label" for="partenaire">Partenaire</label>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <x-lobiko.forms.input name="delai_remboursement" type="number" label="Délai remboursement (jours)" :value="old('delai_remboursement', $assurance->delai_remboursement)" />
                    </div>
                    <div class="col-md-4 mb-3">
                        <x-lobiko.forms.input name="date_partenariat" type="date" label="Date partenariat" :value="old('date_partenariat', optional($assurance->date_partenariat)->format('Y-m-d'))" />
                    </div>
                    <div class="col-md-4 mb-3">
                        <x-lobiko.forms.input name="fin_partenariat" type="date" label="Fin partenariat" :value="old('fin_partenariat', optional($assurance->fin_partenariat)->format('Y-m-d'))" />
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-between align-items-center mb-4">
            <a href="{{ route('admin.assurances.show', $assurance) }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Annuler
            </a>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>Mettre à jour</button>
            </div>
        </div>
    </form>
</div>
@endsection
