@extends('layouts.app')
@section('title', 'Dossier '.$dossier->numero_dossier)

@section('content')
<div class="container py-4">
    <x-lobiko.page-header
        title="Dossier médical"
        subtitle="{{ $dossier->numero_dossier }}"
        :breadcrumbs="[
            ['label' => 'Dossiers médicaux', 'href' => route('dossiers-medicaux.index')],
            ['label' => $dossier->numero_dossier]
        ]"
        :actions="[
            ['type' => 'secondary', 'url' => route('dossiers-medicaux.edit', $dossier), 'label' => 'Modifier', 'icon' => 'pen']
        ]"
    />

    <div class="row g-3">
        <div class="col-lg-6">
            <x-ui.panel title="Patient">
                <p class="mb-1"><strong>Nom :</strong> {{ $dossier->patient->name ?? '' }}</p>
                <p class="mb-1"><strong>Email :</strong> {{ $dossier->patient->email ?? 'N/A' }}</p>
                <p class="mb-0"><strong>Statut :</strong> <x-lobiko.ui.badge-status :status="$dossier->patient->statut_compte ?? 'actif'">{{ $dossier->patient->statut_compte ?? 'actif' }}</x-lobiko.ui.badge-status></p>
            </x-ui.panel>
        </div>
        <div class="col-lg-6">
            <x-ui.panel title="Suivi">
                <p class="mb-1"><strong>Consultations :</strong> {{ $dossier->nombre_consultations ?? 0 }}</p>
                <p class="mb-1"><strong>Dernière consultation :</strong> {{ optional($dossier->derniere_consultation)->format('d/m/Y H:i') ?? 'N/A' }}</p>
                <p class="mb-0"><strong>Partage :</strong> {{ ($dossier->partage_autorise ?? true) ? 'Autorisé' : 'Restreint' }}</p>
            </x-ui.panel>
        </div>
    </div>

    <div class="row g-3 mt-2">
        <div class="col-lg-4">
            <x-ui.panel title="Constantes habituelles">
                <p class="mb-1">Tension : {{ $dossier->tension_habituelle_sys ?? '-' }}/{{ $dossier->tension_habituelle_dia ?? '-' }}</p>
                <p class="mb-1">Poids : {{ $dossier->poids_habituel ?? '-' }} kg</p>
                <p class="mb-0">Taille : {{ $dossier->taille_cm ?? '-' }} cm | IMC : {{ $dossier->imc ?? '-' }}</p>
            </x-ui.panel>
        </div>
        <div class="col-lg-8">
            <x-ui.panel title="Allergies / Antécédents">
                <p class="mb-1"><strong>Allergies :</strong> {{ implode(', ', $dossier->allergies ?? []) ?: '-' }}</p>
                <p class="mb-0"><strong>Antécédents médicaux :</strong> {{ implode(', ', $dossier->antecedents_medicaux ?? []) ?: '-' }}</p>
            </x-ui.panel>
        </div>
    </div>

    <div class="d-flex justify-content-end gap-2 mt-3">
        <x-lobiko.buttons.secondary href="{{ route('dossiers-medicaux.index') }}">Retour</x-lobiko.buttons.secondary>
        @can('update', $dossier)
            <x-lobiko.buttons.primary href="{{ route('dossiers-medicaux.edit', $dossier) }}" icon="fas fa-edit">Modifier</x-lobiko.buttons.primary>
        @endcan
    </div>
</div>
@endsection
