@extends('layouts.app')

@section('title', 'Mes rendez-vous')

@section('content')
@php
    $first = $upcoming[0] ?? null;
    $badgeMap = [
        'Confirme' => 'success',
        'Confirmé' => 'success',
        'ConfirmÇ¸' => 'success',
        'Confirmé(e)' => 'success',
        'En attente' => 'warning',
        'En_attente' => 'warning',
        'Annulé' => 'secondary',
    ];
@endphp

<div class="container py-5">
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4">
        <div>
            <p class="text-uppercase text-primary fw-semibold small mb-1">Rendez-vous</p>
            <h1 class="h3 fw-bold mb-1">Vos rendez-vous</h1>
            <p class="text-muted mb-0">Suivez vos prochains créneaux et planifiez une nouvelle consultation.</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('appointments.create') }}" class="btn btn-gradient rounded-pill">
                <i class="fas fa-plus me-2"></i> Planifier un rendez-vous
            </a>
            <a href="{{ route('services.teleconsultation') }}" class="btn btn-outline-primary rounded-pill">
                <i class="fas fa-video me-2"></i> Téléconsultation
            </a>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <h6 class="fw-bold mb-3">Prochain créneau</h6>
                    @if($first)
                        <div class="d-flex align-items-start">
                            <div class="me-3 text-primary">
                                <i class="fas fa-calendar-day fa-2x"></i>
                            </div>
                            <div>
                                <div class="fw-semibold">{{ $first['date'] }}</div>
                                <div class="text-muted small mb-1">{{ $first['doctor'] ?? 'Praticien' }} • {{ $first['mode'] ?? 'Mode' }}</div>
                                <span class="badge bg-{{ $badgeMap[$first['status'] ?? ''] ?? 'primary' }}">{{ $first['status'] ?? 'À venir' }}</span>
                            </div>
                        </div>
                        <hr>
                        <p class="text-muted small mb-0">Présentez-vous 5 min avant l’heure si présentiel. Pour la visio, testez votre audio/vidéo.</p>
                    @else
                        <p class="text-muted mb-0">Aucun rendez-vous planifié. Planifiez-en un en quelques clics.</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="fw-bold mb-0">À venir</h6>
                        <small class="text-muted">Les 10 prochains rendez-vous</small>
                    </div>
                    @if(empty($upcoming))
                        <p class="text-muted mb-0">Aucun rendez-vous prévu.</p>
                    @else
                        <div class="list-group list-group-flush">
                            @foreach($upcoming as $rdv)
                                <div class="list-group-item d-flex flex-column flex-md-row align-items-md-center justify-content-between">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="text-primary">
                                            <i class="fas fa-clock"></i>
                                        </div>
                                        <div>
                                            <div class="fw-semibold">{{ $rdv['date'] }}</div>
                                            <div class="text-muted small">
                                                {{ $rdv['doctor'] ?? 'Praticien' }} • {{ ucfirst($rdv['mode'] ?? 'mode') }}
                                            </div>
                                        </div>
                                    </div>
                                    <span class="badge bg-{{ $badgeMap[$rdv['status'] ?? ''] ?? 'primary' }}">
                                        {{ $rdv['status'] ?? 'À venir' }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
