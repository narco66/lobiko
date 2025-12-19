@extends('layouts.app')

@section('title', 'Consultation '.$consultation->numero_consultation)

@section('content')
<div class="container py-4">
    <x-lobiko.page-header title="Consultation {{ $consultation->numero_consultation }}" :breadcrumbs="[
        ['label' => 'Consultations', 'href' => route('consultations.index')],
        ['label' => $consultation->numero_consultation]
    ]" />

    <div class="row g-3">
        <div class="col-lg-6">
            <x-ui.panel title="Détails">
                <dl class="row mb-0">
                    <dt class="col-sm-4">Date</dt>
                    <dd class="col-sm-8">{{ optional($consultation->date_consultation)->format('d/m/Y') }}</dd>

                    <dt class="col-sm-4">Patient</dt>
                    <dd class="col-sm-8">{{ $consultation->patient->nom ?? '' }} {{ $consultation->patient->prenom ?? '' }}</dd>

                    <dt class="col-sm-4">Professionnel</dt>
                    <dd class="col-sm-8">{{ $consultation->professionnel->nom ?? '' }} {{ $consultation->professionnel->prenom ?? '' }}</dd>

                    <dt class="col-sm-4">Modalité</dt>
                    <dd class="col-sm-8"><x-lobiko.ui.badge-status :status="$consultation->modalite">{{ $consultation->modalite }}</x-lobiko.ui.badge-status></dd>

                    <dt class="col-sm-4">Type</dt>
                    <dd class="col-sm-8">{{ ucfirst($consultation->type) }}</dd>
                </dl>
            </x-ui.panel>
        </div>
        <div class="col-lg-6">
            <x-ui.panel title="Synthèse">
                <p class="mb-2"><strong>Motif :</strong> {{ $consultation->motif_consultation }}</p>
                <p class="mb-2"><strong>Diagnostic :</strong> {{ $consultation->diagnostic_principal ?? '—' }}</p>
                <p class="mb-0"><strong>Conduite :</strong> {{ $consultation->conduite_a_tenir ?? '—' }}</p>
            </x-ui.panel>
        </div>
    </div>

    <div class="d-flex justify-content-end gap-2 mt-3">
        <x-lobiko.buttons.secondary href="{{ route('consultations.index') }}">Retour</x-lobiko.buttons.secondary>
        @can('update', $consultation)
            <x-lobiko.buttons.primary href="{{ route('consultations.edit', $consultation) }}" icon="fas fa-edit">Modifier</x-lobiko.buttons.primary>
        @endcan
    </div>
</div>
@endsection
