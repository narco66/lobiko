@extends('layouts.app')
@section('title', 'Modifier l\'acte médical')

@section('content')
<div class="container py-4">
    <x-lobiko.page-header
        title="Modifier l'acte médical"
        subtitle="{{ $acte->libelle }}"
        :breadcrumbs="[
            ['label' => 'Dashboard', 'href' => route('dashboard')],
            ['label' => 'Actes', 'href' => route('admin.actes-medicaux.index')],
            ['label' => 'Édition']
        ]"
    />
    <x-lobiko.ui.flash />

    <form method="POST" action="{{ route('admin.actes-medicaux.update', $acte) }}">
        @csrf
        @method('PUT')

        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-id-card me-2"></i>Identification</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 mb-3"><x-lobiko.forms.input name="code_acte" label="Code acte" :value="old('code_acte', $acte->code_acte)" required /></div>
                    <div class="col-md-8 mb-3"><x-lobiko.forms.input name="libelle" label="Libellé" :value="old('libelle', $acte->libelle)" required /></div>
                </div>
                <div class="row">
                    <div class="col-md-4 mb-3"><x-lobiko.forms.input name="categorie" label="Catégorie" :value="old('categorie', $acte->categorie)" required /></div>
                    <div class="col-md-4 mb-3"><x-lobiko.forms.input name="specialite" label="Spécialité" :value="old('specialite', $acte->specialite)" /></div>
                    <div class="col-md-4 mb-3"><x-lobiko.forms.input name="tarif_base" type="number" step="0.01" label="Tarif de base" :value="old('tarif_base', $acte->tarif_base)" required /></div>
                </div>
            </div>
        </div>

        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0"><i class="fas fa-heartbeat me-2"></i>Options et contraintes</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 mb-3 form-check">
                        <input class="form-check-input" type="checkbox" value="1" id="urgence_possible" name="urgence_possible" {{ old('urgence_possible', $acte->urgence_possible) ? 'checked' : '' }}>
                        <label class="form-check-label" for="urgence_possible">Urgence possible</label>
                    </div>
                    <div class="col-md-3 mb-3 form-check">
                        <input class="form-check-input" type="checkbox" value="1" id="teleconsultation_possible" name="teleconsultation_possible" {{ old('teleconsultation_possible', $acte->teleconsultation_possible) ? 'checked' : '' }}>
                        <label class="form-check-label" for="teleconsultation_possible">Téléconsultation</label>
                    </div>
                    <div class="col-md-3 mb-3 form-check">
                        <input class="form-check-input" type="checkbox" value="1" id="domicile_possible" name="domicile_possible" {{ old('domicile_possible', $acte->domicile_possible) ? 'checked' : '' }}>
                        <label class="form-check-label" for="domicile_possible">Domicile</label>
                    </div>
                    <div class="col-md-3 mb-3 form-check">
                        <input class="form-check-input" type="checkbox" value="1" id="remboursable" name="remboursable" {{ old('remboursable', $acte->remboursable) ? 'checked' : '' }}>
                        <label class="form-check-label" for="remboursable">Remboursable</label>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4 mb-3"><x-lobiko.forms.input name="taux_remboursement_base" type="number" step="0.01" label="Taux remboursement (%)" :value="old('taux_remboursement_base', $acte->taux_remboursement_base)" /></div>
                    <div class="col-md-4 mb-3"><x-lobiko.forms.input name="duree_prevue" type="number" label="Durée prévue (min)" :value="old('duree_prevue', $acte->duree_prevue)" /></div>
                    <div class="col-md-4 mb-3"><x-lobiko.forms.input name="code_securite_sociale" label="Code sécurité sociale" :value="old('code_securite_sociale', $acte->code_securite_sociale)" /></div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3"><x-lobiko.forms.input name="age_minimum" type="number" label="Âge minimum" :value="old('age_minimum', $acte->age_minimum)" /></div>
                    <div class="col-md-6 mb-3"><x-lobiko.forms.input name="age_maximum" type="number" label="Âge maximum" :value="old('age_maximum', $acte->age_maximum)" /></div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <x-lobiko.forms.select name="sexe_requis" label="Sexe requis" :options="['M'=>'Homme','F'=>'Femme','Tous'=>'Tous']" :value="old('sexe_requis', $acte->sexe_requis)" />
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0"><i class="fas fa-tools me-2"></i>Tarifs supplémentaires</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 mb-3"><x-lobiko.forms.input name="tarif_urgence" type="number" step="0.01" label="Tarif urgence" :value="old('tarif_urgence', $acte->tarif_urgence)" /></div>
                    <div class="col-md-3 mb-3"><x-lobiko.forms.input name="tarif_weekend" type="number" step="0.01" label="Tarif week-end" :value="old('tarif_weekend', $acte->tarif_weekend)" /></div>
                    <div class="col-md-3 mb-3"><x-lobiko.forms.input name="tarif_nuit" type="number" step="0.01" label="Tarif nuit" :value="old('tarif_nuit', $acte->tarif_nuit)" /></div>
                    <div class="col-md-3 mb-3"><x-lobiko.forms.input name="tarif_domicile" type="number" step="0.01" label="Tarif domicile" :value="old('tarif_domicile', $acte->tarif_domicile)" /></div>
                </div>
            </div>
        </div>

        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-light">
                <h5 class="mb-0"><i class="fas fa-calendar me-2"></i>Validité</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3"><x-lobiko.forms.input name="date_debut_validite" type="date" label="Date début" :value="old('date_debut_validite', optional($acte->date_debut_validite)->format('Y-m-d'))" /></div>
                    <div class="col-md-6 mb-3"><x-lobiko.forms.input name="date_fin_validite" type="date" label="Date fin" :value="old('date_fin_validite', optional($acte->date_fin_validite)->format('Y-m-d'))" /></div>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" value="1" id="actif" name="actif" {{ old('actif', $acte->actif) ? 'checked' : '' }}>
                    <label class="form-check-label" for="actif">Activer l'acte</label>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-between align-items-center mb-4">
            <a href="{{ route('admin.actes-medicaux.show', $acte) }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Annuler
            </a>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>Mettre à jour</button>
            </div>
        </div>
    </form>
</div>
@endsection
