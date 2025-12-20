@extends('layouts.app')
@section('title', 'Facture '.$facture->numero_facture)

@section('content')
<div class="container py-4">
    <x-lobiko.page-header
        title="Facture"
        subtitle="{{ $facture->numero_facture ?? $facture->id }}"
        :breadcrumbs="[
            ['label' => 'Dashboard', 'href' => route('dashboard')],
            ['label' => 'Factures', 'href' => route('admin.factures.index')],
            ['label' => $facture->numero_facture ?? $facture->id]
        ]"
        :actions="[
            ['type' => 'secondary', 'url' => route('admin.factures.edit', $facture), 'label' => 'Modifier', 'icon' => 'pen']
        ]"
    />
    <x-lobiko.ui.flash />

    <div class="row g-3">
        <div class="col-lg-8">
            <div class="card shadow-sm mb-3">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Details</h6>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <p class="mb-1"><strong>Numero :</strong> {{ $facture->numero_facture ?? $facture->id }}</p>
                            <p class="mb-1"><strong>Patient :</strong> {{ $facture->patient?->name ?? '-' }}</p>
                            <p class="mb-1"><strong>Praticien :</strong> {{ $facture->praticien?->name ?? '-' }}</p>
                            <p class="mb-1"><strong>Type :</strong> {{ ucfirst($facture->type ?? 'consultation') }}</p>
                            <p class="mb-1"><strong>Nature :</strong> {{ ucfirst($facture->nature ?? 'normale') }}</p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-1"><strong>Montant total :</strong> {{ number_format($facture->montant_final ?? 0, 0, ',', ' ') }} FCFA</p>
                            <p class="mb-1"><strong>Statut :</strong> <x-lobiko.ui.badge-status :status="$facture->statut_paiement ?? 'brouillon'"/></p>
                            <p class="mb-0"><strong>Date :</strong> {{ optional($facture->date_facture ?? $facture->created_at)->format('d/m/Y') }}</p>
                        </div>
                    </div>
                    <p class="mb-0"><strong>Notes internes :</strong> {{ $facture->notes_internes ?? 'N/A' }}</p>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm">
                <div class="card-header bg-warning">
                    <h6 class="mb-0 text-dark"><i class="fas fa-tools me-2"></i>Actions</h6>
                </div>
                <div class="card-body d-grid gap-2">
                    <a href="{{ route('admin.factures.edit', $facture) }}" class="btn btn-primary"><i class="fas fa-edit me-1"></i>Modifier</a>
                    <form action="{{ route('admin.factures.destroy', $facture) }}" method="POST" onsubmit="return confirm('Supprimer cette facture ?');">
                        @csrf @method('DELETE')
                        <button class="btn btn-outline-danger"><i class="fas fa-trash me-1"></i>Supprimer</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
