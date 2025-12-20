@extends('layouts.app')

@section('title', 'Trouver un professionnel')

@push('styles')
<style>
    .page-hero {
        background: radial-gradient(circle at 20% 20%, rgba(102, 126, 234, 0.12), transparent 35%),
                    radial-gradient(circle at 80% 10%, rgba(118, 75, 162, 0.1), transparent 35%),
                    linear-gradient(135deg, #f8f9ff 0%, #f4f6ff 100%);
    }

    .hero-blob {
        position: absolute;
        width: 220px;
        height: 220px;
        border-radius: 50%;
        filter: blur(60px);
        opacity: 0.7;
        z-index: 0;
    }

    .hero-blob-1 {
        background: rgba(102, 126, 234, 0.5);
        top: 20px;
        left: -60px;
    }

    .hero-blob-2 {
        background: rgba(118, 75, 162, 0.4);
        bottom: -40px;
        right: -40px;
    }

    .finder-card {
        background: #fff;
        border-radius: 18px;
        border: 1px solid #e9ecf5;
        padding: 20px;
    }

    .finder-card .form-control,
    .finder-card .form-select {
        border-radius: 12px;
    }

    .finder-card .btn-primary {
        border-radius: 12px;
    }

    .stat-pill {
        border: 1px solid #e2e8ff;
        background: #fff;
        border-radius: 14px;
        padding: 12px 14px;
        min-width: 170px;
        box-shadow: 0 10px 30px rgba(102, 126, 234, 0.08);
    }

    .stat-pill .label {
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 0.02em;
        color: #6b7280;
    }

    .stat-pill .value {
        font-weight: 700;
        font-size: 18px;
        color: #111827;
    }

    .filter-chip {
        border: 1px solid #e2e8f0;
        background: #f8fafc;
        border-radius: 999px;
        padding: 6px 12px;
        font-size: 13px;
        color: #334155;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .filter-chip .chip-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        display: inline-block;
    }

    .pro-card {
        border: 1px solid #e9ecf5;
        border-radius: 18px;
        box-shadow: 0 20px 50px rgba(15, 23, 42, 0.06);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        position: relative;
        overflow: hidden;
    }

    .pro-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 25px 60px rgba(15, 23, 42, 0.08);
    }

    .pro-card::before {
        content: '';
        position: absolute;
        inset: 0;
        background: linear-gradient(135deg, rgba(102, 126, 234, 0.08), rgba(118, 75, 162, 0.08));
        opacity: 0;
        transition: opacity 0.2s ease;
        pointer-events: none;
    }

    .pro-card:hover::before {
        opacity: 1;
    }

    .avatar-compact {
        width: 56px;
        height: 56px;
        border-radius: 14px;
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        color: #fff;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        box-shadow: 0 10px 30px rgba(102, 126, 234, 0.25);
    }

    .badge-soft {
        background: #eef2ff;
        color: #4338ca;
        border-radius: 999px;
        padding: 6px 10px;
        font-size: 12px;
        font-weight: 600;
    }

    .meta-item {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 10px;
        background: #f8fafc;
        border-radius: 12px;
        font-size: 13px;
        color: #475569;
    }

    .empty-state {
        border: 1px dashed #cbd5e1;
        background: #f8fafc;
        border-radius: 16px;
    }

    .pager-modern .pagination {
        gap: 8px;
    }

    .pager-modern .page-link {
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        color: #0f172a;
        background: #fff;
        padding: 8px 12px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        min-height: 42px;
        box-shadow: 0 10px 30px rgba(15, 23, 42, 0.05);
        transition: all 0.2s ease;
        font-weight: 600;
        font-size: 14px;
    }

    .pager-modern .page-link:hover {
        border-color: #cbd5e1;
        transform: translateY(-1px);
    }

    .pager-modern .page-item.active .page-link {
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        color: #fff;
        border-color: transparent;
        box-shadow: 0 12px 28px rgba(102, 126, 234, 0.25);
    }

    .pager-modern .page-item.disabled .page-link {
        color: #94a3b8;
        background: #f8fafc;
        border-color: #e2e8f0;
        box-shadow: none;
    }

    .pager-modern .page-link.dots {
        color: #94a3b8;
        cursor: default;
        box-shadow: none;
    }

    .pager-modern .page-link i {
        font-size: 0.9rem;
    }
</style>
@endpush

@section('content')
@php
    $specialityValue = $filters['speciality'] ?? '';
    $cityValue = $filters['city'] ?? '';
    $searchValue = $filters['search'] ?? '';
    $activeFilters = array_filter([
        'Spécialité' => $specialityValue,
        'Ville' => $cityValue,
        'Recherche' => $searchValue,
    ]);
