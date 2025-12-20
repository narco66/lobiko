@extends('layouts.app')
@section('title', 'Rendez-vous')

@section('content')
<div class="container py-4">
    <x-lobiko.page-header
        title="Rendez-vous"
        subtitle="Suivi des créneaux"
        :actions="[['type' => 'primary', 'url' => route('appointments.create'), 'label' => 'Nouveau rendez-vous', 'icon' => 'plus']]"
    />

    <div class="row g-3 mb-3">
        @foreach([
            ['label' => 'Total', 'value' => $stats['total'] ?? 0, 'color' => 'primary'],
            ['label' => "Aujourd'hui", 'value' => $stats['aujourd_hui'] ?? 0, 'color' => 'success'],
            ['label' => 'Confirmés', 'value' => $stats['confirmes'] ?? 0, 'color' => 'info'],
            ['label' => 'En attente', 'value' => $stats['en_attente'] ?? 0, 'color' => 'warning'],
        ] as $card)
            <div class="col-6 col-md-3">
                <div class="card shadow-sm border-0">
                    <div class="card-body text-center">
                        <div class="fw-bold fs-4 text-{{ $card['color'] }}">{{ $card['value'] }}</div>
                        <div class="text-muted">{{ $card['label'] }}</div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="card shadow-sm mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('appointments.index') }}" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label">Recherche</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="Numéro, patient, praticien">
                    </div>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Statut</label>
                    <input type="text" name="statut" class="form-control" value="{{ request('statut') }}" placeholder="en_attente">
                </div>
                    <div class="col-md-2">
                        <label class="form-label">Du</label>
                        <input type="date" name="du" class="form-control" value="{{ request('du') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Au</label>
                        <input type="date" name="au" class="form-control" value="{{ request('au') }}">
                    </div>
                <div class="col-md-3 d-flex gap-2">
                    <button class="btn btn-primary w-100" type="submit"><i class="fas fa-filter me-1"></i>Filtrer</button>
                    <a href="{{ route('appointments.index') }}" class="btn btn-outline-secondary w-100"><i class="fas fa-redo me-1"></i>Réinitialiser</a>
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
                    <th>Modalité</th>
                    <th>Statut</th>
                    <th>Date</th>
                    <th class="text-end">Actions</th>
                </x-slot>
                @forelse($upcoming as $rdv)
                    <tr>
                        <td class="fw-semibold">{{ $rdv->numero_rdv ?? $rdv->id }}</td>
                        <td>{{ $rdv->patient?->name ?? '-' }}</td>
                        <td>{{ $rdv->professionnel?->name ?? '-' }}</td>
                        <td>{{ ucfirst($rdv->modalite ?? '-') }}</td>
                        <td><x-lobiko.ui.badge-status :status="$rdv->statut ?? 'en_attente'"/></td>
                        <td>{{ optional($rdv->date_heure)->format('d/m/Y H:i') }}</td>
                        <td class="text-end">
                            <a href="{{ route('appointments.show', $rdv) }}" class="btn btn-sm btn-outline-primary"><i class="fas fa-eye"></i></a>
                            <a href="{{ route('appointments.edit', $rdv) }}" class="btn btn-sm btn-outline-secondary"><i class="fas fa-pen"></i></a>
                            <form action="{{ route('appointments.destroy', $rdv) }}" method="POST" class="d-inline" onsubmit="return confirm('Supprimer ce rendez-vous ?');">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7">
                            <x-lobiko.ui.empty-state
                                title="Aucun rendez-vous"
                                description="Ajoutez un créneau."
                                :action="['label' => 'Nouveau rendez-vous', 'href' => route('appointments.create'), 'icon' => 'fas fa-plus']"
                            />
                        </td>
                    </tr>
                @endforelse
            </x-lobiko.tables.datatable>
        </div>
    </div>

    <div class="mt-3">
        {{ $upcoming->links() }}
    </div>
</div>
@endsection
