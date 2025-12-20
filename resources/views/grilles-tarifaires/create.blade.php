@extends('layouts.app')
@section('title', 'Nouvelle grille tarifaire')

@section('content')
<div class="container py-4">
    <x-lobiko.page-header
        title="Nouvelle grille tarifaire"
        subtitle="Définir une grille pour un type de client"
        :breadcrumbs="[
            ['label' => 'Dashboard', 'href' => route('dashboard')],
            ['label' => 'Grilles tarifaires', 'href' => route('admin.grilles-tarifaires.index')],
            ['label' => 'Créer']
        ]"
    />
    <x-lobiko.ui.flash />

    <form method="POST" action="{{ route('admin.grilles-tarifaires.store') }}">
        @csrf

        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-tag me-2"></i>Identification</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <x-lobiko.forms.input name="nom_grille" label="Nom de la grille" :value="old('nom_grille')" required />
                    </div>
                    <div class="col-md-3 mb-3">
                        <x-lobiko.forms.select name="type_client" label="Type client" :options="['public' => 'Public', 'prive' => 'Privé', 'assure' => 'Assuré', 'indigent' => 'Indigent']" :value="old('type_client')" placeholder="Choisir" required />
                    </div>
                    <div class="col-md-3 mb-3">
                        <x-lobiko.forms.select name="zone" label="Zone" :options="['urbain'=>'Urbain','rural'=>'Rural','periurbain'=>'Périurbain']" :value="old('zone')" placeholder="Choisir" required />
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <x-lobiko.forms.select
                            name="structure_id"
                            label="Structure (optionnel)"
                            :options="$structures->pluck('nom_structure','id')->toArray()"
                            :value="old('structure_id')"
                            placeholder="Grille générale"
                        />
                    </div>
                    <div class="col-md-3 mb-3">
                        <x-lobiko.forms.select name="applicable_a" label="Applicable à" :options="['acte'=>'Acte','produit'=>'Produit','tous'=>'Tous']" :value="old('applicable_a', 'tous')" />
                    </div>
                    <div class="col-md-3 mb-3">
                        <x-lobiko.forms.input name="element_id" label="ID élément (optionnel)" :value="old('element_id')" />
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0"><i class="fas fa-calculator me-2"></i>Tarification</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 mb-3"><x-lobiko.forms.input name="coefficient_multiplicateur" type="number" step="0.01" label="Coefficient" :value="old('coefficient_multiplicateur', 1)" /></div>
                    <div class="col-md-3 mb-3"><x-lobiko.forms.input name="majoration_fixe" type="number" step="0.01" label="Majoration fixe" :value="old('majoration_fixe', 0)" /></div>
                    <div class="col-md-3 mb-3"><x-lobiko.forms.input name="taux_remise" type="number" step="0.01" label="Taux remise (%)" :value="old('taux_remise', 0)" /></div>
                    <div class="col-md-3 mb-3"><x-lobiko.forms.input name="tva_applicable" type="number" step="0.01" label="TVA (%)" :value="old('tva_applicable', 0)" /></div>
                </div>
                <div class="row">
                    <div class="col-md-3 mb-3"><x-lobiko.forms.input name="quantite_min" type="number" label="Quantité min" :value="old('quantite_min')" /></div>
                    <div class="col-md-3 mb-3"><x-lobiko.forms.input name="quantite_max" type="number" label="Quantité max" :value="old('quantite_max')" /></div>
                    <div class="col-md-3 mb-3"><x-lobiko.forms.input name="montant_min" type="number" step="0.01" label="Montant min" :value="old('montant_min')" /></div>
                    <div class="col-md-3 mb-3"><x-lobiko.forms.input name="montant_max" type="number" step="0.01" label="Montant max" :value="old('montant_max')" /></div>
                </div>
            </div>
        </div>

        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-light">
                <h5 class="mb-0"><i class="fas fa-calendar me-2"></i>Validité</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <x-lobiko.forms.input name="date_debut" type="date" label="Date de début" :value="old('date_debut')" required />
                    </div>
                    <div class="col-md-4 mb-3">
                        <x-lobiko.forms.input name="date_fin" type="date" label="Date de fin" :value="old('date_fin')" />
                    </div>
                    <div class="col-md-4 mb-3">
                        <x-lobiko.forms.input name="priorite" type="number" label="Priorité" :value="old('priorite', 0)" />
                    </div>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" value="1" id="actif" name="actif" {{ old('actif', true) ? 'checked' : '' }}>
                    <label class="form-check-label" for="actif">Activer la grille</label>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-between align-items-center mb-4">
            <a href="{{ route('admin.grilles-tarifaires.index') }}" class="btn btn-secondary">
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