@endphp
<div class="page-hero position-relative mb-5">
    <span class="hero-blob hero-blob-1"></span>
    <span class="hero-blob hero-blob-2"></span>
    <div class="container py-5 position-relative">
        <div class="row align-items-center g-4">
            <div class="col-lg-7">
                <p class="text-uppercase text-primary fw-semibold small mb-2">Recherche intelligente</p>
                <h1 class="display-6 fw-bold mb-3">Trouver un professionnel de santé en quelques clics</h1>
                <p class="lead text-muted mb-4">Filtrez par spécialité, ville ou nom, visualisez les structures et prenez rendez-vous immédiatement.</p>
                <div class="d-flex flex-wrap gap-3">
                    <div class="stat-pill">
                        <span class="label d-block mb-1">Réseau certifié</span>
                        <span class="value">250+ praticiens</span>
                    </div>
                    <div class="stat-pill">
                        <span class="label d-block mb-1">Couverture</span>
                        <span class="value">15+ spécialités</span>
                    </div>
                    <div class="stat-pill">
                        <span class="label d-block mb-1">Disponibilité</span>
                        <span class="value">24/7 en ligne</span>
                    </div>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="finder-card shadow-sm">
                    <div class="d-flex align-items-center mb-3">
                        <span class="badge-soft">Recherche rapide</span>
                        <span class="ms-auto text-muted small">Affinez vos critères</span>
                    </div>
                    <form action="{{ route('search.professionals') }}" method="GET" class="row g-3">
                        <div class="col-12">
                            <label class="form-label mb-1">Nom du médecin ou établissement</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0"><i class="fas fa-search text-primary"></i></span>
                                <input type="text" name="search" class="form-control border-start-0" placeholder="Ex: Dr. Owono, Clinique du Centre" value="{{ $searchValue }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label mb-1">Spécialité</label>
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
                        <div class="col-md-6">
                            <label class="form-label mb-1">Ville</label>
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
                        <div class="col-12 d-flex flex-wrap gap-2">
                            <button type="submit" class="btn btn-primary flex-grow-1">
                                <i class="fas fa-magnifying-glass me-2"></i> Lancer la recherche
                            </button>
                            <a href="{{ route('search.professionals') }}" class="btn btn-outline-secondary">
                                Réinitialiser
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
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

