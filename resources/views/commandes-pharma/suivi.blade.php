@extends('layouts.app')
@section('title', 'Suivi commande '.$commande->numero_commande)

@section('content')
<div class="container py-4">
    <x-lobiko.page-header
        title="Suivi de commande"
        subtitle="{{ $commande->numero_commande }}"
        :breadcrumbs="[
            ['label' => 'Dashboard', 'href' => route('dashboard')],
            ['label' => 'Commandes', 'href' => route('commandes-pharma.index')],
            ['label' => 'Suivi']
        ]"
    />

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-6">
                    <p class="mb-1"><strong>Patient :</strong> {{ $commande->patient?->name ?? '-' }}</p>
                    <p class="mb-1"><strong>Pharmacie :</strong> {{ $commande->pharmacie?->nom ?? '-' }}</p>
                </div>
                <div class="col-md-6">
                    <p class="mb-1"><strong>Statut :</strong> <x-lobiko.ui.badge-status :status="$commande->statut"/></p>
                    <p class="mb-1"><strong>Mode :</strong> {{ $commande->mode_retrait === 'livraison' ? 'Livraison' : 'Sur place' }}</p>
                </div>
            </div>
            <div class="row g-3">
                @foreach($historique as $etape)
                    <div class="col-6 col-md-3">
                        <div class="card h-100 {{ $etape['complete'] ? 'border-success' : 'border-light' }}">
                            <div class="card-body text-center">
                                <div class="mb-2">
                                    <i class="fas fa-check-circle {{ $etape['complete'] ? 'text-success' : 'text-muted' }} fa-2x"></i>
                                </div>
                                <h6 class="fw-bold mb-1">{{ $etape['label'] }}</h6>
                                <p class="text-muted mb-0">{{ $etape['date'] ? optional($etape['date'])->format('d/m H:i') : '-' }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
