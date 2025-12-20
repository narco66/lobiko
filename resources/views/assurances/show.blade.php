@extends('layouts.app')
@section('title', $assurance->nom_assureur)

@section('content')
<div class="container py-4">
    <x-lobiko.page-header
        title="Assureur"
        subtitle="{{ $assurance->nom_assureur }}"
        :breadcrumbs="[
            ['label' => 'Dashboard', 'href' => route('dashboard')],
            ['label' => 'Assurances', 'href' => route('admin.assurances.index')],
            ['label' => $assurance->nom_assureur]
        ]"
        :actions="[
            ['type' => 'secondary', 'url' => route('admin.assurances.edit', $assurance), 'label' => 'Modifier', 'icon' => 'pen']
        ]"
    />
    <x-lobiko.ui.flash />

    <div class="row g-3">
        <div class="col-lg-8">
            <div class="card shadow-sm mb-3">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0"><i class="fas fa-building me-2"></i>Identité et contact</h6>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <p class="mb-1"><strong>Code :</strong> {{ $assurance->code_assureur }}</p>
                            <p class="mb-1"><strong>Nom légal :</strong> {{ $assurance->nom_assureur }}</p>
                            <p class="mb-1"><strong>Nom commercial :</strong> {{ $assurance->nom_commercial ?? '-' }}</p>
                            <p class="mb-1"><strong>Type :</strong> {{ ucfirst($assurance->type) }}</p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-1"><strong>Email :</strong> {{ $assurance->email }}</p>
                            <p class="mb-1"><strong>Téléphone :</strong> {{ $assurance->telephone }}</p>
                            <p class="mb-1"><strong>Ville :</strong> {{ $assurance->ville }}, {{ $assurance->pays }}</p>
                            <p class="mb-1"><strong>Adresse :</strong> {{ $assurance->adresse }}</p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <p class="mb-1"><strong>Numéro d'agrément :</strong> {{ $assurance->numero_agrement }}</p>
                            <p class="mb-1"><strong>Numéro fiscal :</strong> {{ $assurance->numero_fiscal ?? '-' }}</p>
                            <p class="mb-0"><strong>RCCM :</strong> {{ $assurance->registre_commerce ?? '-' }}</p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-1"><strong>Email médical :</strong> {{ $assurance->email_medical ?? '-' }}</p>
                            <p class="mb-1"><strong>Téléphone médical :</strong> {{ $assurance->telephone_medical ?? '-' }}</p>
                            <p class="mb-0"><strong>Site web :</strong> {{ $assurance->site_web ?? '-' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">
                    <h6 class="mb-0"><i class="fas fa-cog me-2"></i>Paramètres</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <p class="mb-1"><strong>Tiers payant :</strong> {{ $assurance->tiers_payant ? 'Oui' : 'Non' }}</p>
                            <p class="mb-1"><strong>PEC temps réel :</strong> {{ $assurance->pec_temps_reel ? 'Oui' : 'Non' }}</p>
                        </div>
                        <div class="col-md-4">
                            <p class="mb-1"><strong>Délai remboursement :</strong> {{ $assurance->delai_remboursement }} jours</p>
                            <p class="mb-1"><strong>Partenaire :</strong> {{ $assurance->partenaire ? 'Oui' : 'Non' }}</p>
                        </div>
                        <div class="col-md-4">
                            <p class="mb-1"><strong>Date partenariat :</strong> {{ optional($assurance->date_partenariat)->format('d/m/Y') ?? '-' }}</p>
                            <p class="mb-1"><strong>Fin partenariat :</strong> {{ optional($assurance->fin_partenariat)->format('d/m/Y') ?? '-' }}</p>
                        </div>
                    </div>
                    <div class="mt-2">
                        <x-lobiko.ui.badge-status :status="$assurance->actif ? 'actif' : 'suspendu'">Statut</x-lobiko.ui.badge-status>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card shadow-sm mb-3">
                <div class="card-header bg-warning">
                    <h6 class="mb-0 text-dark"><i class="fas fa-info-circle me-2"></i>Actions</h6>
                </div>
                <div class="card-body">
                    <a href="{{ route('admin.assurances.edit', $assurance) }}" class="btn btn-primary w-100 mb-2"><i class="fas fa-edit me-1"></i>Modifier</a>
                    <form action="{{ route('admin.assurances.destroy', $assurance) }}" method="POST" onsubmit="return confirm('Archiver cet assureur ?');">
                        @csrf @method('DELETE')
                        <button class="btn btn-outline-danger w-100"><i class="fas fa-trash me-1"></i>Archiver</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