<div class="container pb-5">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
        <div>
            <p class="text-uppercase text-primary fw-semibold small mb-1">Résultats</p>
            <h2 class="h4 mb-0">{{ method_exists($professionals, 'total') ? $professionals->total() : $professionals->count() }} professionnels trouvés</h2>
        </div>
        @if($activeFilters)
            <div class="d-flex align-items-center flex-wrap gap-2">
                <span class="text-muted small">Filtres actifs :</span>
                @foreach($activeFilters as $label => $value)
                    <span class="filter-chip"><span class="chip-dot"></span>{{ $label }} : {{ $value }}</span>
                @endforeach
                <a href="{{ route('search.professionals') }}" class="btn btn-link btn-sm text-decoration-none">
                    Réinitialiser
                </a>
            </div>
        @endif
    </div>

    <div class="row g-4">
        @forelse($professionals as $pro)
            @php
                $structure = $pro->structurePrincipale ?: $pro->structures->first();
                $city = $pro->adresse_ville ?? (optional($structure)->adresse_ville ?? 'Non renseigné');
                $structureName = optional($structure)->nom_structure ?? 'Structure non renseignée';
                $displayName = trim(trim(($pro->prenom ?? '') . ' ' . ($pro->nom ?? ''))) ?: ($pro->name ?? 'Praticien');
                $initials = strtoupper(
                    mb_substr($pro->prenom ?? $pro->nom ?? 'P', 0, 1) .
                    mb_substr($pro->nom ?? $pro->prenom ?? 'R', 0, 1)
                );
            @endphp
            <div class="col-md-6 col-lg-4">
                <div class="pro-card h-100 bg-white">
                    <div class="card-body">
                        <div class="d-flex align-items-start justify-content-between mb-3">
                            <div class="d-flex align-items-center">
                                <div class="avatar-compact me-3">
                                    {{ $initials }}
                                </div>
                                <div>
                                    <h5 class="mb-1">{{ $displayName }}</h5>
                                    <div class="d-flex align-items-center gap-2 flex-wrap">
                                        <span class="badge-soft">{{ $pro->specialite ?? 'Spécialité non renseignée' }}</span>
                                        <span class="text-muted small">{{ $structureName }}</span>
                                    </div>
                                </div>
                            </div>
                            <span class="text-muted small d-inline-flex align-items-center gap-1">
                                <i class="fas fa-check-circle text-success"></i> Vérifié
                            </span>
                        </div>
                        <div class="d-flex flex-wrap gap-2 mb-3">
                            <span class="meta-item">
                                <i class="fas fa-map-marker-alt text-primary"></i>{{ $city }}
                            </span>
                            <span class="meta-item">
                                <i class="fas fa-hospital text-primary"></i>{{ $structureName }}
                            </span>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="{{ route('appointments.create', ['practitioner_id' => $pro->id, 'structure_id' => optional($structure)->id, 'speciality' => $pro->specialite]) }}" class="btn btn-primary w-100">
                                <i class="fas fa-calendar-plus me-2"></i> Prendre rendez-vous
                            </a>
                            <a href="{{ route('doctor.profile', $pro->id) }}" class="btn btn-outline-secondary w-100">
                                <i class="fas fa-user-md me-2"></i> Profil
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="empty-state p-4 d-flex flex-column flex-md-row align-items-center gap-3">
                    <div class="avatar-compact" style="width:64px; height:64px; border-radius:50%;">
                        <i class="fas fa-magnifying-glass"></i>
                    </div>
                    <div class="flex-fill">
                        <h5 class="mb-2">Aucun professionnel ne correspond à vos filtres</h5>
                        <p class="mb-0 text-muted">Essayez une autre ville ou une spécialité voisine. Vous pouvez aussi opter pour la téléconsultation immédiate.</p>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('services.teleconsultation') }}" class="btn btn-primary">
                            <i class="fas fa-video me-2"></i> Téléconsultation
                        </a>
                        <a href="{{ route('search.professionals') }}" class="btn btn-outline-secondary">
                            Réinitialiser
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-12">
                <p class="text-uppercase text-primary fw-semibold small mb-2">Suggestions populaires</p>
            </div>
            @foreach($fallback as $fake)
                <div class="col-md-6 col-lg-4">
                    <div class="pro-card h-100 bg-white">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <div class="avatar-compact me-3">
                                    {{ strtoupper(mb_substr($fake['name'], 0, 1)) }}
                                </div>
                                <div>
                                    <h5 class="mb-1">{{ $fake['name'] }}</h5>
                                    <span class="badge-soft">{{ $fake['speciality'] }}</span>
                                </div>
                            </div>
                            <div class="d-flex flex-wrap gap-2 mb-3">
                                <span class="meta-item">
                                    <i class="fas fa-map-marker-alt text-primary"></i>{{ $fake['city'] }}
                                </span>
                                <span class="meta-item">
                                    <i class="fas fa-hospital text-primary"></i>{{ $fake['structure'] }}
                                </span>
                            </div>
                            <div class="d-flex gap-2">
                                <a href="{{ route('appointments.create') }}" class="btn btn-primary w-100">
                                    <i class="fas fa-calendar-plus me-2"></i> Prendre rendez-vous
                                </a>
                                <a href="{{ route('services.teleconsultation') }}" class="btn btn-outline-secondary w-100">
                                    <i class="fas fa-video me-2"></i> Téléconsultation
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        @endforelse
    </div>

    @if($hasResults && method_exists($professionals, 'hasPages') && $professionals->hasPages())
        @php
            $paginator = $professionals;
            $start = max(1, $paginator->currentPage() - 2);
            $end = min($paginator->lastPage(), $paginator->currentPage() + 2);
        @endphp
        <nav class="mt-4 pager-modern" aria-label="Pagination des professionnels">
            <ul class="pagination justify-content-center align-items-center mb-0 flex-wrap">
                <li class="page-item {{ $paginator->onFirstPage() ? 'disabled' : '' }}">
                    <a class="page-link" href="{{ $paginator->onFirstPage() ? '#' : $paginator->previousPageUrl() }}" aria-label="Précédent" tabindex="{{ $paginator->onFirstPage() ? '-1' : '0' }}">
                        <i class="fas fa-arrow-left"></i><span>Précédent</span>
                    </a>
                </li>

                @if($start > 1)
                    <li class="page-item">
                        <a class="page-link" href="{{ $paginator->url(1) }}">1</a>
                    </li>
                    @if($start > 2)
                        <li class="page-item disabled">
                            <span class="page-link dots">…</span>
                        </li>
                    @endif
                @endif

                @for($page = $start; $page <= $end; $page++)
                    <li class="page-item {{ $page === $paginator->currentPage() ? 'active' : '' }}">
                        <a class="page-link" href="{{ $paginator->url($page) }}">{{ $page }}</a>
                    </li>
                @endfor

                @if($end < $paginator->lastPage())
                    @if($end < $paginator->lastPage() - 1)
                        <li class="page-item disabled">
                            <span class="page-link dots">…</span>
                        </li>
                    @endif
                    <li class="page-item">
                        <a class="page-link" href="{{ $paginator->url($paginator->lastPage()) }}">{{ $paginator->lastPage() }}</a>
                    </li>
                @endif

                <li class="page-item {{ $paginator->currentPage() === $paginator->lastPage() ? 'disabled' : '' }}">
                    <a class="page-link" href="{{ $paginator->currentPage() === $paginator->lastPage() ? '#' : $paginator->nextPageUrl() }}" aria-label="Suivant" tabindex="{{ $paginator->currentPage() === $paginator->lastPage() ? '-1' : '0' }}">
                        <span>Suivant</span><i class="fas fa-arrow-right"></i>
                    </a>
                </li>
            </ul>
        </nav>
    @endif
</div>
@endsection
