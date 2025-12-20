@extends('layouts.app')
@section('title', $acte->libelle)

@section('content')
<div class="container py-4">
    <x-lobiko.page-header
        title="Acte médical"
        subtitle="{{ $acte->libelle }}"
        :breadcrumbs="[
            ['label' => 'Dashboard', 'href' => route('dashboard')],
            ['label' => 'Actes médicaux', 'href' => route('admin.actes-medicaux.index')],
            ['label' => $acte->libelle]
        ]"
        :actions="[
            ['type' => 'secondary', 'url' => route('admin.actes-medicaux.edit', $acte), 'label' => 'Modifier', 'icon' => 'pen']
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
                            <p class="mb-1"><strong>Code :</strong> {{ $acte->code_acte }}</p>
                            <p class="mb-1"><strong>Libellé :</strong> {{ $acte->libelle }}</p>
                            <p class="mb-1"><strong>Catégorie :</strong> {{ $acte->categorie }}</p>
                            <p class="mb-0"><strong>Spécialité :</strong> {{ $acte->specialite ?? '-' }}</p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-1"><strong>Tarif base :</strong> {{ number_format($acte->tarif_base ?? 0, 0, ',', ' ') }} FCFA</p>
                            <p class="mb-1"><strong>Durée prévue :</strong> {{ $acte->duree_prevue ?? '-' }} min</p>
                            <p class="mb-0"><strong>Statut :</strong> <x-lobiko.ui.badge-status :status="$acte->actif ? 'actif' : 'suspendu'"/></p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm mb-3">
                <div class="card-header bg-success text-white">
                    <h6 class="mb-0"><i class="fas fa-heartbeat me-2"></i>Options et contraintes</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p class="mb-1"><strong>Urgence :</strong> {{ $acte->urgence_possible ? 'Oui' : 'Non' }}</p>
                            <p class="mb-1"><strong>Téléconsultation :</strong> {{ $acte->teleconsultation_possible ? 'Oui' : 'Non' }}</p>
                            <p class="mb-1"><strong>Domicile :</strong> {{ $acte->domicile_possible ? 'Oui' : 'Non' }}</p>
                            <p class="mb-1"><strong>Remboursable :</strong> {{ $acte->remboursable ? 'Oui' : 'Non' }}</p>
                            <p class="mb-1"><strong>Taux remboursement :</strong> {{ $acte->taux_remboursement_base ?? 0 }}%</p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-1"><strong>Âge min :</strong> {{ $acte->age_minimum ?? '-' }}</p>
                            <p class="mb-1"><strong>Âge max :</strong> {{ $acte->age_maximum ?? '-' }}</p>
                            <p class="mb-1"><strong>Sexe requis :</strong> {{ $acte->sexe_requis }}</p>
                            <p class="mb-1"><strong>Code sécu :</strong> {{ $acte->code_securite_sociale ?? '-' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="fas fa-calendar me-2"></i>Validité</h6>
                </div>
                <div class="card-body">
                    <p class="mb-1"><strong>Début :</strong> {{ optional($acte->date_debut_validite)->format('d/m/Y') ?? '-' }}</p>
                    <p class="mb-1"><strong>Fin :</strong> {{ optional($acte->date_fin_validite)->format('d/m/Y') ?? '-' }}</p>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm">
                <div class="card-header bg-warning">
                    <h6 class="mb-0 text-dark"><i class="fas fa-tools me-2"></i>Actions</h6>
                </div>
                <div class="card-body d-grid gap-2">
                    <a href="{{ route('admin.actes-medicaux.edit', $acte) }}" class="btn btn-primary"><i class="fas fa-edit me-1"></i>Modifier</a>
                    <form action="{{ route('admin.actes-medicaux.destroy', $acte) }}" method="POST" onsubmit="return confirm('Supprimer cet acte ?');">
                        @csrf @method('DELETE')
                        <button class="btn btn-outline-danger"><i class="fas fa-trash me-1"></i>Supprimer</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
