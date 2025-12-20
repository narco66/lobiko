@extends('layouts.app')
@section('title', 'Nouvelle commande pharma')

@php
    $statuts = ['sur_place' => 'Sur place', 'livraison' => 'Livraison'];
@endphp

@section('content')
<div class="container py-4">
    <x-lobiko.page-header
        title="Nouvelle commande"
        subtitle="Créer une commande pharmaceutique"
        :breadcrumbs="[
            ['label' => 'Dashboard', 'href' => route('dashboard')],
            ['label' => 'Commandes', 'href' => route('commandes-pharma.index')],
            ['label' => 'Créer']
        ]"
    />
    <x-lobiko.ui.flash />

    <form method="POST" action="{{ route('commandes-pharma.store') }}">
        @csrf

        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-pills me-2"></i>Informations générales</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <x-lobiko.forms.select
                            name="pharmacie_id"
                            label="Pharmacie"
                            :options="$pharmacies->pluck('nom', 'id')->toArray()"
                            :value="old('pharmacie_id')"
                            placeholder="Choisir une pharmacie"
                            required
                        />
                    </div>
                    <div class="col-md-3 mb-3">
                        <x-lobiko.forms.select name="mode_retrait" label="Mode" :options="$statuts" :value="old('mode_retrait','sur_place')" />
                    </div>
                    <div class="col-md-3 mb-3 form-check mt-4 pt-2">
                        <input class="form-check-input" type="checkbox" id="urgent" name="urgent" value="1" {{ old('urgent') ? 'checked' : '' }}>
                        <label class="form-check-label" for="urgent">Commande urgente</label>
                    </div>
                </div>
                <div class="row livraison-fields" style="{{ old('mode_retrait','sur_place') === 'livraison' ? '' : 'display:none;' }}">
                    <div class="col-md-6 mb-3">
                        <x-lobiko.forms.input name="adresse_livraison" label="Adresse de livraison" :value="old('adresse_livraison')" />
                    </div>
                    <div class="col-md-3 mb-3">
                        <x-lobiko.forms.input name="latitude_livraison" type="number" step="0.000001" label="Latitude" :value="old('latitude_livraison')" />
                    </div>
                    <div class="col-md-3 mb-3">
                        <x-lobiko.forms.input name="longitude_livraison" type="number" step="0.000001" label="Longitude" :value="old('longitude_livraison')" />
                    </div>
                </div>
                <div class="mb-3">
                    <x-lobiko.forms.textarea name="instructions_speciales" label="Instructions spéciales" :value="old('instructions_speciales')" rows="3" />
                </div>
            </div>
        </div>

        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0"><i class="fas fa-capsules me-2"></i>Produits</h5>
            </div>
            <div class="card-body">
                <div id="produits-wrapper">
                    @php $lines = old('produits', $produits ?? [['produit_id'=>'','quantite'=>1]]); @endphp
                    @foreach($lines as $index => $ligne)
                        <div class="row g-2 align-items-end produit-row mb-2">
                            <div class="col-md-5">
                                <label class="form-label">Produit</label>
                                <select name="produits[{{ $index }}][produit_id]" class="form-select" required>
                                    <option value="">Choisir</option>
                                    @foreach($catalogueProduits as $prod)
                                        <option value="{{ $prod->id }}" @selected($ligne['produit_id'] == $prod->id)>{{ $prod->nom_commercial }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <x-lobiko.forms.input name="produits[{{ $index }}][quantite]" type="number" min="1" label="Quantité" :value="$ligne['quantite'] ?? 1" required />
                            </div>
                            <div class="col-md-2">
                                <x-lobiko.forms.input name="produits[{{ $index }}][taux_remboursement]" type="number" step="0.01" min="0" max="100" label="Taux remb. (%)" :value="$ligne['taux_remboursement'] ?? 0" />
                            </div>
                            <div class="col-md-3">
                                <x-lobiko.forms.input name="produits[{{ $index }}][posologie]" label="Posologie" :value="$ligne['posologie'] ?? ''" />
                            </div>
                        </div>
                    @endforeach
                </div>
                <button type="button" class="btn btn-outline-primary btn-sm" id="add-produit"><i class="fas fa-plus me-1"></i>Ajouter un produit</button>
            </div>
        </div>

        <div class="d-flex justify-content-between align-items-center mb-4">
            <a href="{{ route('commandes-pharma.index') }}" class="btn btn-secondary">
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
    const modeSelect = document.querySelector('select[name="mode_retrait"]');
    const livraisonFields = document.querySelector('.livraison-fields');
    const addBtn = document.getElementById('add-produit');
    const wrapper = document.getElementById('produits-wrapper');

    const toggleLivraison = () => {
        if (modeSelect.value === 'livraison') {
            livraisonFields.style.display = '';
        } else {
            livraisonFields.style.display = 'none';
        }
    };
    modeSelect.addEventListener('change', toggleLivraison);
    toggleLivraison();

    addBtn.addEventListener('click', () => {
        const index = wrapper.querySelectorAll('.produit-row').length;
        const row = document.createElement('div');
        row.className = 'row g-2 align-items-end produit-row mb-2';
        row.innerHTML = `
            <div class="col-md-5">
                <label class="form-label">Produit</label>
                <select name="produits[${index}][produit_id]" class="form-select" required>
                    <option value="">Choisir</option>
                    @foreach($catalogueProduits as $prod)
                        <option value="{{ $prod->id }}">{{ $prod->nom_commercial }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Quantité</label>
                <input type="number" name="produits[${index}][quantite]" class="form-control" value="1" min="1" required>
            </div>
            <div class="col-md-2">
                <label class="form-label">Taux remb. (%)</label>
                <input type="number" name="produits[${index}][taux_remboursement]" class="form-control" step="0.01" min="0" max="100">
            </div>
            <div class="col-md-3">
                <label class="form-label">Posologie</label>
                <input type="text" name="produits[${index}][posologie]" class="form-control">
            </div>
        `;
        wrapper.appendChild(row);
    });
});
</script>
@endpush
@endsection
