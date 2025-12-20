@extends('layouts.app')
@section('title', 'Commandes pharmaceutiques')

@section('content')
<div class="container py-4">
    <x-lobiko.page-header
        title="Commandes pharmaceutiques"
        subtitle="Suivi des commandes pharmacie"
        :actions="[['type' => 'primary', 'url' => route('commandes-pharma.create'), 'label' => 'Nouvelle commande', 'icon' => 'plus']]"
    />
    <x-lobiko.ui.flash />

    <div class="card shadow-sm mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('commandes-pharma.index') }}" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label">N° commande</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" name="numero_commande" class="form-control" value="{{ request('numero_commande') }}" placeholder="CMD-...">
                    </div>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Statut</label>
                    <select name="statut" class="form-select">
                        <option value="">Tous</option>
                        @foreach(['en_attente'=>'En attente','confirmee'=>'Confirmée','en_preparation'=>'Préparation','prete'=>'Prête','en_livraison'=>'En livraison','livree'=>'Livrée','annulee'=>'Annulée'] as $value => $label)
                            <option value="{{ $value }}" @selected(request('statut')===$value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Mode</label>
                    <select name="mode_retrait" class="form-select">
                        <option value="">Tous</option>
                        <option value="sur_place" @selected(request('mode_retrait')==='sur_place')>Sur place</option>
                        <option value="livraison" @selected(request('mode_retrait')==='livraison')>Livraison</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Du</label>
                    <input type="date" name="date_debut" class="form-control" value="{{ request('date_debut') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Au</label>
                    <input type="date" name="date_fin" class="form-control" value="{{ request('date_fin') }}">
                </div>
                <div class="col-md-1 form-check mt-4 pt-2">
                    <input type="checkbox" class="form-check-input" id="urgent" name="urgent" value="1" @checked(request()->has('urgent'))>
                    <label class="form-check-label" for="urgent">Urgent</label>
                </div>
                <div class="col-12 text-end">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-filter me-1"></i>Filtrer</button>
                    <a href="{{ route('commandes-pharma.index') }}" class="btn btn-outline-secondary ms-2"><i class="fas fa-redo me-1"></i>Réinitialiser</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <x-lobiko.tables.datatable>
                <x-slot name="head">
                    <th>Commande</th>
                    <th>Patient</th>
                    <th>Pharmacie</th>
                    <th>Montant</th>
                    <th>Statut</th>
                    <th>Mode</th>
                    <th>Date</th>
                    <th class="text-end">Actions</th>
                </x-slot>
                @forelse($commandes as $commande)
                    <tr>
                        <td class="fw-semibold">{{ $commande->numero_commande }}</td>
                        <td>{{ $commande->patient?->name ?? '-' }}</td>
                        <td>{{ $commande->pharmacie?->nom ?? '-' }}</td>
                        <td>{{ number_format($commande->montant_total ?? 0, 0, ',', ' ') }} FCFA</td>
                        <td>
                            <x-lobiko.ui.badge-status :status="$commande->statut ?? 'en_attente'"/>
                            @if($commande->urgent)<span class="badge bg-danger ms-1">Urgent</span>@endif
                        </td>
                        <td>{{ $commande->mode_retrait === 'livraison' ? 'Livraison' : 'Sur place' }}</td>
                        <td>{{ optional($commande->date_commande ?? $commande->created_at)->format('d/m/Y') }}</td>
                        <td class="text-end">
                            <a href="{{ route('commandes-pharma.show', $commande) }}" class="btn btn-sm btn-outline-primary"><i class="fas fa-eye"></i></a>
                            <a href="{{ route('commandes-pharma.bon', $commande) }}" class="btn btn-sm btn-outline-secondary"><i class="fas fa-file-pdf"></i></a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8">
                            <x-lobiko.ui.empty-state
                                title="Aucune commande"
                                description="Aucune commande pour le moment."
                                :action="['label' => 'Nouvelle commande', 'href' => route('commandes-pharma.create'), 'icon' => 'fas fa-plus']"
                            />
                        </td>
                    </tr>
                @endforelse
            </x-lobiko.tables.datatable>
        </div>
    </div>

    <div class="mt-3">
        {{ $commandes->withQueryString()->links() }}
    </div>
</div>
@endsection
