@extends('layouts.app')
@section('title', 'Ordonnance '.$ordonnance->numero_ordonnance)

@section('content')
<div class="container py-4">
    <x-lobiko.page-header
        title="Ordonnance"
        subtitle="{{ $ordonnance->numero_ordonnance }}"
        :breadcrumbs="[
            ['label' => 'Ordonnances', 'href' => route('ordonnances.index')],
            ['label' => $ordonnance->numero_ordonnance]
        ]"
        :actions="[
            ['type' => 'secondary', 'url' => route('ordonnances.edit', $ordonnance), 'label' => 'Modifier', 'icon' => 'pen']
        ]"
    />
    <x-lobiko.ui.flash />

    <div class="row g-3">
        <div class="col-lg-8">
            <div class="card shadow-sm mb-3">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Détails</h6>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <p class="mb-1"><strong>Patient :</strong> {{ $ordonnance->patient?->name ?? '-' }}</p>
                            <p class="mb-1"><strong>Praticien :</strong> {{ $ordonnance->praticien?->name ?? '-' }}</p>
                            <p class="mb-1"><strong>Type :</strong> {{ ucfirst($ordonnance->type_ordonnance ?? 'normale') }}</p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-1"><strong>Statut :</strong> <x-lobiko.ui.badge-status :status="$ordonnance->statut ?? 'active'"/></p>
                            <p class="mb-1"><strong>Date :</strong> {{ optional($ordonnance->date_ordonnance)->format('d/m/Y') }}</p>
                            <p class="mb-0"><strong>Valide jusqu'au :</strong> {{ optional($ordonnance->date_expiration)->format('d/m/Y') ?? 'N/A' }}</p>
                        </div>
                    </div>
                    <p class="mb-2"><strong>Diagnostic :</strong> {{ $ordonnance->diagnostic }}</p>
                    <p class="mb-0"><strong>Observations :</strong> {{ $ordonnance->observations ?? 'N/A' }}</p>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0"><i class="fas fa-pills me-2"></i>Prescriptions</h6>
                </div>
                <div class="card-body p-0">
                    <x-lobiko.tables.datatable>
                        <x-slot name="head">
                            <th>Médicament</th>
                            <th>Quantité</th>
                            <th>Posologie</th>
                            <th>Durée</th>
                        </x-slot>
                        @foreach($ordonnance->lignes as $ligne)
                            <tr>
                                <td>{{ $ligne->produit?->nom_commercial ?? $ligne->produit_pharmaceutique_id }}</td>
                                <td>{{ $ligne->quantite }}</td>
                                <td>{{ $ligne->posologie }}</td>
                                <td>{{ $ligne->duree_traitement_jours ?? $ligne->duree_traitement ?? '-' }} jours</td>
                            </tr>
                        @endforeach
                    </x-lobiko.tables.datatable>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card shadow-sm">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <span>QR Code</span>
                    <span class="fw-bold">{{ $ordonnance->numero_ordonnance }}</span>
                </div>
                <div class="card-body text-center">
                    @if($ordonnance->qr_code)
                        <img src="{{ $ordonnance->qr_code }}" alt="QR Ordonnance" class="img-fluid" style="max-width:200px;">
                    @else
                        <p class="text-muted mb-0">QR code non disponible.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
