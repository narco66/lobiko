@extends('layouts.app')
@section('title', 'Modifier le forfait')

@section('content')
<div class="container py-4">
    <x-lobiko.page-header
        title="Modifier le forfait"
        subtitle="{{ $forfait->nom_forfait }}"
        :breadcrumbs="[
            ['label' => 'Dashboard', 'href' => route('dashboard')],
            ['label' => 'Forfaits', 'href' => route('admin.forfaits.index')],
            ['label' => 'Édition']
        ]"
    />
    <x-lobiko.ui.flash />

    <form method="POST" action="{{ route('admin.forfaits.update', $forfait) }}">
        @csrf
        @method('PUT')

        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-layer-group me-2"></i>Identification</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 mb-3"><x-lobiko.forms.input name="code_forfait" label="Code" :value="old('code_forfait', $forfait->code_forfait)" required /></div>
                    <div class="col-md-8 mb-3"><x-lobiko.forms.input name="nom_forfait" label="Nom du forfait" :value="old('nom_forfait', $forfait->nom_forfait)" required /></div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3"><x-lobiko.forms.input name="categorie" label="Catégorie" :value="old('categorie', $forfait->categorie)" required /></div>
                    <div class="col-md-6 mb-3"><x-lobiko.forms.input name="prix_forfait" type="number" step="0.01" label="Prix" :value="old('prix_forfait', $forfait->prix_forfait)" required /></div>
                </div>
                <div class="mb-3">
                    <x-lobiko.forms.input name="description" label="Description" :value="old('description', $forfait->description)" required />
                </div>
            </div>
        </div>

        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0"><i class="fas fa-clock me-2"></i>Durée et séances</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3"><x-lobiko.forms.input name="duree_validite" type="number" label="Durée (jours)" :value="old('duree_validite', $forfait->duree_validite)" /></div>
                    <div class="col-md-6 mb-3"><x-lobiko.forms.input name="nombre_seances" type="number" label="Nombre de séances" :value="old('nombre_seances', $forfait->nombre_seances)" /></div>
                </div>
            </div>
        </div>

        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0"><i class="fas fa-list me-2"></i>Composition</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label">Actes inclus</label>
                    <input type="text" name="actes_inclus[]" class="form-control" placeholder="Liste d'ID actes séparés par des virgules" value="{{ old('actes_inclus') ? implode(',', old('actes_inclus')) : ($forfait->actes_inclus ? implode(',', $forfait->actes_inclus) : '') }}">
                </div>
                <div class="mb-3">
                    <label class="form-label">Produits inclus</label>
                    <input type="text" name="produits_inclus[]" class="form-control" placeholder="Liste d'ID produits séparés par des virgules" value="{{ old('produits_inclus') ? implode(',', old('produits_inclus')) : ($forfait->produits_inclus ? implode(',', $forfait->produits_inclus) : '') }}">
                </div>
                <div class="mb-3">
                    <label class="form-label">Examens inclus</label>
                    <input type="text" name="examens_inclus[]" class="form-control" placeholder="Liste d'ID examens séparés par des virgules" value="{{ old('examens_inclus') ? implode(',', old('examens_inclus')) : ($forfait->examens_inclus ? implode(',', $forfait->examens_inclus) : '') }}">
                </div>
            </div>
        </div>

        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-warning">
                <h5 class="mb-0 text-dark"><i class="fas fa-user-shield me-2"></i>Conditions et remboursement</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 mb-3"><x-lobiko.forms.input name="age_minimum" type="number" label="Âge minimum" :value="old('age_minimum', $forfait->age_minimum)" /></div>
                    <div class="col-md-4 mb-3"><x-lobiko.forms.input name="age_maximum" type="number" label="Âge maximum" :value="old('age_maximum', $forfait->age_maximum)" /></div>
                    <div class="col-md-4 mb-3">
                        <x-lobiko.forms.select name="sexe_requis" label="Sexe requis" :options="['M'=>'Homme','F'=>'Femme','Tous'=>'Tous']" :value="old('sexe_requis', $forfait->sexe_requis)" />
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Pathologies cibles</label>
                    <input type="text" name="pathologies_cibles[]" class="form-control" placeholder="Liste séparée par des virgules" value="{{ old('pathologies_cibles') ? implode(',', old('pathologies_cibles')) : ($forfait->pathologies_cibles ? implode(',', $forfait->pathologies_cibles) : '') }}">
                </div>
                <div class="row">
                    <div class="col-md-4 mb-3 form-check">
                        <input class="form-check-input" type="checkbox" value="1" id="remboursable" name="remboursable" {{ old('remboursable', $forfait->remboursable) ? 'checked' : '' }}>
                        <label class="form-check-label" for="remboursable">Remboursable</label>
                    </div>
                    <div class="col-md-4 mb-3">
                        <x-lobiko.forms.input name="taux_remboursement" type="number" step="0.01" label="Taux remboursement (%)" :value="old('taux_remboursement', $forfait->taux_remboursement)" />
                    </div>
                    <div class="col-md-4 mb-3 form-check">
                        <input class="form-check-input" type="checkbox" value="1" id="actif" name="actif" {{ old('actif', $forfait->actif) ? 'checked' : '' }}>
                        <label class="form-check-label" for="actif">Actif</label>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-between align-items-center mb-4">
            <a href="{{ route('admin.forfaits.show', $forfait) }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Annuler
            </a>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>Mettre à jour</button>
            </div>
        </div>
    </form>
</div>
@endsection
