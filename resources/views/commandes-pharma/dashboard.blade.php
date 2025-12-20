@extends('layouts.app')
@section('title', 'Dashboard pharmacie')

@section('content')
<div class="container py-4">
    <x-lobiko.page-header
        title="Tableau de bord"
        subtitle="{{ $pharmacie->nom ?? 'Pharmacie' }}"
        :breadcrumbs="[
            ['label' => 'Dashboard', 'href' => route('dashboard')],
            ['label' => 'Commandes', 'href' => route('commandes-pharma.index')],
            ['label' => 'Tableau de bord']
        ]"
    />

    <div class="row g-3 mb-3">
        @foreach([
            ['label'=>'Commandes du jour','value'=>$statistiques['commandes_jour'] ?? 0,'color'=>'primary'],
            ['label'=>'En attente','value'=>$statistiques['en_attente'] ?? 0,'color'=>'warning'],
            ['label'=>'En préparation','value'=>$statistiques['en_preparation'] ?? 0,'color'=>'info'],
            ['label'=>'Prêtes','value'=>$statistiques['pretes'] ?? 0,'color'=>'success'],
            ['label'=>'En livraison','value'=>$statistiques['en_livraison'] ?? 0,'color'=>'secondary'],
            ['label'=>'Urgentes','value'=>$statistiques['urgentes'] ?? 0,'color'=>'danger'],
        ] as $card)
            <div class="col-6 col-md-4 col-lg-2">
                <div class="card text-center shadow-sm border-0">
                    <div class="card-body">
                        <div class="fs-4 fw-bold text-{{ $card['color'] }}">{{ $card['value'] }}</div>
                        <div class="text-muted small">{{ $card['label'] }}</div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="row g-3">
        <div class="col-lg-7">
            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>Commandes récentes</span>
                    <a href="{{ route('commandes-pharma.index') }}" class="btn btn-sm btn-outline-primary">Voir tout</a>
                </div>
                <div class="card-body p-0">
                    <x-lobiko.tables.datatable>
                        <x-slot name="head">
                            <th>N°</th><th>Patient</th><th>Montant</th><th>Statut</th>
                        </x-slot>
                        @forelse($commandesRecentes as $commande)
                            <tr>
                                <td>{{ $commande->numero_commande }}</td>
                                <td>{{ $commande->patient?->name ?? '-' }}</td>
                                <td>{{ number_format($commande->montant_total ?? 0, 0, ',', ' ') }} FCFA</td>
                                <td><x-lobiko.ui.badge-status :status="$commande->statut"/></td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-center py-3 text-muted">Aucune commande</td></tr>
                        @endforelse
                    </x-lobiko.tables.datatable>
                </div>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>Livraisons en cours</span>
                </div>
                <div class="card-body">
                    @forelse($livraisonsEnCours as $commande)
                        <div class="border rounded p-2 mb-2">
                            <div class="d-flex justify-content-between">
                                <span class="fw-semibold">{{ $commande->numero_commande }}</span>
                                <span class="badge bg-info">En livraison</span>
                            </div>
                            <div class="small text-muted">Patient : {{ $commande->patient?->name ?? '-' }}</div>
                            <div class="small text-muted">Adresse : {{ $commande->adresse_livraison ?? 'N/A' }}</div>
                        </div>
                    @empty
                        <p class="text-muted mb-0">Aucune livraison en cours.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
