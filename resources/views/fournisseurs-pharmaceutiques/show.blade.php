@extends('layouts.app')
@section('title', $fournisseur->nom_fournisseur)

@section('content')
<div class="container py-4">
    <x-lobiko.page-header
        title="Fournisseur"
        subtitle="{{ $fournisseur->nom_fournisseur }}"
        :breadcrumbs="[
            ['label' => 'Dashboard', 'href' => route('dashboard')],
            ['label' => 'Fournisseurs', 'href' => route('admin.fournisseurs-pharmaceutiques.index')],
            ['label' => $fournisseur->nom_fournisseur]
        ]"
        :actions="[
            ['type' => 'secondary', 'url' => route('admin.fournisseurs-pharmaceutiques.edit', $fournisseur), 'label' => 'Modifier', 'icon' => 'pen']
        ]"
    />
    <x-lobiko.ui.flash />

    <div class="row g-3">
        <div class="col-lg-8">
            <div class="card shadow-sm mb-3">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0"><i class="fas fa-truck-medical me-2"></i>Informations</h6>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <p class="mb-1"><strong>Nom :</strong> {{ $fournisseur->nom_fournisseur }}</p>
                            <p class="mb-1"><strong>Licence :</strong> {{ $fournisseur->numero_licence }}</p>
                            <p class="mb-1"><strong>Statut :</strong> <x-lobiko.ui.badge-status :status="$fournisseur->statut === 'actif' ? 'actif' : 'suspendu'"/></p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-1"><strong>Adresse :</strong> {{ $fournisseur->adresse }}</p>
                            <p class="mb-1"><strong>Téléphone :</strong> {{ $fournisseur->telephone }}</p>
                            <p class="mb-1"><strong>Email :</strong> {{ $fournisseur->email ?? '-' }}</p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <p class="mb-1"><strong>Contact :</strong> {{ $fournisseur->personne_contact ?? '-' }}</p>
                            <p class="mb-1"><strong>Téléphone contact :</strong> {{ $fournisseur->telephone_contact ?? '-' }}</p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-1"><strong>Catégories :</strong> {{ $fournisseur->categories_produits ? implode(', ', $fournisseur->categories_produits) : '-' }}</p>
                            <p class="mb-1"><strong>Délai livraison :</strong> {{ $fournisseur->delai_livraison_jours ?? 0 }} jours</p>
                            <p class="mb-1"><strong>Minimum commande :</strong> {{ $fournisseur->montant_minimum_commande ?? 0 }} FCFA</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="fas fa-store me-2"></i>Pharmacies partenaires</h6>
                </div>
                <div class="card-body">
                    @if($fournisseur->pharmacies->isEmpty())
                        <x-lobiko.ui.empty-state title="Aucune pharmacie" description="Aucune relation enregistrée." />
                    @else
                        <ul class="list-group list-group-flush">
                            @foreach($fournisseur->pharmacies as $pharmacie)
                                <li class="list-group-item d-flex justify-content-between">
                                    <div>
                                        <div class="fw-semibold">{{ $pharmacie->nom_pharmacie }}</div>
                                        <small class="text-muted">{{ $pharmacie->adresse_complete }}</small>
                                    </div>
                                    <span class="badge bg-secondary">Licence {{ $pharmacie->numero_licence }}</span>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm">
                <div class="card-header bg-warning">
                    <h6 class="mb-0 text-dark"><i class="fas fa-tools me-2"></i>Actions</h6>
                </div>
                <div class="card-body d-grid gap-2">
                    <a href="{{ route('admin.fournisseurs-pharmaceutiques.edit', $fournisseur) }}" class="btn btn-primary"><i class="fas fa-edit me-1"></i>Modifier</a>
                    <form action="{{ route('admin.fournisseurs-pharmaceutiques.destroy', $fournisseur) }}" method="POST" onsubmit="return confirm('Supprimer ce fournisseur ?');">
                        @csrf @method('DELETE')
                        <button class="btn btn-outline-danger"><i class="fas fa-trash me-1"></i>Supprimer</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
