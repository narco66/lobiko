@extends('layouts.app')

@section('title', 'Profil praticien')

@php
    $name = trim(($doctor->prenom ?? '') . ' ' . ($doctor->nom ?? ''));
    $name = $name !== '' ? $name : ($doctor->name ?? 'Praticien');
    $speciality = $doctor->specialite ?? 'Spécialité non renseignée';
    $structureName = $structure->nom_structure ?? 'Structure non renseignée';
    $cityLabel = $city ?: 'Ville non renseignée';
@endphp

@section('content')
<div class="container py-5">
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body d-flex flex-column flex-md-row gap-4">
                    <div class="flex-shrink-0">
                        <div class="rounded-circle bg-primary text-white fw-bold d-flex align-items-center justify-content-center" style="width:96px; height:96px; font-size:28px;">
                            {{ strtoupper(mb_substr($doctor->prenom ?? $doctor->nom ?? 'P', 0, 1) . mb_substr($doctor->nom ?? $doctor->prenom ?? 'R', 0, 1)) }}
                        </div>
                    </div>
                    <div>
                        <p class="text-uppercase text-primary fw-semibold small mb-1">Profil praticien</p>
                        <h1 class="h3 fw-bold mb-1">{{ $name }}</h1>
                        <p class="text-muted mb-2">{{ $speciality }}</p>
                        <p class="mb-2">
                            <i class="fas fa-map-marker-alt text-primary me-2"></i>{{ $cityLabel }}
                            <span class="text-muted">·</span>
                            <i class="fas fa-hospital text-primary me-2 ms-1"></i>{{ $structureName }}
                        </p>
                        <div class="d-flex flex-wrap gap-2">
                            <a href="{{ route('appointments.create', ['practitioner_id' => $doctor->id, 'structure_id' => optional($structure)->id, 'speciality' => $speciality]) }}" class="btn btn-primary rounded-pill">
                                <i class="fas fa-calendar-plus me-2"></i> Prendre rendez-vous
                            </a>
                            <a href="{{ route('services.teleconsultation') }}" class="btn btn-outline-primary rounded-pill">
                                <i class="fas fa-video me-2"></i> Téléconsultation
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body">
                    <h5 class="fw-bold mb-3">Présentation</h5>
                    <p class="text-muted mb-0">
                        {{ $doctor->bio ?? "Ce praticien exerce en $speciality. Vous pouvez prendre rendez-vous ou demander une téléconsultation en ligne." }}
                    </p>
                </div>
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h5 class="fw-bold mb-3">Informations pratiques</h5>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <p class="mb-1 text-muted">Structure</p>
                            <p class="fw-semibold mb-0">{{ $structureName }}</p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-1 text-muted">Ville</p>
                            <p class="fw-semibold mb-0">{{ $cityLabel }}</p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-1 text-muted">Spécialité</p>
                            <p class="fw-semibold mb-0">{{ $speciality }}</p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-1 text-muted">Email</p>
                            <p class="fw-semibold mb-0">{{ $doctor->email ?? 'Non renseigné' }}</p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-1 text-muted">Téléphone</p>
                            <p class="fw-semibold mb-0">{{ $doctor->telephone ?? 'Non renseigné' }}</p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-1 text-muted">Expérience</p>
                            <p class="fw-semibold mb-0">{{ $stats['experience'] }} ans (indicatif)</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm border-0 mb-3">
                <div class="card-body">
                    <h6 class="fw-bold mb-3">Disponibilités</h6>
                    <p class="text-muted mb-2">Contactez-nous pour proposer un créneau selon vos préférences (présentiel ou téléconsultation).</p>
                    <a href="{{ route('appointments.create', ['practitioner_id' => $doctor->id, 'structure_id' => optional($structure)->id, 'speciality' => $speciality]) }}" class="btn btn-gradient w-100">
                        <i class="fas fa-calendar-check me-2"></i> Réserver un créneau
                    </a>
                </div>
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h6 class="fw-bold mb-3">Notes</h6>
                    <p class="mb-1">
                        <i class="fas fa-star text-warning me-1"></i>
                        {{ $stats['note_moyenne'] ? number_format($stats['note_moyenne'], 1) . '/5' : 'Pas encore noté' }}
                    </p>
                    <p class="text-muted mb-0">{{ $stats['nombre_evaluations'] }} avis</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
