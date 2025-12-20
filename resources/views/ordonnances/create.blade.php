@extends('layouts.app')
@section('title', 'Nouvelle ordonnance')

@php
    $types = ['normale' => 'Normale', 'secure' => 'Securisée', 'exception' => 'Exception', 'hospitaliere' => 'Hospitalière'];
@endphp

@section('content')
<div class="container py-4">
    <x-lobiko.page-header
        title="Nouvelle ordonnance"
        subtitle="Prescriptions patient"
        :breadcrumbs="[
            ['label' => 'Dashboard', 'href' => route('dashboard')],
            ['label' => 'Ordonnances', 'href' => route('ordonnances.index')],
            ['label' => 'Créer']
        ]"
    />
    <x-lobiko.ui.flash />

    <form method="POST" action="{{ route('ordonnances.store') }}">
        @csrf

        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-user me-2"></i>Informations</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <x-lobiko.forms.select
                            name="patient_id"
                            label="Patient"
                            :options="$patients->pluck('name','id')->toArray()"
                            :value="old('patient_id', $consultation->patient_id ?? '')"
                            placeholder="Sélectionner"
                            required
                        />
                    </div>
                    <div class="col-md-6 mb-3">
                        <x-lobiko.forms.select name="type_ordonnance" label="Type" :options="$types" :value="old('type_ordonnance','normale')" required />
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4 mb-3"><x-lobiko.forms.input name="validite_jours" type="number" min="1" label="Validité (jours)" :value="old('validite_jours', 30)" /></div>
                    <div class="col-md-4 mb-3 form-check mt-4 pt-2">
                        <input class="form-check-input" type="checkbox" id="renouvelable" name="renouvelable" value="1" {{ old('renouvelable', true) ? 'checked' : '' }}>
                        <label class="form-check-label" for="renouvelable">Renouvelable</label>
                    </div>
                    <div class="col-md-4 mb-3"><x-lobiko.forms.input name="nombre_renouvellements" type="number" min="0" label="Renouvellements" :value="old('nombre_renouvellements', 0)" /></div>
                </div>
                <div class="mb-3">
                    <x-lobiko.forms.input name="diagnostic" label="Diagnostic" :value="old('diagnostic')" required />
                </div>
                <div class="mb-3">
                    <x-lobiko.forms.textarea name="observations" label="Observations" :value="old('observations')" rows="3" />
                </div>
            </div>
        </div>

        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0"><i class="fas fa-pills me-2"></i>Prescriptions</h5>
            </div>
            <div class="card-body">
                <div id="lignes-wrapper">
                    @php $lignes = old('lignes', [['produit_id'=>'','quantite'=>1,'posologie'=>'']]); @endphp
                    @foreach($lignes as $index => $ligne)
                        <div class="row g-2 align-items-end ligne-row mb-2">
                            <div class="col-md-4">
                                <label class="form-label">Médicament</label>
                                <select name="lignes[{{ $index }}][produit_id]" class="form-select" required>
                                    <option value="">Choisir</option>
                                    @foreach($produits as $produit)
                                        <option value="{{ $produit->id }}" @selected($ligne['produit_id']==$produit->id)>{{ $produit->nom_commercial }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <x-lobiko.forms.input name="lignes[{{ $index }}][quantite]" type="number" min="1" label="Quantité" :value="$ligne['quantite'] ?? 1" required />
                            </div>
                            <div class="col-md-3">
                                <x-lobiko.forms.input name="lignes[{{ $index }}][posologie]" label="Posologie" :value="$ligne['posologie'] ?? ''" required />
                            </div>
                            <div class="col-md-3">
                                <x-lobiko.forms.input name="lignes[{{ $index }}][duree_traitement]" type="number" min="0" label="Durée (jours)" :value="$ligne['duree_traitement'] ?? ''" />
                            </div>
                        </div>
                    @endforeach
                </div>
                <button type="button" class="btn btn-outline-primary btn-sm" id="add-ligne"><i class="fas fa-plus me-1"></i>Ajouter une ligne</button>
            </div>
        </div>

        <div class="d-flex justify-content-between align-items-center mb-4">
            <a href="{{ route('ordonnances.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Retour
            </a>
            <div class="d-flex gap-2">
                <button type="reset" class="btn btn-outline-secondary">Réinitialiser</button>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>Enregistrer</button>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const addBtn = document.getElementById('add-ligne');
    const wrapper = document.getElementById('lignes-wrapper');
    addBtn.addEventListener('click', () => {
        const index = wrapper.querySelectorAll('.ligne-row').length;
        const row = document.createElement('div');
        row.className = 'row g-2 align-items-end ligne-row mb-2';
        row.innerHTML = `
            <div class="col-md-4">
                <label class="form-label">Médicament</label>
                <select name="lignes[${index}][produit_id]" class="form-select" required>
                    <option value="">Choisir</option>
                    @foreach($produits as $produit)
                        <option value="{{ $produit->id }}">{{ $produit->nom_commercial }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Quantité</label>
                <input type="number" name="lignes[${index}][quantite]" class="form-control" value="1" min="1" required>
            </div>
            <div class="col-md-3">
                <label class="form-label">Posologie</label>
                <input type="text" name="lignes[${index}][posologie]" class="form-control" required>
            </div>
            <div class="col-md-3">
                <label class="form-label">Durée (jours)</label>
                <input type="number" name="lignes[${index}][duree_traitement]" class="form-control" min="0">
            </div>
        `;
        wrapper.appendChild(row);
    });
});
</script>
@endpush
@endsection
