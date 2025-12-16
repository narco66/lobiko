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
    <div class="row align-items-start g-4">
        <div class="col-lg-7">
            <div class="mb-4">
                <p class="text-uppercase text-primary fw-semibold small mb-1">Contact</p>
                <h1 class="h3 fw-bold mb-2">Écrivez-nous, nous répondons rapidement</h1>
                <p class="text-muted mb-0">Un conseiller vous répondra par email ou téléphone pour orienter votre demande.</p>
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
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
                            <label class="form-label">Téléphone (optionnel)</label>
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
                            <textarea name="message" rows="4" class="form-control @error('message') is-invalid @enderror" placeholder="Précisez votre besoin (téléconsultation, rendez-vous, support, partenariat)..." required>{{ old('message') }}</textarea>
                            @error('message') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-12 d-flex justify-content-end">
                            <button type="submit" class="btn btn-gradient">
                                <i class="fas fa-paper-plane me-2"></i> Envoyer
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card shadow-sm border-0 mb-3">
                <div class="card-body">
                    <h5 class="fw-bold mb-3">Nos bureaux</h5>
                    @foreach($offices as $office)
                        <div class="d-flex align-items-start mb-3">
                            <div class="me-3 text-primary">
                                <i class="fas fa-map-marker-alt"></i>
                            </div>
                            <div>
                                <h6 class="mb-1">{{ $office['city'] }}, {{ $office['country'] }} @if($office['is_headquarters']) <span class="badge bg-primary">Siège</span>@endif</h6>
                                <p class="text-muted mb-1">{{ $office['address'] }}</p>
                                <p class="mb-0">
                                    <i class="fas fa-phone me-2"></i>{{ $office['phone'] }}<br>
                                    <i class="fas fa-envelope me-2"></i>{{ $office['email'] }}
                                </p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h6 class="fw-bold mb-2">Assistance 24/7</h6>
                    <p class="text-muted mb-2">Téléconsultation, rendez-vous, pharmacie, assurance et urgences.</p>
                    <div class="d-flex flex-column gap-2">
                        <a href="{{ route('services.teleconsultation') }}" class="btn btn-outline-primary"><i class="fas fa-video me-2"></i> Téléconsultation</a>
                        <a href="{{ route('appointments.create') }}" class="btn btn-outline-success"><i class="fas fa-calendar-plus me-2"></i> Prendre rendez-vous</a>
                        <a href="{{ route('services.emergency.request') }}" class="btn btn-outline-danger"><i class="fas fa-ambulance me-2"></i> Urgence</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
