@extends('layouts.app')
@section('title', 'Rendez-vous '.$rdv->numero_rdv)

@section('content')
<div class="container py-4">
    <x-lobiko.page-header
        title="Rendez-vous"
        subtitle="{{ $rdv->numero_rdv ?? $rdv->id }}"
        :breadcrumbs="[
            ['label' => 'Rendez-vous', 'href' => route('appointments.index')],
            ['label' => $rdv->numero_rdv ?? $rdv->id]
        ]"
        :actions="[
            ['type' => 'secondary', 'url' => route('appointments.edit', $rdv), 'label' => 'Modifier', 'icon' => 'pen']
        ]"
    />
    <x-lobiko.ui.flash />

    <div class="row g-3">
        <div class="col-lg-8">
            <div class="card shadow-sm mb-3">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Détails</h6>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <p class="mb-1"><strong>Patient :</strong> {{ $rdv->patient?->name ?? '-' }}</p>
                            <p class="mb-1"><strong>Praticien :</strong> {{ $rdv->professionnel?->name ?? '-' }}</p>
                            <p class="mb-1"><strong>Structure :</strong> {{ $rdv->structure?->nom_structure ?? '-' }}</p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-1"><strong>Modalité :</strong> {{ ucfirst($rdv->modalite ?? '-') }}</p>
                            <p class="mb-1"><strong>Statut :</strong> <x-lobiko.ui.badge-status :status="$rdv->statut ?? 'en_attente'"/></p>
                            <p class="mb-0"><strong>Date :</strong> {{ optional($rdv->date_heure)->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>
                    <p class="mb-1"><strong>Motif :</strong> {{ $rdv->motif ?? 'N/A' }}</p>
                    <p class="mb-0"><strong>Notes :</strong> {{ $rdv->notes_patient ?? 'N/A' }}</p>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card shadow-sm">
                <div class="card-header bg-warning">
                    <h6 class="mb-0 text-dark"><i class="fas fa-tools me-2"></i>Actions</h6>
                </div>
                <div class="card-body d-grid gap-2">
                    <a href="{{ route('appointments.edit', $rdv) }}" class="btn btn-primary"><i class="fas fa-edit me-1"></i>Modifier</a>
                    <form action="{{ route('appointments.destroy', $rdv) }}" method="POST" onsubmit="return confirm('Supprimer ce rendez-vous ?');">
                        @csrf @method('DELETE')
                        <button class="btn btn-outline-danger"><i class="fas fa-trash me-1"></i>Supprimer</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
