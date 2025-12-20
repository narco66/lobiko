@php
use Illuminate\Support\Str;
@endphp

@extends('layouts.app')

@section('title', 'Stocks - '.$pharmacie->nom_pharmacie)

@section('content')
<div class="container py-4">
    <x-lobiko.page-header
        title="Stocks pharmacie"
        subtitle="{{ $pharmacie->nom_pharmacie }}"
        :breadcrumbs="[
            ['label' => 'Dashboard', 'href' => route('dashboard')],
            ['label' => 'Pharmacies', 'href' => route('admin.pharmacies.index')],
            ['label' => 'Stocks']
        ]"
    />

    <x-lobiko.ui.flash />

    <div class="card shadow-sm mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.pharmacies.stocks', $pharmacie) }}" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label">Rechercher</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" name="search" class="form-control" placeholder="Nom, DCI..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Statut stock</label>
                    <select name="statut_stock" class="form-select">
                        <option value="">Tous</option>
                        <option value="disponible" {{ request('statut_stock') == 'disponible' ? 'selected' : '' }}>Disponible</option>
                        <option value="faible" {{ request('statut_stock') == 'faible' ? 'selected' : '' }}>Faible</option>
                        <option value="rupture" {{ request('statut_stock') == 'rupture' ? 'selected' : '' }}>Rupture</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Filtres</label>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="prescription_requise" value="1" {{ request('prescription_requise') ? 'checked' : '' }}>
                        <label class="form-check-label">Prescription requise</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="stock_faible" value="1" {{ request('stock_faible') ? 'checked' : '' }}>
                        <label class="form-check-label">Stock faible</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="expiration_proche" value="1" {{ request('expiration_proche') ? 'checked' : '' }}>
                        <label class="form-check-label">Expiration proche</label>
                    </div>
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary w-100"><i class="fas fa-filter me-1"></i>Filtrer</button>
                    <a href="{{ route('admin.pharmacies.stocks', $pharmacie) }}" class="btn btn-outline-secondary w-100"><i class="fas fa-redo me-1"></i>Réinitialiser</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <x-lobiko.tables.datatable>
                <x-slot name="head">
                    <th>Produit</th>
                    <th>Quantité</th>
                    <th>Statut</th>
                    <th>Lot / Expiration</th>
                    <th>Prix</th>
                    <th>Prescription</th>
                    <th class="text-end">Actions</th>
                </x-slot>
                @forelse($stocks as $stock)
                    <tr>
                        <td>
                            <div class="fw-semibold">{{ $stock->produitPharmaceutique->nom_commercial ?? 'Produit' }}</div>
                            <small class="text-muted">DCI : {{ $stock->produitPharmaceutique->dci ?? '-' }}</small>
                        </td>
                        <td>{{ $stock->quantite_disponible ?? 0 }}</td>
                        <td>
                            <x-lobiko.ui.badge-status :status="$stock->statut_stock ?? 'disponible'"></x-lobiko.ui.badge-status>
                        </td>
                        <td>
                            <div>Lot : {{ $stock->numero_lot ?? '-' }}</div>
                            <div>Exp : {{ $stock->date_expiration ? $stock->date_expiration->format('d/m/Y') : '-' }}</div>
                        </td>
                        <td>
                            <div>Vente : {{ $stock->prix_vente ?? 0 }} FCFA</div>
                            @if($stock->prix_achat)
                                <div class="text-muted small">Achat : {{ $stock->prix_achat }} FCFA</div>
                            @endif
                        </td>
                        <td>{{ $stock->prescription_requise ? 'Oui' : 'Non' }}</td>
                        <td class="text-end">
                            <a href="{{ route('admin.pharmacies.show', $pharmacie) }}" class="btn btn-sm btn-outline-primary"><i class="fas fa-eye"></i></a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7">
                            <x-lobiko.ui.empty-state
                                title="Aucun stock"
                                description="Ajoutez des produits dans cette pharmacie."
                                :action="['label' => 'Retour pharmacie', 'href' => route('admin.pharmacies.show', $pharmacie), 'icon' => 'fas fa-arrow-left']"
                            />
                        </td>
                    </tr>
                @endforelse
            </x-lobiko.tables.datatable>
        </div>
    </div>

    <div class="mt-3">
        {{ $stocks->withQueryString()->links() }}
    </div>
</div>
@endsection
