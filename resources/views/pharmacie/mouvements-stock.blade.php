@extends('layouts.app')
@section('title', 'Mouvements de stock - '.$pharmacie->nom_pharmacie)

@section('content')
<div class="container py-4">
    <x-lobiko.page-header
        title="Mouvements de stock"
        subtitle="{{ $pharmacie->nom_pharmacie }}"
        :breadcrumbs="[
            ['label' => 'Dashboard', 'href' => route('dashboard')],
            ['label' => 'Pharmacies', 'href' => route('admin.pharmacies.index')],
            ['label' => 'Mouvements']
        ]"
    />

    <x-lobiko.ui.flash />

    <div class="card shadow-sm mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.pharmacies.mouvements-stock', $pharmacie) }}" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label">Type de mouvement</label>
                    <select name="type_mouvement" class="form-select">
                        <option value="">Tous</option>
                        <option value="entree" {{ request('type_mouvement') === 'entree' ? 'selected' : '' }}>Entrée</option>
                        <option value="sortie" {{ request('type_mouvement') === 'sortie' ? 'selected' : '' }}>Sortie</option>
                        <option value="ajustement" {{ request('type_mouvement') === 'ajustement' ? 'selected' : '' }}>Ajustement</option>
                        <option value="perime" {{ request('type_mouvement') === 'perime' ? 'selected' : '' }}>Périmé</option>
                        <option value="retour" {{ request('type_mouvement') === 'retour' ? 'selected' : '' }}>Retour</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Du</label>
                    <input type="date" name="date_debut" class="form-control" value="{{ request('date_debut') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Au</label>
                    <input type="date" name="date_fin" class="form-control" value="{{ request('date_fin') }}">
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary w-100"><i class="fas fa-filter me-1"></i>Filtrer</button>
                    <a href="{{ route('admin.pharmacies.mouvements-stock', $pharmacie) }}" class="btn btn-outline-secondary w-100"><i class="fas fa-redo me-1"></i>Réinitialiser</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <x-lobiko.tables.datatable>
                <x-slot name="head">
                    <th>Date</th>
                    <th>Produit</th>
                    <th>Type</th>
                    <th>Quantité</th>
                    <th>Avant / Après</th>
                    <th>Utilisateur</th>
                    <th>Motif</th>
                </x-slot>
                @forelse($mouvements as $mouvement)
                    <tr>
                        <td>{{ optional($mouvement->created_at)->format('d/m/Y H:i') }}</td>
                        <td>
                            <div class="fw-semibold">{{ $mouvement->nom_commercial }}</div>
                            <small class="text-muted">DCI : {{ $mouvement->dci }}</small>
                        </td>
                        <td><span class="badge bg-secondary text-uppercase">{{ $mouvement->type_mouvement }}</span></td>
                        <td>{{ $mouvement->quantite }}</td>
                        <td>{{ $mouvement->stock_avant }} → {{ $mouvement->stock_apres }}</td>
                        <td>{{ $mouvement->utilisateur_nom ?? '-' }}</td>
                        <td>{{ $mouvement->motif ?? '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7">
                            <x-lobiko.ui.empty-state title="Aucun mouvement" description="Aucun mouvement enregistré pour cette période." />
                        </td>
                    </tr>
                @endforelse
            </x-lobiko.tables.datatable>
        </div>
    </div>

    <div class="mt-3">
        {{ $mouvements->withQueryString()->links() }}
    </div>
</div>
@endsection
