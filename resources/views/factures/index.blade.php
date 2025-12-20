@extends('layouts.app')
@section('title', 'Factures')

@section('content')
<div class="container py-4">
    <x-lobiko.page-header
        title="Factures"
        subtitle="Liste des factures et reglements"
        :actions="[['type' => 'primary', 'url' => route('admin.factures.create'), 'label' => 'Nouvelle facture', 'icon' => 'plus']]"
    />
    <x-lobiko.ui.flash />

    <div class="card shadow-sm mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.factures.index') }}" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label">Rechercher</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" name="search" class="form-control" placeholder="Numero, patient, praticien" value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Statut</label>
                    <select name="statut" class="form-select">
                        <option value="">Tous</option>
                        <option value="en_attente" {{ request('statut') === 'en_attente' ? 'selected' : '' }}>En attente</option>
                        <option value="paye" {{ request('statut') === 'paye' ? 'selected' : '' }}>Payee</option>
                        <option value="partiel" {{ request('statut') === 'partiel' ? 'selected' : '' }}>Partiel</option>
                        <option value="impaye" {{ request('statut') === 'impaye' ? 'selected' : '' }}>Impayee</option>
                        <option value="annule" {{ request('statut') === 'annule' ? 'selected' : '' }}>Annulee</option>
                        <option value="rembourse" {{ request('statut') === 'rembourse' ? 'selected' : '' }}>Rembourse</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Date du</label>
                    <input type="date" name="du" class="form-control" value="{{ request('du') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Date au</label>
                    <input type="date" name="au" class="form-control" value="{{ request('au') }}">
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary w-100"><i class="fas fa-filter me-1"></i>Filtrer</button>
                    <a href="{{ route('admin.factures.index') }}" class="btn btn-outline-secondary w-100"><i class="fas fa-redo me-1"></i>Reinitialiser</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <x-lobiko.tables.datatable>
                <x-slot name="head">
                    <th>Numero</th>
                    <th>Patient</th>
                    <th>Praticien</th>
                    <th>Montant</th>
                    <th>Statut</th>
                    <th>Date</th>
                    <th class="text-end">Actions</th>
                </x-slot>
                @forelse($factures as $facture)
                    <tr>
                        <td class="fw-semibold">{{ $facture->numero_facture ?? $facture->id }}</td>
                        <td>{{ $facture->patient?->name ?? '-' }}</td>
                        <td>{{ $facture->praticien?->name ?? '-' }}</td>
                        <td>{{ number_format($facture->montant_final ?? 0, 0, ',', ' ') }} FCFA</td>
                        <td><x-lobiko.ui.badge-status :status="$facture->statut_paiement ?? 'brouillon'"/></td>
                        <td>{{ optional($facture->date_facture ?? $facture->created_at)->format('d/m/Y') }}</td>
                        <td class="text-end">
                            <a href="{{ route('admin.factures.show', $facture) }}" class="btn btn-sm btn-outline-primary"><i class="fas fa-eye"></i></a>
                            <a href="{{ route('admin.factures.edit', $facture) }}" class="btn btn-sm btn-outline-secondary"><i class="fas fa-pen"></i></a>
                            <form action="{{ route('admin.factures.destroy', $facture) }}" method="POST" class="d-inline" onsubmit="return confirm('Supprimer cette facture ?');">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7">
                            <x-lobiko.ui.empty-state
                                title="Aucune facture"
                                description="Ajoutez une facture."
                                :action="['label' => 'Nouvelle facture', 'href' => route('admin.factures.create'), 'icon' => 'fas fa-plus']"
                            />
                        </td>
                    </tr>
                @endforelse
            </x-lobiko.tables.datatable>
        </div>
    </div>

    <div class="mt-3">
        {{ $factures->links() }}
    </div>
</div>
@endsection
