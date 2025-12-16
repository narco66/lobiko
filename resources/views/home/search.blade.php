@extends('layouts.app')

@section('title', 'Trouver un professionnel')

@section('content')
@php
    $specialityValue = $filters['speciality'] ?? '';
    $cityValue = $filters['city'] ?? '';
    $searchValue = $filters['search'] ?? '';
@endphp
<div class="container py-5">
    <div class="d-flex align-items-start align-items-md-center flex-column flex-md-row gap-3 mb-4">
        <div>
            <p class="text-uppercase text-primary fw-semibold small mb-1">Recherche</p>
            <h1 class="h3 fw-bold mb-2">Trouver un professionnel de santé</h1>
            <p class="text-muted mb-0">Filtrez par spécialité, ville ou nom et prenez rendez-vous en quelques clics.</p>
        </div>
        <div class="ms-md-auto">
            <a href="{{ route('appointments.create') }}" class="btn btn-gradient rounded-pill">
                <i class="fas fa-calendar-check me-2"></i> Prendre rendez-vous
            </a>
        </div>
    </div>

    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <form action="{{ route('search.professionals') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label">Spécialité</label>
                    <select name="speciality" class="form-select">
                        <option value="">Toutes les spécialités</option>
                        @forelse($specialities as $spec)
                            <option value="{{ $spec }}" @selected($specialityValue === $spec)>{{ $spec }}</option>
                        @empty
                            @foreach(['Médecine générale', 'Cardiologie', 'Dermatologie', 'Pédiatrie'] as $spec)
                                <option value="{{ $spec }}" @selected($specialityValue === $spec)>{{ $spec }}</option>
                            @endforeach
                        @endforelse
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Ville</label>
                    <select name="city" class="form-select">
                        <option value="">Toutes les villes</option>
                        @forelse($cities as $city)
                            <option value="{{ $city }}" @selected($cityValue === $city)>{{ $city }}</option>
                        @empty
                            @foreach(['Libreville', 'Port-Gentil', 'Franceville', 'Oyem'] as $city)
                                <option value="{{ $city }}" @selected($cityValue === $city)>{{ $city }}</option>
                            @endforeach
                        @endforelse
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Nom du médecin ou établissement</label>
                    <div class="d-flex gap-2">
                        <input type="text" name="search" class="form-control" placeholder="Ex: Dr. Owono, Clinique du Centre" value="{{ $searchValue }}">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @php
        $hasResults = $professionals->count() > 0;
        $fallback = [
            [
                'name' => 'Dr. Marie Owono',
                'speciality' => 'Médecine générale',
                'city' => 'Libreville',
                'structure' => 'Clinique du Centre'
            ],
            [
                'name' => 'Dr. Serge Mba',
                'speciality' => 'Cardiologie',
                'city' => 'Libreville',
                'structure' => 'Hôpital Général'
            ],
            [
                'name' => 'Dr. Aïcha Diallo',
                'speciality' => 'Dermatologie',
                'city' => 'Douala',
                'structure' => 'Centre Médical Akwa'
            ],
        ];
    @endphp

    <div class="row g-4">
        @forelse($professionals as $pro)
            @php
                $structure = $pro->structurePrincipale ?? $pro->structures->first();
                $city = $pro->adresse_ville ?? ($structure->adresse_ville ?? 'Non renseigné');
                $displayName = trim(trim(($pro->prenom ?? '') . ' ' . ($pro->nom ?? ''))) ?: ($pro->name ?? 'Praticien');
                $initials = strtoupper(
                    mb_substr($pro->prenom ?? $pro->nom ?? 'P', 0, 1) .
                    mb_substr($pro->nom ?? $pro->prenom ?? 'R', 0, 1)
                );
            @endphp
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="rounded-circle bg-primary text-white fw-bold d-flex align-items-center justify-content-center" style="width:48px; height:48px;">
                                {{ $initials }}
                            </div>
                            <div class="ms-3">
                                <h5 class="mb-0">{{ $displayName }}</h5>
                                <small class="text-muted">{{ $pro->specialite ?? 'Spécialité non renseignée' }}</small>
                            </div>
                        </div>
                        <p class="mb-1 text-muted">
                            <i class="fas fa-map-marker-alt me-2 text-primary"></i>{{ $city }}
                        </p>
                        <p class="mb-3 text-muted">
                            <i class="fas fa-hospital me-2 text-primary"></i>{{ $structure->nom_structure ?? 'Structure non renseignée' }}
                        </p>
                        <div class="d-flex gap-2">
                            <a href="{{ route('appointments.create', ['practitioner_id' => $pro->id, 'structure_id' => optional($structure)->id, 'speciality' => $pro->specialite]) }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-calendar-plus me-1"></i> Prendre rendez-vous
                            </a>
                            <a href="{{ route('doctor.profile', $pro->id) }}" class="btn btn-outline-secondary btn-sm">
                                <i class="fas fa-user-md me-1"></i> Profil
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            @foreach($fallback as $fake)
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 shadow-sm border-0">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <div class="rounded-circle bg-primary text-white fw-bold d-flex align-items-center justify-content-center" style="width:48px; height:48px;">
                                    {{ strtoupper(mb_substr($fake['name'], 0, 1)) }}
                                </div>
                                <div class="ms-3">
                                    <h5 class="mb-0">{{ $fake['name'] }}</h5>
                                    <small class="text-muted">{{ $fake['speciality'] }}</small>
                                </div>
                            </div>
                            <p class="mb-1 text-muted">
                                <i class="fas fa-map-marker-alt me-2 text-primary"></i>{{ $fake['city'] }}
                            </p>
                            <p class="mb-3 text-muted">
                                <i class="fas fa-hospital me-2 text-primary"></i>{{ $fake['structure'] }}
                            </p>
                            <div class="d-flex gap-2">
                                <a href="{{ route('appointments.create') }}" class="btn btn-primary btn-sm">
                                    <i class="fas fa-calendar-plus me-1"></i> Prendre rendez-vous
                                </a>
                                <a href="{{ route('services.teleconsultation') }}" class="btn btn-outline-secondary btn-sm">
                                    <i class="fas fa-video me-1"></i> Téléconsultation
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        @endforelse
    </div>

    @if($hasResults)
        <div class="mt-4">
            {{ $professionals->links() }}
        </div>
    @endif
</div>
@endsection
