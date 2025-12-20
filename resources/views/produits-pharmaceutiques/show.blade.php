@extends('layouts.app')
@section('title', $produit->nom_commercial)

@section('content')
<div class="container py-4">
    <x-lobiko.page-header
        title="Produit pharmaceutique"
        subtitle="{{ $produit->nom_commercial }}"
        :breadcrumbs="[
            ['label' => 'Dashboard', 'href' => route('dashboard')],
            ['label' => 'Produits', 'href' => route('admin.produits-pharmaceutiques.index')],
            ['label' => $produit->nom_commercial]
        ]"
        :actions="[
            ['type' => 'secondary', 'url' => route('admin.produits-pharmaceutiques.edit', $produit), 'label' => 'Modifier', 'icon' => 'pen']
        ]"
    />
    <x-lobiko.ui.flash />

    <div class="row g-3">
        <div class="col-lg-8">
            <div class="card shadow-sm mb-3">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0"><i class="fas fa-capsules me-2"></i>Informations générales</h6>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <p class="mb-1"><strong>Code :</strong> {{ $produit->code_produit }}</p>
                            <p class="mb-1"><strong>Nom commercial :</strong> {{ $produit->nom_commercial }}</p>
                            <p class="mb-1"><strong>DCI :</strong> {{ $produit->dci }}</p>
                            <p class="mb-1"><strong>Laboratoire :</strong> {{ $produit->laboratoire ?? '-' }}</p>
                            <p class="mb-1"><strong>Classe thérapeutique :</strong> {{ $produit->classe_therapeutique ?? '-' }}</p>
                            <p class="mb-0"><strong>Famille :</strong> {{ $produit->famille ?? '-' }}</p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-1"><strong>Forme :</strong> {{ $produit->forme ?? '-' }}</p>
                            <p class="mb-1"><strong>Dosage :</strong> {{ $produit->dosage ?? '-' }}</p>
                            <p class="mb-1"><strong>Conditionnement :</strong> {{ $produit->conditionnement ?? '-' }}</p>
                            <p class="mb-1"><strong>Voie :</strong> {{ $produit->voie_administration ?? '-' }}</p>
                            <p class="mb-0"><strong>Princeps :</strong> {{ $produit->princeps ?? '-' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm mb-3">
                <div class="card-header bg-success text-white">
                    <h6 class="mb-0"><i class="fas fa-euro-sign me-2"></i>Tarifs & stocks</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <p class="mb-1"><strong>Prix unitaire :</strong> {{ number_format($produit->prix_unitaire ?? 0, 0, ',', ' ') }} FCFA</p>
                            <p class="mb-1"><strong>Prix boîte :</strong> {{ number_format($produit->prix_boite ?? 0, 0, ',', ' ') }} FCFA</p>
                        </div>
                        <div class="col-md-4">
                            <p class="mb-1"><strong>Stock minimum :</strong> {{ $produit->stock_minimum ?? 0 }}</p>
                            <p class="mb-1"><strong>Stock alerte :</strong> {{ $produit->stock_alerte ?? 0 }}</p>
                        </div>
                        <div class="col-md-4">
                            <p class="mb-1"><strong>Disponible :</strong> {{ $produit->disponible ? 'Oui' : 'Non' }}</p>
                            <p class="mb-1"><strong>Rupture :</strong> {{ $produit->rupture_stock ? 'Oui' : 'Non' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-header bg-warning">
                    <h6 class="mb-0 text-dark"><i class="fas fa-shield-alt me-2"></i>Réglementaire & remboursement</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p class="mb-1"><strong>Générique :</strong> {{ $produit->generique ? 'Oui' : 'Non' }}</p>
                            <p class="mb-1"><strong>Prescription obligatoire :</strong> {{ $produit->prescription_obligatoire ? 'Oui' : 'Non' }}</p>
                            <p class="mb-1"><strong>Liste I :</strong> {{ $produit->liste_i ? 'Oui' : 'Non' }}</p>
                            <p class="mb-1"><strong>Liste II :</strong> {{ $produit->liste_ii ? 'Oui' : 'Non' }}</p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-1"><strong>Remboursable :</strong> {{ $produit->remboursable ? 'Oui' : 'Non' }}</p>
                            <p class="mb-1"><strong>Taux remboursement :</strong> {{ $produit->taux_remboursement ?? 0 }}%</p>
                            <p class="mb-1"><strong>Code CIP :</strong> {{ $produit->code_cip ?? '-' }}</p>
                            <p class="mb-1"><strong>Code UCD :</strong> {{ $produit->code_ucd ?? '-' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm mb-3">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Statut</h6>
                </div>
                <div class="card-body">
                    <p class="mb-2"><strong>Disponible :</strong> {{ $produit->disponible ? 'Oui' : 'Non' }}</p>
                    <p class="mb-2"><strong>Rupture stock :</strong> {{ $produit->rupture_stock ? 'Oui' : 'Non' }}</p>
                    <p class="mb-0"><strong>Mis à jour :</strong> {{ optional($produit->updated_at)->format('d/m/Y H:i') }}</p>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-header bg-warning">
                    <h6 class="mb-0 text-dark"><i class="fas fa-tools me-2"></i>Actions</h6>
                </div>
                <div class="card-body d-grid gap-2">
                    <a href="{{ route('admin.produits-pharmaceutiques.edit', $produit) }}" class="btn btn-primary"><i class="fas fa-edit me-1"></i>Modifier</a>
                    <form action="{{ route('admin.produits-pharmaceutiques.destroy', $produit) }}" method="POST" onsubmit="return confirm('Supprimer ce produit ?');">
                        @csrf @method('DELETE')
                        <button class="btn btn-outline-danger"><i class="fas fa-trash me-1"></i>Supprimer</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
