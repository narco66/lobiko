@extends('layouts.app')
@section('title', 'Devis '.$devis->numero_devis)

@section('content')
<div class="container py-4">
    <x-lobiko.page-header
        title="Devis"
        subtitle="{{ $devis->numero_devis ?? $devis->id }}"
        :breadcrumbs="[
            ['label' => 'Dashboard', 'href' => route('dashboard')],
            ['label' => 'Devis', 'href' => route('admin.devis.index')],
            ['label' => $devis->numero_devis ?? $devis->id]
        ]"
        :actions="[
            ['type' => 'secondary', 'url' => route('admin.devis.edit', $devis), 'label' => 'Modifier', 'icon' => 'pen']
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
                            <p class="mb-1"><strong>Numero :</strong> {{ $devis->numero_devis ?? $devis->id }}</p>
                            <p class="mb-1"><strong>Patient :</strong> {{ $devis->patient?->name ?? '-' }}</p>
                            <p class="mb-1"><strong>Praticien :</strong> {{ $devis->praticien?->name ?? '-' }}</p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-1"><strong>Montant :</strong> {{ number_format($devis->montant_final ?? 0, 0, ',', ' ') }} FCFA</p>
                            <p class="mb-1"><strong>Statut :</strong> <x-lobiko.ui.badge-status :status="$devis->statut ?? 'brouillon'"/></p>
                            <p class="mb-0"><strong>Date emission :</strong> {{ optional($devis->date_emission ?? $devis->created_at)->format('d/m/Y') }}</p>
                            <p class="mb-0"><strong>Valide jusqu'au :</strong> {{ optional($devis->date_validite)->format('d/m/Y') ?? 'N/A' }}</p>
                        </div>
                    </div>
                    <p class="mb-0"><strong>Notes internes :</strong> {{ $devis->notes_internes ?? 'N/A' }}</p>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm">
                <div class="card-header bg-warning">
                    <h6 class="mb-0 text-dark"><i class="fas fa-tools me-2"></i>Actions</h6>
                </div>
                <div class="card-body d-grid gap-2">
                    <a href="{{ route('admin.devis.edit', $devis) }}" class="btn btn-primary"><i class="fas fa-edit me-1"></i>Modifier</a>
                    <form action="{{ route('admin.devis.destroy', $devis) }}" method="POST" onsubmit="return confirm('Supprimer ce devis ?');">
                        @csrf @method('DELETE')
                        <button class="btn btn-outline-danger"><i class="fas fa-trash me-1"></i>Supprimer</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
