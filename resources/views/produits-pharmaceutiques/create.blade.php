@extends('layouts.app')
@section('title', 'Nouveau produit pharmaceutique')

@section('content')
<div class="container py-4">
    <x-lobiko.page-header
        title="Nouveau produit"
        subtitle="Ajouter un produit pharmaceutique"
        :breadcrumbs="[
            ['label' => 'Dashboard', 'href' => route('dashboard')],
            ['label' => 'Produits', 'href' => route('admin.produits-pharmaceutiques.index')],
            ['label' => 'Créer']
        ]"
    />
    <x-lobiko.ui.flash />

    <form method="POST" action="{{ route('admin.produits-pharmaceutiques.store') }}">
        @csrf

        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-capsules me-2"></i>Identification</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <x-lobiko.forms.input name="code_produit" label="Code produit" :value="old('code_produit')" required />
                    </div>
                    <div class="col-md-4 mb-3">
                        <x-lobiko.forms.input name="dci" label="DCI" :value="old('dci')" required />
                    </div>
                    <div class="col-md-4 mb-3">
                        <x-lobiko.forms.input name="nom_commercial" label="Nom commercial" :value="old('nom_commercial')" required />
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <x-lobiko.forms.input name="laboratoire" label="Laboratoire" :value="old('laboratoire')" />
                    </div>
                    <div class="col-md-4 mb-3">
                        <x-lobiko.forms.input name="classe_therapeutique" label="Classe thérapeutique" :value="old('classe_therapeutique')" />
                    </div>
                    <div class="col-md-4 mb-3">
                        <x-lobiko.forms.input name="famille" label="Famille" :value="old('famille')" />
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0"><i class="fas fa-syringe me-2"></i>Forme & administration</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 mb-3"><x-lobiko.forms.input name="forme" label="Forme" :value="old('forme')" /></div>
                    <div class="col-md-3 mb-3"><x-lobiko.forms.input name="dosage" label="Dosage" :value="old('dosage')" /></div>
                    <div class="col-md-3 mb-3"><x-lobiko.forms.input name="conditionnement" label="Conditionnement" :value="old('conditionnement')" /></div>
                    <div class="col-md-3 mb-3"><x-lobiko.forms.input name="voie_administration" label="Voie d'administration" :value="old('voie_administration')" /></div>
                </div>
            </div>
        </div>

        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0"><i class="fas fa-euro-sign me-2"></i>Tarifs & stock</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 mb-3"><x-lobiko.forms.input name="prix_unitaire" type="number" step="0.01" label="Prix unitaire" :value="old('prix_unitaire', 0)" required /></div>
                    <div class="col-md-3 mb-3"><x-lobiko.forms.input name="prix_boite" type="number" step="0.01" label="Prix boîte" :value="old('prix_boite', 0)" required /></div>
                    <div class="col-md-3 mb-3"><x-lobiko.forms.input name="stock_minimum" type="number" label="Stock minimum" :value="old('stock_minimum', 10)" /></div>
                    <div class="col-md-3 mb-3"><x-lobiko.forms.input name="stock_alerte" type="number" label="Stock alerte" :value="old('stock_alerte', 20)" /></div>
                </div>
            </div>
        </div>

        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-warning">
                <h5 class="mb-0 text-dark"><i class="fas fa-shield-alt me-2"></i>Réglementaire</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 mb-3 form-check">
                        <input class="form-check-input" type="checkbox" value="1" id="generique" name="generique" {{ old('generique') ? 'checked' : '' }}>
                        <label class="form-check-label" for="generique">Générique</label>
                    </div>
                    <div class="col-md-3 mb-3 form-check">
                        <input class="form-check-input" type="checkbox" value="1" id="prescription_obligatoire" name="prescription_obligatoire" {{ old('prescription_obligatoire', true) ? 'checked' : '' }}>
                        <label class="form-check-label" for="prescription_obligatoire">Prescription obligatoire</label>
                    </div>
                    <div class="col-md-3 mb-3 form-check">
                        <input class="form-check-input" type="checkbox" value="1" id="stupefiant" name="stupefiant" {{ old('stupefiant') ? 'checked' : '' }}>
                        <label class="form-check-label" for="stupefiant">Stupéfiant</label>
                    </div>
                    <div class="col-md-3 mb-3 form-check">
                        <input class="form-check-input" type="checkbox" value="1" id="disponible" name="disponible" {{ old('disponible', true) ? 'checked' : '' }}>
                        <label class="form-check-label" for="disponible">Disponible</label>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3 mb-3 form-check">
                        <input class="form-check-input" type="checkbox" value="1" id="liste_i" name="liste_i" {{ old('liste_i') ? 'checked' : '' }}>
                        <label class="form-check-label" for="liste_i">Liste I</label>
                    </div>
                    <div class="col-md-3 mb-3 form-check">
                        <input class="form-check-input" type="checkbox" value="1" id="liste_ii" name="liste_ii" {{ old('liste_ii') ? 'checked' : '' }}>
                        <label class="form-check-label" for="liste_ii">Liste II</label>
                    </div>
                    <div class="col-md-3 mb-3">
                        <x-lobiko.forms.input name="duree_traitement_max" type="number" label="Durée max (jours)" :value="old('duree_traitement_max')" />
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-light">
                <h5 class="mb-0"><i class="fas fa-file-alt me-2"></i>Remboursement</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 mb-3 form-check">
                        <input class="form-check-input" type="checkbox" value="1" id="remboursable" name="remboursable" {{ old('remboursable', true) ? 'checked' : '' }}>
                        <label class="form-check-label" for="remboursable">Remboursable</label>
                    </div>
                    <div class="col-md-3 mb-3">
                        <x-lobiko.forms.input name="taux_remboursement" type="number" step="0.01" label="Taux remboursement (%)" :value="old('taux_remboursement', 65)" />
                    </div>
                    <div class="col-md-3 mb-3">
                        <x-lobiko.forms.input name="code_cip" label="Code CIP" :value="old('code_cip')" />
                    </div>
                    <div class="col-md-3 mb-3">
                        <x-lobiko.forms.input name="code_ucd" label="Code UCD" :value="old('code_ucd')" />
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-between align-items-center mb-4">
            <a href="{{ route('admin.produits-pharmaceutiques.index') }}" class="btn btn-secondary">
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
