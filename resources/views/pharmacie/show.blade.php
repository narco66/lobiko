@extends('layouts.app')
@section('title', $pharmacie->nom_pharmacie)

@section('content')
<div class="container py-4">
    <x-lobiko.page-header
        title="Pharmacie"
        subtitle="{{ $pharmacie->nom_pharmacie }}"
        :breadcrumbs="[
            ['label' => 'Dashboard', 'href' => route('dashboard')],
            ['label' => 'Pharmacies', 'href' => route('admin.pharmacies.index')],
            ['label' => $pharmacie->nom_pharmacie]
        ]"
        :actions="[
            ['type' => 'secondary', 'url' => route('admin.pharmacies.edit', $pharmacie), 'label' => 'Modifier', 'icon' => 'pen'],
            ['type' => 'primary', 'url' => route('admin.pharmacies.stocks', $pharmacie), 'label' => 'Stocks', 'icon' => 'boxes']
        ]"
    />
    <x-lobiko.ui.flash />

    <div class="row g-3">
        <div class="col-lg-8">
            <div class="card shadow-sm mb-3">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0"><i class="fas fa-clinic-medical me-2"></i>Informations</h6>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <p class="mb-1"><strong>Licence :</strong> {{ $pharmacie->numero_licence }}</p>
                            <p class="mb-1"><strong>Structure :</strong> {{ $pharmacie->structureMedicale?->nom_structure ?? '-' }}</p>
                            <p class="mb-1"><strong>Responsable :</strong> {{ $pharmacie->nom_responsable }}</p>
                            <p class="mb-0"><strong>Statut :</strong> <x-lobiko.ui.badge-status :status="$pharmacie->statut ?? 'inactive'" /></p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-1"><strong>Téléphone :</strong> {{ $pharmacie->telephone_pharmacie }}</p>
                            <p class="mb-1"><strong>Email :</strong> {{ $pharmacie->email_pharmacie ?? '-' }}</p>
                            <p class="mb-1"><strong>Adresse :</strong> {{ $pharmacie->adresse_complete }}</p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <p class="mb-1"><strong>Garde :</strong> {{ $pharmacie->service_garde ? 'Oui' : 'Non' }}</p>
                            <p class="mb-1"><strong>Livraison :</strong> {{ $pharmacie->livraison_disponible ? 'Oui' : 'Non' }}</p>
                            <p class="mb-1"><strong>Rayon livraison :</strong> {{ $pharmacie->rayon_livraison_km ?? 0 }} km</p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-1"><strong>Paiement Mobile Money :</strong> {{ $pharmacie->paiement_mobile_money ? 'Oui' : 'Non' }}</p>
                            <p class="mb-1"><strong>Paiement Carte :</strong> {{ $pharmacie->paiement_carte ? 'Oui' : 'Non' }}</p>
                            <p class="mb-1"><strong>Paiement Espèces :</strong> {{ $pharmacie->paiement_especes ? 'Oui' : 'Non' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="fas fa-clipboard-list me-2"></i>Commandes récentes</h6>
                </div>
                <div class="card-body">
                    @if($commandesRecentes->isEmpty())
                        <x-lobiko.ui.empty-state title="Aucune commande récente" description="Les commandes apparaîtront ici." />
                    @else
                        <ul class="list-group list-group-flush">
                            @foreach($commandesRecentes as $commande)
                                <li class="list-group-item d-flex justify-content-between">
                                    <div>
                                        <div class="fw-semibold">#{{ $commande->numero_commande }}</div>
                                        <small class="text-muted">{{ $commande->patient->name ?? '' }} • {{ $commande->statut }}</small>
                                    </div>
                                    <div class="text-end">
                                        <div>{{ number_format($commande->montant_total, 0, ',', ' ') }} FCFA</div>
                                        <small class="text-muted">{{ optional($commande->created_at)->format('d/m/Y H:i') }}</small>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm mb-3">
                <div class="card-header bg-success text-white">
                    <h6 class="mb-0"><i class="fas fa-chart-line me-2"></i>Statistiques</h6>
                </div>
                <div class="card-body">
                    <div class="mb-2"><strong>Produits en stock :</strong> {{ $statistiques['stocks'] ?? 0 }}</div>
                    <div class="mb-2"><strong>Alertes actives :</strong> {{ $statistiques['alertes_actives'] ?? 0 }}</div>
                    <div class="mb-2"><strong>Commandes :</strong> {{ $statistiques['commandes_total'] ?? 0 }}</div>
                    <div class="mb-0"><strong>Commandes livrées :</strong> {{ $statistiques['commandes_livrees'] ?? 0 }}</div>
                </div>
            </div>

            <div class="card shadow-sm mb-3">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0"><i class="fas fa-bell me-2"></i>Alertes</h6>
                </div>
                <div class="card-body">
                    @if($alertes->isEmpty())
                        <p class="text-muted mb-0">Aucune alerte en cours.</p>
                    @else
                        <ul class="list-group list-group-flush">
                            @foreach($alertes as $alerte)
                                <li class="list-group-item d-flex justify-content-between">
                                    <div>
                                        <div class="fw-semibold">{{ $alerte->type_alerte }}</div>
                                        <small class="text-muted">{{ $alerte->message }}</small>
                                    </div>
                                    <span class="badge bg-warning">Active</span>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-header bg-warning">
                    <h6 class="mb-0 text-dark"><i class="fas fa-tools me-2"></i>Actions</h6>
                </div>
                <div class="card-body d-grid gap-2">
                    <a href="{{ route('admin.pharmacies.stocks', $pharmacie) }}" class="btn btn-outline-primary"><i class="fas fa-boxes me-1"></i>Stocks</a>
                    <a href="{{ route('admin.pharmacies.dashboard', $pharmacie) }}" class="btn btn-outline-info"><i class="fas fa-chart-line me-1"></i>Dashboard</a>
                    <a href="{{ route('admin.pharmacies.edit', $pharmacie) }}" class="btn btn-primary"><i class="fas fa-edit me-1"></i>Modifier</a>
                    <form action="{{ route('admin.pharmacies.destroy', $pharmacie) }}" method="POST" onsubmit="return confirm('Supprimer cette pharmacie ?');">
                        @csrf @method('DELETE')
                        <button class="btn btn-outline-danger"><i class="fas fa-trash me-1"></i>Supprimer</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
