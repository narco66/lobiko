@extends('layouts.app')

@section('title', 'Contact')

@section('content')
@php
    $offices = $offices ?? [
        [
            'city' => 'Libreville',
            'country' => 'Gabon',
            'address' => 'Immeuble Shell, Boulevard Triomphal',
            'phone' => '+241 01 23 45 67',
            'email' => 'gabon@lobiko.com',
            'is_headquarters' => true
        ],
        [
            'city' => 'Douala',
            'country' => 'Cameroun',
            'address' => 'Rue des Palmiers, Akwa',
            'phone' => '+237 233 42 42 42',
            'email' => 'cameroun@lobiko.com',
            'is_headquarters' => false
        ],
    ];
@endphp

<div class="container py-5">
    <x-lobiko.page-header
        title="Contact"
        subtitle="Support, rendez-vous, pharmacie, assurance ou partenariat : nous vous repondons sous 24h"
        :breadcrumbs="[['label' => 'Accueil', 'href' => route('home')], ['label' => 'Contact']]"
    />

    <div class="row g-4">
        <div class="col-lg-7">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body p-4">
                    <div class="d-flex align-items-start justify-content-between mb-3">
                        <div>
                            <p class="text-uppercase text-primary fw-semibold small mb-1">Formulaire</p>
                            <h2 class="h5 fw-bold mb-0">Ecrivez-nous</h2>
                        </div>
                        <span class="badge bg-primary-subtle text-primary">Reponse < 24h</span>
                    </div>

                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    <form method="POST" action="{{ route('contact.submit') }}" class="row g-3">
                        @csrf
                        <div class="col-md-6">
                            <label class="form-label">Nom complet</label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required>
                            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Telephone (optionnel)</label>
                            <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone') }}">
                            @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Sujet</label>
                            <input type="text" name="subject" class="form-control @error('subject') is-invalid @enderror" value="{{ old('subject') }}" required>
                            @error('subject') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label">Message</label>
                            <textarea name="message" rows="4" class="form-control @error('message') is-invalid @enderror" placeholder="Precisez votre besoin (teleconsultation, rendez-vous, support, partenariat)..." required>{{ old('message') }}</textarea>
                            @error('message') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-12 d-flex justify-content-end">
                            <button type="submit" class="btn btn-gradient">
                                <i class="fas fa-paper-plane me-2" aria-hidden="true"></i> Envoyer
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card shadow-sm border-0 mb-3">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <h6 class="fw-bold mb-0">Canaux rapides</h6>
                        <span class="badge bg-light text-body">Support</span>
                    </div>
                    <div class="d-flex flex-column gap-2">
                        <a class="btn btn-primary" href="mailto:support@lobiko.example"><i class="fas fa-envelope-open-text me-2" aria-hidden="true"></i>support@lobiko.example</a>
                        <a class="btn btn-outline-primary" href="tel:+24177790654"><i class="fas fa-phone me-2" aria-hidden="true"></i>+241 77 79 06 54</a>
                        <a class="btn btn-outline-secondary" href="{{ route('services.emergency.request') }}"><i class="fas fa-ambulance me-2" aria-hidden="true"></i>Urgence</a>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm border-0 mb-3">
                <div class="card-body">
                    <h6 class="fw-bold mb-2">Nos bureaux</h6>
                    @foreach($offices as $office)
                        <div class="d-flex align-items-start mb-3">
                            <div class="me-3 text-primary"><i class="fas fa-map-marker-alt" aria-hidden="true"></i></div>
                            <div>
                                <h6 class="mb-1">
                                    {{ $office['city'] }}, {{ $office['country'] }}
                                    @if($office['is_headquarters']) <span class="badge bg-primary">Siege</span> @endif
                                </h6>
                                <p class="text-muted small mb-1">{{ $office['address'] }}</p>
                                <p class="mb-0 small">
                                    <i class="fas fa-phone me-2" aria-hidden="true"></i>{{ $office['phone'] }}<br>
                                    <i class="fas fa-envelope me-2" aria-hidden="true"></i>{{ $office['email'] }}
                                </p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h6 class="fw-bold mb-2">Liens utiles</h6>
                    <div class="d-flex flex-column gap-2">
                        <a href="{{ route('appointments.index') }}" class="text-decoration-none d-flex justify-content-between align-items-center">
                            <span><i class="fas fa-calendar-check me-2" aria-hidden="true"></i>Mes rendez-vous</span>
                            <i class="fas fa-arrow-right text-muted" aria-hidden="true"></i>
                        </a>
                        <a href="{{ route('commandes-pharma.index') }}" class="text-decoration-none d-flex justify-content-between align-items-center">
                            <span><i class="fas fa-pills me-2" aria-hidden="true"></i>Commandes pharma</span>
                            <i class="fas fa-arrow-right text-muted" aria-hidden="true"></i>
                        </a>
                        <a href="{{ route('admin.assurances.index') }}" class="text-decoration-none d-flex justify-content-between align-items-center">
                            <span><i class="fas fa-shield-alt me-2" aria-hidden="true"></i>Assurance</span>
                            <i class="fas fa-arrow-right text-muted" aria-hidden="true"></i>
                        </a>
                        <a href="{{ route('admin.factures.index') }}" class="text-decoration-none d-flex justify-content-between align-items-center">
                            <span><i class="fas fa-file-invoice-dollar me-2" aria-hidden="true"></i>Factures</span>
                            <i class="fas fa-arrow-right text-muted" aria-hidden="true"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
