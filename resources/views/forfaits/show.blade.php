@extends('layouts.app')
@section('title', $forfait->nom_forfait)

@section('content')
<div class="container py-4">
    <x-lobiko.page-header
        title="Forfait"
        subtitle="{{ $forfait->nom_forfait }}"
        :breadcrumbs="[
            ['label' => 'Dashboard', 'href' => route('dashboard')],
            ['label' => 'Forfaits', 'href' => route('admin.forfaits.index')],
            ['label' => $forfait->nom_forfait]
        ]"
        :actions="[
            ['type' => 'secondary', 'url' => route('admin.forfaits.edit', $forfait), 'label' => 'Modifier', 'icon' => 'pen']
        ]"
    />
    <x-lobiko.ui.flash />

    <div class="row g-3">
        <div class="col-lg-8">
            <div class="card shadow-sm mb-3">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Informations</h6>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <p class="mb-1"><strong>Code :</strong> {{ $forfait->code_forfait }}</p>
                            <p class="mb-1"><strong>Nom :</strong> {{ $forfait->nom_forfait }}</p>
                            <p class="mb-1"><strong>Catégorie :</strong> {{ $forfait->categorie }}</p>
                            <p class="mb-1"><strong>Prix :</strong> {{ number_format($forfait->prix_forfait ?? 0, 0, ',', ' ') }} FCFA</p>
                            <p class="mb-0"><strong>Statut :</strong> <x-lobiko.ui.badge-status :status="$forfait->actif ? 'actif' : 'suspendu'"/></p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-1"><strong>Durée :</strong> {{ $forfait->duree_validite ?? '-' }} jours</p>
                            <p class="mb-1"><strong>Nombre de séances :</strong> {{ $forfait->nombre_seances ?? '-' }}</p>
                            <p class="mb-1"><strong>Remboursable :</strong> {{ $forfait->remboursable ? 'Oui' : 'Non' }}</p>
                            <p class="mb-1"><strong>Taux remboursement :</strong> {{ $forfait->taux_remboursement ?? 0 }}%</p>
                        </div>
                    </div>
                    <div class="mb-2">
                        <p class="mb-0"><strong>Description :</strong> {{ $forfait->description }}</p>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm mb-3">
                <div class="card-header bg-success text-white">
                    <h6 class="mb-0"><i class="fas fa-list me-2"></i>Composition</h6>
                </div>
                <div class="card-body">
                    <p class="mb-1"><strong>Actes inclus :</strong> {{ $forfait->actes_inclus ? implode(', ', $forfait->actes_inclus) : '-' }}</p>
                    <p class="mb-1"><strong>Produits inclus :</strong> {{ $forfait->produits_inclus ? implode(', ', $forfait->produits_inclus) : '-' }}</p>
                    <p class="mb-1"><strong>Examens inclus :</strong> {{ $forfait->examens_inclus ? implode(', ', $forfait->examens_inclus) : '-' }}</p>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-header bg-warning">
                    <h6 class="mb-0 text-dark"><i class="fas fa-user-shield me-2"></i>Conditions</h6>
                </div>
                <div class="card-body">
                    <p class="mb-1"><strong>Âge min :</strong> {{ $forfait->age_minimum ?? '-' }}</p>
                    <p class="mb-1"><strong>Âge max :</strong> {{ $forfait->age_maximum ?? '-' }}</p>
                    <p class="mb-1"><strong>Sexe requis :</strong> {{ $forfait->sexe_requis }}</p>
                    <p class="mb-1"><strong>Pathologies cibles :</strong> {{ $forfait->pathologies_cibles ? implode(', ', $forfait->pathologies_cibles) : '-' }}</p>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm">
                <div class="card-header bg-warning">
                    <h6 class="mb-0 text-dark"><i class="fas fa-tools me-2"></i>Actions</h6>
                </div>
                <div class="card-body d-grid gap-2">
                    <a href="{{ route('admin.forfaits.edit', $forfait) }}" class="btn btn-primary"><i class="fas fa-edit me-1"></i>Modifier</a>
                    <form action="{{ route('admin.forfaits.destroy', $forfait) }}" method="POST" onsubmit="return confirm('Supprimer ce forfait ?');">
                        @csrf @method('DELETE')
                        <button class="btn btn-outline-danger"><i class="fas fa-trash me-1"></i>Supprimer</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
