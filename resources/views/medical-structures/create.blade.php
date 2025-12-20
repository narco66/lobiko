@extends('layouts.app')
@section('title', 'Nouvelle structure médicale')

@section('content')
<div class="container py-4">
    <x-lobiko.page-header
        title="Nouvelle structure médicale"
        subtitle="Créer une nouvelle structure médicale"
        :breadcrumbs="[
            ['label' => 'Dashboard', 'href' => route('dashboard')],
            ['label' => 'Structures', 'href' => route('admin.structures.index')],
            ['label' => 'Créer']
        ]"
    />

    <x-lobiko.ui.flash />

    <form method="POST" action="{{ route('admin.structures.store') }}" id="createStructureForm">
        @csrf

        <!-- Informations de base -->
        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Informations de base</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <!-- Code Structure - Généré automatiquement -->
                    <div class="col-md-4 mb-3">
                        <label for="code_structure" class="form-label">
                            Code <span class="text-danger">*</span>
                            <small class="text-muted">(Généré automatiquement)</small>
                        </label>
                        <input
                            type="text"
                            class="form-control bg-light @error('code_structure') is-invalid @enderror"
                            id="code_structure"
                            name="code_structure"
                            value="{{ old('code_structure', $nextCode) }}"
                            readonly
                        >
                        <small class="form-text text-muted">Format: STRXXX (ex: STR001, STR002, ...)</small>
                        @error('code_structure')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Nom Structure -->
                    <div class="col-md-8 mb-3">
                        <label for="nom_structure" class="form-label">Nom de la structure <span class="text-danger">*</span></label>
                        <input
                            type="text"
                            class="form-control @error('nom_structure') is-invalid @enderror"
                            id="nom_structure"
                            name="nom_structure"
                            value="{{ old('nom_structure') }}"
                            required
                            placeholder="Ex: Clinique Sainte Marie"
                        >
                        @error('nom_structure')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <!-- Type Structure -->
                    <div class="col-md-6 mb-3">
                        <label for="type_structure" class="form-label">Type de structure <span class="text-danger">*</span></label>
                        <select
                            class="form-select @error('type_structure') is-invalid @enderror"
                            id="type_structure"
                            name="type_structure"
                            required
                        >
                            <option value="">-- Sélectionnez un type --</option>
                            <option value="cabinet" {{ old('type_structure') == 'cabinet' ? 'selected' : '' }}>Cabinet médical</option>
                            <option value="clinique" {{ old('type_structure') == 'clinique' ? 'selected' : '' }}>Clinique</option>
                            <option value="hopital" {{ old('type_structure') == 'hopital' ? 'selected' : '' }}>Hôpital</option>
                            <option value="pharmacie" {{ old('type_structure') == 'pharmacie' ? 'selected' : '' }}>Pharmacie</option>
                            <option value="laboratoire" {{ old('type_structure') == 'laboratoire' ? 'selected' : '' }}>Laboratoire</option>
                            <option value="centre_imagerie" {{ old('type_structure') == 'centre_imagerie' ? 'selected' : '' }}>Centre d'imagerie</option>
                            <option value="centre_specialise" {{ old('type_structure') == 'centre_specialise' ? 'selected' : '' }}>Centre spécialisé</option>
                        </select>
                        @error('type_structure')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Responsable -->
                    <div class="col-md-6 mb-3">
                        <label for="responsable_id" class="form-label">
                            Responsable <span class="text-danger">*</span>
                        </label>
                        <select
                            class="form-select select2-users @error('responsable_id') is-invalid @enderror"
                            id="responsable_id"
                            name="responsable_id"
                            required
                        >
                            <option value="">-- Rechercher un utilisateur --</option>
                            @foreach($users as $user)
                                <option
                                    value="{{ $user['id'] }}"
                                    {{ old('responsable_id') == $user['id'] ? 'selected' : '' }}
                                    data-roles="{{ $user['roles'] }}"
                                >
                                    {{ $user['text'] }}
                                </option>
                            @endforeach
                        </select>
                        <small class="form-text text-muted">
                            <i class="fas fa-search me-1"></i>Tapez pour rechercher par nom, prénom ou email
                        </small>
                        @error('responsable_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Statut -->
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="statut" class="form-label">Statut <span class="text-danger">*</span></label>
                        <select
                            class="form-select @error('statut') is-invalid @enderror"
                            id="statut"
                            name="statut"
                            required
                        >
                            <option value="actif" {{ old('statut', 'actif') == 'actif' ? 'selected' : '' }}>
                                ✓ Actif
                            </option>
                            <option value="en_validation" {{ old('statut') == 'en_validation' ? 'selected' : '' }}>
                                ⏱ En validation
                            </option>
                            <option value="suspendu" {{ old('statut') == 'suspendu' ? 'selected' : '' }}>
                                ⏸ Suspendu
                            </option>
                            <option value="ferme" {{ old('statut') == 'ferme' ? 'selected' : '' }}>
                                ✕ Fermé
                            </option>
                        </select>
                        @error('statut')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Coordonnées et Contact -->
        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0"><i class="fas fa-phone me-2"></i>Coordonnées et Contact</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="telephone_principal" class="form-label">Téléphone principal <span class="text-danger">*</span></label>
                        <input
                            type="tel"
                            class="form-control @error('telephone_principal') is-invalid @enderror"
                            id="telephone_principal"
                            name="telephone_principal"
                            value="{{ old('telephone_principal') }}"
                            required
                            placeholder="+241 01 23 45 67"
                        >
                        @error('telephone_principal')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                        <input
                            type="email"
                            class="form-control @error('email') is-invalid @enderror"
                            id="email"
                            name="email"
                            value="{{ old('email') }}"
                            required
                            placeholder="contact@structure.com"
                        >
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Localisation -->
        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0"><i class="fas fa-map-marker-alt me-2"></i>Localisation</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label for="adresse_rue" class="form-label">Adresse complète <span class="text-danger">*</span></label>
                        <input
                            type="text"
                            class="form-control @error('adresse_rue') is-invalid @enderror"
                            id="adresse_rue"
                            name="adresse_rue"
                            value="{{ old('adresse_rue') }}"
                            required
                            placeholder="Ex: Avenue du Colonel Parant"
                        >
                        @error('adresse_rue')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="adresse_quartier" class="form-label">Quartier <span class="text-danger">*</span></label>
                        <input
                            type="text"
                            class="form-control @error('adresse_quartier') is-invalid @enderror"
                            id="adresse_quartier"
                            name="adresse_quartier"
                            value="{{ old('adresse_quartier') }}"
                            required
                            placeholder="Ex: Glass"
                        >
                        @error('adresse_quartier')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="adresse_ville" class="form-label">Ville <span class="text-danger">*</span></label>
                        <input
                            type="text"
                            class="form-control @error('adresse_ville') is-invalid @enderror"
                            id="adresse_ville"
                            name="adresse_ville"
                            value="{{ old('adresse_ville') }}"
                            required
                            placeholder="Ex: Libreville"
                        >
                        @error('adresse_ville')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="adresse_pays" class="form-label">Pays <span class="text-danger">*</span></label>
                        <input
                            type="text"
                            class="form-control @error('adresse_pays') is-invalid @enderror"
                            id="adresse_pays"
                            name="adresse_pays"
                            value="{{ old('adresse_pays', 'Gabon') }}"
                            required
                        >
                        @error('adresse_pays')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="latitude" class="form-label">
                            Latitude <span class="text-danger">*</span>
                            <small class="text-muted">(Coordonnées GPS)</small>
                        </label>
                        <input
                            type="number"
                            step="any"
                            class="form-control @error('latitude') is-invalid @enderror"
                            id="latitude"
                            name="latitude"
                            value="{{ old('latitude') }}"
                            required
                            placeholder="0.4162"
                        >
                        @error('latitude')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="longitude" class="form-label">
                            Longitude <span class="text-danger">*</span>
                            <small class="text-muted">(Coordonnées GPS)</small>
                        </label>
                        <input
                            type="number"
                            step="any"
                            class="form-control @error('longitude') is-invalid @enderror"
                            id="longitude"
                            name="longitude"
                            value="{{ old('longitude') }}"
                            required
                            placeholder="9.4673"
                        >
                        @error('longitude')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Astuce:</strong> Vous pouvez obtenir les coordonnées GPS en recherchant l'adresse sur
                    <a href="https://www.google.com/maps" target="_blank" class="alert-link">Google Maps</a>
                    puis clic droit > "Que trouve-t-on ici ?"
                </div>
            </div>
        </div>

        <!-- Boutons d'action -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <a href="{{ route('admin.structures.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Retour
            </a>
            <div class="d-flex gap-2">
                <button type="reset" class="btn btn-outline-secondary">
                    <i class="fas fa-redo me-2"></i>Réinitialiser
                </button>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-2"></i>Enregistrer la structure
                </button>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Initialiser Select2 pour le champ responsable
    $('.select2-users').select2({
        theme: 'bootstrap-5',
        placeholder: '-- Rechercher un utilisateur --',
        allowClear: true,
        width: '100%',
        templateResult: formatUser,
        templateSelection: formatUserSelection
    });

    // Format de l'affichage dans la liste déroulante
    function formatUser(user) {
        if (!user.id) {
            return user.text;
        }

        var roles = $(user.element).data('roles');
        var $user = $(
            '<div class="d-flex flex-column">' +
                '<div>' + user.text + '</div>' +
                (roles ? '<small class="text-muted"><i class="fas fa-user-tag me-1"></i>' + roles + '</small>' : '') +
            '</div>'
        );
        return $user;
    }

    // Format de l'affichage dans le champ sélectionné
    function formatUserSelection(user) {
        return user.text;
    }

    // Validation du formulaire
    $('#createStructureForm').on('submit', function(e) {
        var isValid = true;
        var errors = [];

        // Vérifier les champs requis
        if (!$('#nom_structure').val()) {
            errors.push('Le nom de la structure est requis');
            isValid = false;
        }

        if (!$('#type_structure').val()) {
            errors.push('Le type de structure est requis');
            isValid = false;
        }

        if (!$('#responsable_id').val()) {
            errors.push('Le responsable est requis');
            isValid = false;
        }

        if (!$('#telephone_principal').val()) {
            errors.push('Le téléphone principal est requis');
            isValid = false;
        }

        if (!$('#email').val()) {
            errors.push('L\'email est requis');
            isValid = false;
        }

        if (!isValid) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Erreur de validation',
                html: errors.join('<br>'),
                confirmButtonText: 'OK'
            });
        }
    });
});
</script>
@endpush
