@extends('layouts.app')
@section('title', 'Devis')

@section('content')
<div class="container py-4">
    <x-lobiko.page-header
        title="Devis"
        subtitle="Liste des devis"
        :actions="[['type' => 'primary', 'url' => route('admin.devis.create'), 'label' => 'Nouveau devis', 'icon' => 'plus']]"
    />
    <x-lobiko.ui.flash />

    <div class="card shadow-sm mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.devis.index') }}" class="row g-3 align-items-end">
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
                        <option value="brouillon" {{ request('statut') === 'brouillon' ? 'selected' : '' }}>Brouillon</option>
                        <option value="emis" {{ request('statut') === 'emis' ? 'selected' : '' }}>Emis</option>
                        <option value="envoye" {{ request('statut') === 'envoye' ? 'selected' : '' }}>Envoye</option>
                        <option value="accepte" {{ request('statut') === 'accepte' ? 'selected' : '' }}>Accepte</option>
                        <option value="refuse" {{ request('statut') === 'refuse' ? 'selected' : '' }}>Refuse</option>
                        <option value="expire" {{ request('statut') === 'expire' ? 'selected' : '' }}>Expire</option>
                        <option value="converti" {{ request('statut') === 'converti' ? 'selected' : '' }}>Converti</option>
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
                    <a href="{{ route('admin.devis.index') }}" class="btn btn-outline-secondary w-100"><i class="fas fa-redo me-1"></i>Reinitialiser</a>
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
                    <th>Date emission</th>
                    <th class="text-end">Actions</th>
                </x-slot>
                @forelse($devis as $d)
                    <tr>
                        <td class="fw-semibold">{{ $d->numero_devis ?? $d->id }}</td>
                        <td>{{ $d->patient?->name ?? '-' }}</td>
                        <td>{{ $d->praticien?->name ?? '-' }}</td>
                        <td>{{ number_format($d->montant_final ?? 0, 0, ',', ' ') }} FCFA</td>
                        <td><x-lobiko.ui.badge-status :status="$d->statut ?? 'brouillon'"/></td>
                        <td>{{ optional($d->date_emission ?? $d->created_at)->format('d/m/Y') }}</td>
                        <td class="text-end">
                            <a href="{{ route('admin.devis.show', $d) }}" class="btn btn-sm btn-outline-primary"><i class="fas fa-eye"></i></a>
                            <a href="{{ route('admin.devis.edit', $d) }}" class="btn btn-sm btn-outline-secondary"><i class="fas fa-pen"></i></a>
                            <form action="{{ route('admin.devis.destroy', $d) }}" method="POST" class="d-inline" onsubmit="return confirm('Supprimer ce devis ?');">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7">
                            <x-lobiko.ui.empty-state
                                title="Aucun devis"
                                description="Ajoutez un devis."
                                :action="['label' => 'Nouveau devis', 'href' => route('admin.devis.create'), 'icon' => 'fas fa-plus']"
                            />
                        </td>
                    </tr>
                @endforelse
            </x-lobiko.tables.datatable>
        </div>
    </div>

    <div class="mt-3">
        {{ $devis->links() }}
    </div>
</div>
@endsection
