@extends('layouts.app')

@section('title', 'Nouvelle Ordonnance')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h3 mb-0">
                    <i class="fas fa-prescription"></i> Nouvelle Ordonnance
                </h1>
                <a href="{{ route('ordonnances.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Retour
                </a>
            </div>
        </div>
    </div>

    <form action="{{ route('ordonnances.store') }}" method="POST" id="ordonnanceForm">
        @csrf

        <div class="row">
            <div class="col-lg-8">
                <!-- Informations patient -->
                <div class="card shadow mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-user"></i> Informations Patient
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @if($consultation)
                            <input type="hidden" name="consultation_id" value="{{ $consultation->id }}">
                            <input type="hidden" name="patient_id" value="{{ $consultation->patient_id }}">

                            <div class="col-md-12">
                                <div class="alert alert-info">
                                    <strong>Patient :</strong> {{ $consultation->patient->nom }} {{ $consultation->patient->prenom }}<br>
                                    <strong>Consultation :</strong> {{ $consultation->date_consultation->format('d/m/Y H:i') }}<br>
                                    <strong>Motif :</strong> {{ $consultation->motif }}
                                </div>
                            </div>
                            @else
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="patient_id">Patient <span class="text-danger">*</span></label>
                                    <select name="patient_id" id="patient_id" class="form-control select2 @error('patient_id') is-invalid @enderror" required>
                                        <option value="">Sélectionner un patient</option>
                                        @foreach($patients as $patient)
                                            <option value="{{ $patient->id }}"
                                                    {{ old('patient_id') == $patient->id ? 'selected' : '' }}
                                                    data-allergies="{{ $patient->dossierMedical ? json_encode($patient->dossierMedical->allergies) : '[]' }}">
                                                {{ $patient->nom }} {{ $patient->prenom }} - {{ $patient->telephone }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('patient_id')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            @endif
                        </div>

                        <div id="allergiesAlert" class="alert alert-warning d-none">
                            <i class="fas fa-exclamation-triangle"></i>
                            <strong>Attention - Allergies connues :</strong>
                            <span id="allergiesList"></span>
                        </div>
                    </div>
                </div>

                <!-- Diagnostic et observations -->
                <div class="card shadow mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-stethoscope"></i> Diagnostic
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="diagnostic">Diagnostic <span class="text-danger">*</span></label>
                                    <input type="text" name="diagnostic" id="diagnostic"
                                           class="form-control @error('diagnostic') is-invalid @enderror"
                                           value="{{ old('diagnostic', $consultation->diagnostic ?? '') }}"
                                           placeholder="Ex: Hypertension artérielle, Diabète type 2..."
                                           required>
                                    @error('diagnostic')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="observations">Observations</label>
                                    <textarea name="observations" id="observations" rows="3"
                                              class="form-control @error('observations') is-invalid @enderror"
                                              placeholder="Observations complémentaires...">{{ old('observations') }}</textarea>
                                    @error('observations')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Médicaments -->
                <div class="card shadow mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-pills"></i> Médicaments
                        </h5>
                        <button type="button" class="btn btn-sm btn-success" onclick="ajouterLigne()">
                            <i class="fas fa-plus"></i> Ajouter un médicament
                        </button>
                    </div>
                    <div class="card-body">
                        <div id="lignesMedicaments">
                            <!-- Les lignes de médicaments seront ajoutées ici -->
                        </div>

                        <div class="text-right mt-3">
                            <h5>Total estimé : <span id="totalOrdonnance">0</span> FCFA</h5>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <!-- Paramètres de l'ordonnance -->
                <div class="card shadow mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-cog"></i> Paramètres
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="type_ordonnance">Type d'ordonnance</label>
                            <select name="type_ordonnance" id="type_ordonnance" class="form-control">
                                <option value="normale" {{ old('type_ordonnance') == 'normale' ? 'selected' : '' }}>
                                    Normale
                                </option>
                                <option value="secure" {{ old('type_ordonnance') == 'secure' ? 'selected' : '' }}>
                                    Sécurisée (stupéfiants)
                                </option>
                                <option value="exception" {{ old('type_ordonnance') == 'exception' ? 'selected' : '' }}>
                                    Exception
                                </option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="validite_jours">Validité (jours)</label>
                            <input type="number" name="validite_jours" id="validite_jours"
                                   class="form-control"
                                   value="{{ old('validite_jours', 15) }}"
                                   min="1" max="365">
                            <small class="form-text text-muted">
                                Nombre de jours de validité de l'ordonnance
                            </small>
                        </div>

                        <div class="form-group">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input"
                                       name="renouvelable" id="renouvelable"
                                       value="1"
                                       {{ old('renouvelable') ? 'checked' : '' }}>
                                <label class="custom-control-label" for="renouvelable">
                                    Ordonnance renouvelable
                                </label>
                            </div>
                        </div>

                        <div class="form-group" id="nombreRenouvellements" style="display: none;">
                            <label for="nombre_renouvellements">Nombre de renouvellements</label>
                            <input type="number" name="nombre_renouvellements"
                                   id="nombre_renouvellements"
                                   class="form-control"
                                   value="{{ old('nombre_renouvellements', 1) }}"
                                   min="1" max="12">
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="card shadow">
                    <div class="card-body">
                        <button type="submit" class="btn btn-primary btn-block">
                            <i class="fas fa-save"></i> Créer l'ordonnance
                        </button>
                        <button type="button" class="btn btn-secondary btn-block" onclick="sauvegarderBrouillon()">
                            <i class="fas fa-file-alt"></i> Sauvegarder comme brouillon
                        </button>
                        <a href="{{ route('ordonnances.index') }}" class="btn btn-light btn-block">
                            <i class="fas fa-times"></i> Annuler
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Template pour ligne de médicament -->
<template id="ligneMedicamentTemplate">
    <div class="ligne-medicament card mb-3">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Médicament <span class="text-danger">*</span></label>
                        <select name="lignes[INDEX][produit_id]" class="form-control select2-medicament" required onchange="updatePrix(this)">
                            <option value="">Sélectionner un médicament</option>
                            @foreach($produits as $produit)
                                <option value="{{ $produit->id }}"
                                        data-prix="{{ $produit->prix_unitaire }}"
                                        data-stock="{{ $produit->stock_disponible }}">
                                    {{ $produit->nom_commercial }} - {{ $produit->dosage }} {{ $produit->forme }}
                                    ({{ number_format($produit->prix_unitaire, 0, ',', ' ') }} FCFA)
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        <label>Quantité <span class="text-danger">*</span></label>
                        <input type="number" name="lignes[INDEX][quantite]"
                               class="form-control quantite-input"
                               min="1" value="1" required
                               onchange="calculerTotal()">
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        <label>Prix unitaire</label>
                        <input type="text" class="form-control prix-unitaire" readonly value="0 FCFA">
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="form-group">
                        <label>Posologie <span class="text-danger">*</span></label>
                        <input type="text" name="lignes[INDEX][posologie]"
                               class="form-control"
                               placeholder="Ex: 1 comprimé matin et soir"
                               required>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <label>Durée du traitement</label>
                        <div class="input-group">
                            <input type="number" name="lignes[INDEX][duree_traitement]"
                                   class="form-control" min="1" placeholder="Durée">
                            <select name="lignes[INDEX][unite_duree]" class="form-control">
                                <option value="jours">Jours</option>
                                <option value="semaines">Semaines</option>
                                <option value="mois">Mois</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <label>Voie d'administration</label>
                        <select name="lignes[INDEX][voie_administration]" class="form-control">
                            <option value="">Sélectionner</option>
                            <option value="Orale">Orale</option>
                            <option value="Injectable">Injectable</option>
                            <option value="Cutanée">Cutanée</option>
                            <option value="Ophtalmique">Ophtalmique</option>
                            <option value="Nasale">Nasale</option>
                            <option value="Rectale">Rectale</option>
                            <option value="Inhalation">Inhalation</option>
                        </select>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <label>Options</label>
                        <div>
                            <div class="custom-control custom-checkbox custom-control-inline">
                                <input type="checkbox" class="custom-control-input"
                                       name="lignes[INDEX][substitution_autorisee]"
                                       id="substitution_INDEX" value="1">
                                <label class="custom-control-label" for="substitution_INDEX">
                                    Substitution autorisée
                                </label>
                            </div>
                            <div class="custom-control custom-checkbox custom-control-inline">
                                <input type="checkbox" class="custom-control-input"
                                       name="lignes[INDEX][urgence]"
                                       id="urgence_INDEX" value="1">
                                <label class="custom-control-label" for="urgence_INDEX">
                                    Urgent
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="form-group">
                        <label>Instructions spéciales</label>
                        <input type="text" name="lignes[INDEX][instructions_speciales]"
                               class="form-control"
                               placeholder="Ex: À prendre pendant les repas">
                    </div>
                </div>

                <div class="col-md-12 text-right">
                    <button type="button" class="btn btn-sm btn-danger" onclick="supprimerLigne(this)">
                        <i class="fas fa-trash"></i> Supprimer
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
@endsection

@push('scripts')
<script>
let ligneIndex = 0;

$(document).ready(function() {
    // Initialiser Select2
    $('.select2').select2({
        theme: 'bootstrap4',
        width: '100%'
    });

    // Ajouter une première ligne par défaut
    ajouterLigne();

    // Gérer l'affichage du nombre de renouvellements
    $('#renouvelable').change(function() {
        if ($(this).is(':checked')) {
            $('#nombreRenouvellements').show();
        } else {
            $('#nombreRenouvellements').hide();
        }
    });

    // Gérer l'affichage des allergies
    $('#patient_id').change(function() {
        const selectedOption = $(this).find('option:selected');
        const allergies = selectedOption.data('allergies');

        if (allergies && allergies.length > 0) {
            $('#allergiesList').text(allergies.join(', '));
            $('#allergiesAlert').removeClass('d-none');
        } else {
            $('#allergiesAlert').addClass('d-none');
        }
    });

    // Validation du formulaire
    $('#ordonnanceForm').submit(function(e) {
        const lignes = $('.ligne-medicament');
        if (lignes.length === 0) {
            e.preventDefault();
            alert('Veuillez ajouter au moins un médicament');
            return false;
        }
    });
});

function ajouterLigne() {
    const template = document.getElementById('ligneMedicamentTemplate');
    const clone = template.content.cloneNode(true);

    // Remplacer INDEX par l'index actuel
    const html = clone.querySelector('.ligne-medicament').outerHTML.replace(/INDEX/g, ligneIndex);

    $('#lignesMedicaments').append(html);

    // Initialiser Select2 sur le nouveau select
    $(`select[name="lignes[${ligneIndex}][produit_id]"]`).select2({
        theme: 'bootstrap4',
        width: '100%'
    });

    ligneIndex++;
}

function supprimerLigne(button) {
    if ($('.ligne-medicament').length > 1) {
        $(button).closest('.ligne-medicament').remove();
        calculerTotal();
    } else {
        alert('Vous devez avoir au moins un médicament dans l\'ordonnance');
    }
}

function updatePrix(select) {
    const selectedOption = $(select).find('option:selected');
    const prix = selectedOption.data('prix') || 0;
    const stock = selectedOption.data('stock') || 0;

    const ligneCard = $(select).closest('.ligne-medicament');
    ligneCard.find('.prix-unitaire').val(formatMontant(prix));

    // Vérifier le stock
    if (stock <= 0 && selectedOption.val()) {
        alert('Attention : Ce médicament est en rupture de stock');
    }

    calculerTotal();
}

function calculerTotal() {
    let total = 0;

    $('.ligne-medicament').each(function() {
        const select = $(this).find('select[name*="[produit_id]"]');
        const quantite = $(this).find('input[name*="[quantite]"]').val() || 0;
        const prix = select.find('option:selected').data('prix') || 0;

        total += prix * quantite;
    });

    $('#totalOrdonnance').text(formatMontant(total));
}

function formatMontant(montant) {
    return new Intl.NumberFormat('fr-FR').format(montant) + ' FCFA';
}

function sauvegarderBrouillon() {
    // Sauvegarder en localStorage
    const formData = $('#ordonnanceForm').serializeArray();
    localStorage.setItem('ordonnance_brouillon', JSON.stringify(formData));
    alert('Brouillon sauvegardé avec succès');
}
</script>
@endpush

@push('styles')
<style>
.ligne-medicament {
    border-left: 3px solid #007bff;
}
.ligne-medicament:hover {
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
}
</style>
@endpush
