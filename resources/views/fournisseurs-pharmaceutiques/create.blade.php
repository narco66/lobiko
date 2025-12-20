@extends('layouts.app')
@section('title', 'Nouveau fournisseur')

@section('content')
<div class="container py-4">
    <x-lobiko.page-header
        title="Nouveau fournisseur"
        subtitle="Ajouter un fournisseur pharmaceutique"
        :breadcrumbs="[
            ['label' => 'Dashboard', 'href' => route('dashboard')],
            ['label' => 'Fournisseurs', 'href' => route('admin.fournisseurs-pharmaceutiques.index')],
            ['label' => 'Créer']
        ]"
    />
    <x-lobiko.ui.flash />

    <form method="POST" action="{{ route('admin.fournisseurs-pharmaceutiques.store') }}">
        @csrf

        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-truck-medical me-2"></i>Identité</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <x-lobiko.forms.input name="nom_fournisseur" label="Nom" :value="old('nom_fournisseur')" required />
                    </div>
                    <div class="col-md-6 mb-3">
                        <x-lobiko.forms.input name="numero_licence" label="Numéro de licence" :value="old('numero_licence')" required />
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <x-lobiko.forms.input name="telephone" label="Téléphone" :value="old('telephone')" required />
                    </div>
                    <div class="col-md-6 mb-3">
                        <x-lobiko.forms.input name="email" type="email" label="Email" :value="old('email')" />
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <x-lobiko.forms.input name="adresse" label="Adresse" :value="old('adresse')" required />
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0"><i class="fas fa-user-tie me-2"></i>Contact & catégories</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <x-lobiko.forms.input name="personne_contact" label="Personne de contact" :value="old('personne_contact')" />
                    </div>
                    <div class="col-md-6 mb-3">
                        <x-lobiko.forms.input name="telephone_contact" label="Téléphone contact" :value="old('telephone_contact')" />
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-8 mb-3">
                        <label class="form-label">Catégories de produits</label>
                        <input type="text" name="categories_produits[]" class="form-control" placeholder="Ajouter des catégories séparées par des virgules" value="{{ old('categories_produits') ? implode(',', old('categories_produits')) : '' }}">
                        <small class="text-muted">Ex: Antibiotiques, Parapharmacie, Vaccins</small>
                    </div>
                    <div class="col-md-2 mb-3">
                        <x-lobiko.forms.input name="delai_livraison_jours" type="number" label="Délai livraison (jours)" :value="old('delai_livraison_jours', 1)" />
                    </div>
                    <div class="col-md-2 mb-3">
                        <x-lobiko.forms.input name="montant_minimum_commande" type="number" step="0.01" label="Commande minimum" :value="old('montant_minimum_commande', 0)" />
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-light">
                <h5 class="mb-0"><i class="fas fa-toggle-on me-2"></i>Statut</h5>
            </div>
            <div class="card-body">
                <x-lobiko.forms.select name="statut" label="Statut" :options="['actif' => 'Actif', 'inactif' => 'Inactif']" :value="old('statut', 'actif')" />
            </div>
        </div>

        <div class="d-flex justify-content-between align-items-center mb-4">
            <a href="{{ route('admin.fournisseurs-pharmaceutiques.index') }}" class="btn btn-secondary">
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
