@extends('layouts.app')

@section('title', 'Services')

@section('content')
@php
    $services = [
        [
            'title' => 'Téléconsultation',
            'description' => 'Consultez un médecin en vidéo sécurisée. Partagez documents et prescriptions.',
            'icon' => 'fa-video',
            'route' => route('services.teleconsultation'),
            'cta' => 'Accéder',
            'color' => 'primary',
        ],
        [
            'title' => 'Prise de rendez-vous',
            'description' => 'Choisissez un praticien ou une structure, proposez un créneau et recevez confirmation par email/SMS.',
            'icon' => 'fa-calendar-check',
            'route' => route('appointments.create'),
            'cta' => 'Prendre rendez-vous',
            'color' => 'success',
        ],
        [
            'title' => 'Pharmacie',
            'description' => 'Commandez vos médicaments et faites-vous livrer en toute sécurité.',
            'icon' => 'fa-pills',
            'route' => route('services.pharmacy.request'),
            'cta' => 'Commander',
            'color' => 'warning',
        ],
        [
            'title' => 'Assurance santé',
            'description' => 'Demandez une prise en charge, envoyez vos justificatifs et suivez vos remboursements.',
            'icon' => 'fa-shield-alt',
            'route' => route('services.insurance.request'),
            'cta' => 'Demander une prise en charge',
            'color' => 'danger',
        ],
        [
            'title' => 'Urgences médicales',
            'description' => 'Formulaire d’urgence pour alerter et déclencher l’assistance.',
            'icon' => 'fa-ambulance',
            'route' => route('services.emergency.request'),
            'cta' => 'Signaler une urgence',
            'color' => 'dark',
        ],
        [
            'title' => 'Trouver un professionnel',
            'description' => 'Filtrez par spécialité, ville ou nom pour choisir un praticien vérifié.',
            'icon' => 'fa-user-md',
            'route' => route('search.professionals'),
            'cta' => 'Rechercher',
            'color' => 'info',
        ],
    ];
@endphp

<div class="container py-5">
    <div class="row align-items-center mb-5">
        <div class="col-lg-7">
            <p class="text-uppercase text-primary fw-semibold small mb-2">Services</p>
            <h1 class="display-5 fw-bold mb-3">Tout votre parcours de soins, au même endroit</h1>
            <p class="lead text-muted mb-0">
                Téléconsultation, rendez-vous, pharmacie, assurance et urgences : accédez aux formulaires et pages dédiées en un clic.
            </p>
        </div>
        <div class="col-lg-5 text-lg-end mt-4 mt-lg-0">
            <a href="{{ route('appointments.create') }}" class="btn btn-gradient rounded-pill px-4 me-2 mb-2">
                <i class="fas fa-calendar-plus me-2"></i> Prendre rendez-vous
            </a>
            <a href="{{ route('services.teleconsultation') }}" class="btn btn-outline-primary rounded-pill px-4 mb-2">
                <i class="fas fa-video me-2"></i> Téléconsultation
            </a>
        </div>
    </div>

    <div class="row g-4">
        @foreach($services as $service)
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 shadow-sm border-0 hover-lift">
                    <div class="card-body">
                        <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-{{ $service['color'] }} text-white mb-3" style="width:52px; height:52px;">
                            <i class="fas {{ $service['icon'] }}"></i>
                        </div>
                        <h5 class="fw-bold mb-2">{{ $service['title'] }}</h5>
                        <p class="text-muted mb-3">{{ $service['description'] }}</p>
                        <a href="{{ $service['route'] }}" class="btn btn-{{ $service['color'] }}">
                            {{ $service['cta'] }}
                        </a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
