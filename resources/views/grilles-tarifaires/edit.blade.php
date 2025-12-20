@extends('layouts.app')
@section('title', 'Modifier la grille tarifaire')

@section('content')
<div class="container py-4">
    <x-lobiko.page-header
        title="Modifier la grille"
        subtitle="{{ $grille->nom_grille }}"
        :breadcrumbs="[
            ['label' => 'Dashboard', 'href' => route('dashboard')],
            ['label' => 'Grilles tarifaires', 'href' => route('admin.grilles-tarifaires.index')],
            ['label' => 'Édition']
        ]"
    />
    <x-lobiko.ui.flash />

    <form method="POST" action="{{ route('admin.grilles-tarifaires.update', $grille) }}">
        @csrf
        @method('PUT')

        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-tag me-2"></i>Identification</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <x-lobiko.forms.input name="nom_grille" label="Nom de la grille" :value="old('nom_grille', $grille->nom_grille)" required />
                    </div>
                    <div class="col-md-3 mb-3">
                        <x-lobiko.forms.select name="type_client" label="Type client" :options="['public' => 'Public', 'prive' => 'Privé', 'assure' => 'Assuré', 'indigent' => 'Indigent']" :value="old('type_client', $grille->type_client)" placeholder="Choisir" required />
                    </div>
                    <div class="col-md-3 mb-3">
                        <x-lobiko.forms.select name="zone" label="Zone" :options="['urbain'=>'Urbain','rural'=>'Rural','periurbain'=>'Périurbain']" :value="old('zone', $grille->zone)" placeholder="Choisir" required />
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <x-lobiko.forms.select
                            name="structure_id"
                            label="Structure (optionnel)"
                            :options="$structures->pluck('nom_structure','id')->toArray()"
                            :value="old('structure_id', $grille->structure_id)"
                            placeholder="Grille générale"
                        />
                    </div>
                    <div class="col-md-3 mb-3">
                        <x-lobiko.forms.select name="applicable_a" label="Applicable à" :options="['acte'=>'Acte','produit'=>'Produit','tous'=>'Tous']" :value="old('applicable_a', $grille->applicable_a)" />
                    </div>
                    <div class="col-md-3 mb-3">
                        <x-lobiko.forms.input name="element_id" label="ID élément (optionnel)" :value="old('element_id', $grille->element_id)" />
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
                    <div class="col-md-3 mb-3"><x-lobiko.forms.input name="coefficient_multiplicateur" type="number" step="0.01" label="Coefficient" :value="old('coefficient_multiplicateur', $grille->coefficient_multiplicateur)" /></div>
                    <div class="col-md-3 mb-3"><x-lobiko.forms.input name="majoration_fixe" type="number" step="0.01" label="Majoration fixe" :value="old('majoration_fixe', $grille->majoration_fixe)" /></div>
                    <div class="col-md-3 mb-3"><x-lobiko.forms.input name="taux_remise" type="number" step="0.01" label="Taux remise (%)" :value="old('taux_remise', $grille->taux_remise)" /></div>
                    <div class="col-md-3 mb-3"><x-lobiko.forms.input name="tva_applicable" type="number" step="0.01" label="TVA (%)" :value="old('tva_applicable', $grille->tva_applicable)" /></div>
                </div>
                <div class="row">
                    <div class="col-md-3 mb-3"><x-lobiko.forms.input name="quantite_min" type="number" label="Quantité min" :value="old('quantite_min', $grille->quantite_min)" /></div>
                    <div class="col-md-3 mb-3"><x-lobiko.forms.input name="quantite_max" type="number" label="Quantité max" :value="old('quantite_max', $grille->quantite_max)" /></div>
                    <div class="col-md-3 mb-3"><x-lobiko.forms.input name="montant_min" type="number" step="0.01" label="Montant min" :value="old('montant_min', $grille->montant_min)" /></div>
                    <div class="col-md-3 mb-3"><x-lobiko.forms.input name="montant_max" type="number" step="0.01" label="Montant max" :value="old('montant_max', $grille->montant_max)" /></div>
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
                        <x-lobiko.forms.input name="date_debut" type="date" label="Date de début" :value="old('date_debut', optional($grille->date_debut)->format('Y-m-d'))" required />
                    </div>
                    <div class="col-md-4 mb-3">
                        <x-lobiko.forms.input name="date_fin" type="date" label="Date de fin" :value="old('date_fin', optional($grille->date_fin)->format('Y-m-d'))" />
                    </div>
                    <div class="col-md-4 mb-3">
                        <x-lobiko.forms.input name="priorite" type="number" label="Priorité" :value="old('priorite', $grille->priorite)" />
                    </div>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" value="1" id="actif" name="actif" {{ old('actif', $grille->actif) ? 'checked' : '' }}>
                    <label class="form-check-label" for="actif">Activer la grille</label>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-between align-items-center mb-4">
            <a href="{{ route('admin.grilles-tarifaires.show', $grille) }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Annuler
            </a>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>Mettre à jour</button>
            </div>
        </div>
    </form>
</div>
@endsection
