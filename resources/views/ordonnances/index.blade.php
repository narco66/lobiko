@extends('layouts.app')
@section('title', 'Ordonnances')

@section('content')
<div class="container py-4">
    <x-lobiko.page-header
        title="Ordonnances"
        subtitle="Gestion des prescriptions"
        :actions="[['type' => 'primary', 'url' => route('ordonnances.create'), 'label' => 'Nouvelle ordonnance', 'icon' => 'plus']]"
    />
    <x-lobiko.ui.flash />

    <div class="row g-3 mb-3">
        <div class="col-md-3">
            <div class="card shadow-sm border-0">
                <div class="card-body text-center">
                    <div class="fw-bold fs-4">{{ $stats['total'] ?? 0 }}</div>
                    <div class="text-muted">Total</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0">
                <div class="card-body text-center">
                    <div class="fw-bold fs-4">{{ $stats['actives'] ?? 0 }}</div>
                    <div class="text-muted">Actives</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0">
                <div class="card-body text-center">
                    <div class="fw-bold fs-4">{{ $stats['dispensees'] ?? 0 }}</div>
                    <div class="text-muted">Dispensées</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0">
                <div class="card-body text-center">
                    <div class="fw-bold fs-4">{{ $stats['expirees'] ?? 0 }}</div>
                    <div class="text-muted">Expirées</div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('ordonnances.index') }}" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label">Recherche</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="Numéro, diagnostic, patient">
                    </div>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Statut</label>
                    <select name="statut" class="form-select">
                        <option value="">Tous</option>
                        @foreach(['active'=>'Active','dispensee'=>'Dispensée','expiree'=>'Expirée','annulee'=>'Annulée'] as $value => $label)
                            <option value="{{ $value }}" @selected(request('statut')===$value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Patient</label>
                    <input type="text" name="patient" class="form-control" value="{{ request('patient') }}" placeholder="ID patient">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Praticien</label>
                    <input type="text" name="praticien" class="form-control" value="{{ request('praticien') }}" placeholder="ID praticien">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Du</label>
                    <input type="date" name="date_debut" class="form-control" value="{{ request('date_debut') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Au</label>
                    <input type="date" name="date_fin" class="form-control" value="{{ request('date_fin') }}">
                </div>
                <div class="col-12 text-end">
                    <button class="btn btn-primary"><i class="fas fa-filter me-1"></i>Filtrer</button>
                    <a href="{{ route('ordonnances.index') }}" class="btn btn-outline-secondary ms-2"><i class="fas fa-redo me-1"></i>Réinitialiser</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <x-lobiko.tables.datatable>
                <x-slot name="head">
                    <th>Numéro</th>
                    <th>Patient</th>
                    <th>Praticien</th>
                    <th>Type</th>
                    <th>Statut</th>
                    <th>Date</th>
                    <th class="text-end">Actions</th>
                </x-slot>
                @forelse($ordonnances as $ordonnance)
                    <tr>
                        <td class="fw-semibold">{{ $ordonnance->numero_ordonnance }}</td>
                        <td>{{ $ordonnance->patient?->name ?? '-' }}</td>
                        <td>{{ $ordonnance->praticien?->name ?? '-' }}</td>
                        <td>{{ ucfirst($ordonnance->type_ordonnance ?? 'normale') }}</td>
                        <td><x-lobiko.ui.badge-status :status="$ordonnance->statut ?? 'active'"/></td>
                        <td>{{ optional($ordonnance->date_ordonnance)->format('d/m/Y') }}</td>
                        <td class="text-end">
                            <a href="{{ route('ordonnances.show', $ordonnance) }}" class="btn btn-sm btn-outline-primary"><i class="fas fa-eye"></i></a>
                            <a href="{{ route('ordonnances.edit', $ordonnance) }}" class="btn btn-sm btn-outline-secondary"><i class="fas fa-pen"></i></a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7">
                            <x-lobiko.ui.empty-state
                                title="Aucune ordonnance"
                                description="Ajoutez une ordonnance."
                                :action="['label' => 'Nouvelle ordonnance', 'href' => route('ordonnances.create'), 'icon' => 'fas fa-plus']"
                            />
                        </td>
                    </tr>
                @endforelse
            </x-lobiko.tables.datatable>
        </div>
    </div>

    <div class="mt-3">
        {{ $ordonnances->links() }}
    </div>
</div>
@endsection
