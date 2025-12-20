@extends('layouts.app')
@section('title', $grille->nom_grille)

@section('content')
<div class="container py-4">
    <x-lobiko.page-header
        title="Grille tarifaire"
        subtitle="{{ $grille->nom_grille }}"
        :breadcrumbs="[
            ['label' => 'Dashboard', 'href' => route('dashboard')],
            ['label' => 'Grilles tarifaires', 'href' => route('admin.grilles-tarifaires.index')],
            ['label' => $grille->nom_grille]
        ]"
        :actions="[
            ['type' => 'secondary', 'url' => route('admin.grilles-tarifaires.edit', $grille), 'label' => 'Modifier', 'icon' => 'pen']
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
                            <p class="mb-1"><strong>Nom :</strong> {{ $grille->nom_grille }}</p>
                            <p class="mb-1"><strong>Type client :</strong> {{ ucfirst($grille->type_client) }}</p>
                            <p class="mb-1"><strong>Zone :</strong> {{ ucfirst($grille->zone) }}</p>
                            <p class="mb-0"><strong>Structure :</strong> {{ $grille->structure?->nom_structure ?? 'Générale' }}</p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-1"><strong>Applicable à :</strong> {{ ucfirst($grille->applicable_a) }}</p>
                            <p class="mb-1"><strong>ID élément :</strong> {{ $grille->element_id ?? '-' }}</p>
                            <p class="mb-1"><strong>Priorité :</strong> {{ $grille->priorite ?? 0 }}</p>
                            <p class="mb-0"><strong>Statut :</strong> <x-lobiko.ui.badge-status :status="$grille->actif ? 'actif' : 'suspendu'"/></p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">
                    <h6 class="mb-0"><i class="fas fa-calculator me-2"></i>Tarification</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <p class="mb-1"><strong>Coefficient :</strong> {{ $grille->coefficient_multiplicateur ?? 1 }}</p>
                            <p class="mb-1"><strong>Majoration fixe :</strong> {{ $grille->majoration_fixe ?? 0 }} FCFA</p>
                        </div>
                        <div class="col-md-4">
                            <p class="mb-1"><strong>Remise :</strong> {{ $grille->taux_remise ?? 0 }}%</p>
                            <p class="mb-1"><strong>TVA :</strong> {{ $grille->tva_applicable ?? 0 }}%</p>
                        </div>
                        <div class="col-md-4">
                            <p class="mb-1"><strong>Quantité :</strong> {{ $grille->quantite_min ?? 0 }} - {{ $grille->quantite_max ?? '∞' }}</p>
                            <p class="mb-1"><strong>Montant :</strong> {{ $grille->montant_min ?? 0 }} - {{ $grille->montant_max ?? '∞' }}</p>
                        </div>
                    </div>
                    <div class="mt-3">
                        <p class="mb-1"><strong>Validité :</strong> {{ optional($grille->date_debut)->format('d/m/Y') }} - {{ optional($grille->date_fin)->format('d/m/Y') ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm">
                <div class="card-header bg-warning">
                    <h6 class="mb-0 text-dark"><i class="fas fa-tools me-2"></i>Actions</h6>
                </div>
                <div class="card-body d-grid gap-2">
                    <a href="{{ route('admin.grilles-tarifaires.edit', $grille) }}" class="btn btn-primary"><i class="fas fa-edit me-1"></i>Modifier</a>
                    <form action="{{ route('admin.grilles-tarifaires.destroy', $grille) }}" method="POST" onsubmit="return confirm('Supprimer cette grille ?');">
                        @csrf @method('DELETE')
                        <button class="btn btn-outline-danger"><i class="fas fa-trash me-1"></i>Supprimer</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
