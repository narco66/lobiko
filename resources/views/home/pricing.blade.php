@extends('layouts.app')

@section('title', 'Tarifs')

@section('content')
<div class="container py-5">
    <div class="row align-items-center mb-5">
        <div class="col-lg-7">
            <p class="text-uppercase text-primary fw-semibold small mb-2">Tarifs</p>
            <h1 class="display-5 fw-bold mb-3">Choisissez le plan qui vous convient</h1>
            <p class="lead text-muted mb-0">
                Téléconsultation, rendez-vous, pharmacie et assurance. Des formules adaptées aux patients, aux professionnels et aux structures.
            </p>
        </div>
        <div class="col-lg-5 text-lg-end mt-4 mt-lg-0">
            <a href="{{ route('register') }}" class="btn btn-gradient rounded-pill px-4 me-2 mb-2">
                <i class="fas fa-user-plus me-2"></i> Créer un compte
            </a>
            <a href="{{ route('contact') }}" class="btn btn-outline-primary rounded-pill px-4 mb-2">
                <i class="fas fa-phone me-2"></i> Parler à un conseiller
            </a>
        </div>
    </div>

    <div class="row g-4">
        @foreach($plans as $plan)
            <div class="col-md-4">
                <div class="card h-100 shadow-sm border-0 position-relative {{ $plan['is_popular'] ? 'popular-plan' : '' }}">
                    @if($plan['is_popular'])
                        <span class="badge bg-warning text-dark position-absolute top-0 end-0 m-3">Populaire</span>
                    @endif
                    <div class="card-body d-flex flex-column">
                        <h5 class="fw-bold mb-1">{{ $plan['name'] }}</h5>
                        <p class="text-muted mb-3">{{ $plan['cta'] ?? 'Accédez aux services essentiels' }}</p>
                        <div class="mb-3">
                            <span class="display-5 fw-bold">{{ $plan['price'] }}</span>
                            <span class="text-muted">{{ $plan['currency'] }}</span>
                            <div class="text-muted">/ {{ $plan['period'] }}</div>
                        </div>
                        <ul class="list-unstyled mb-4">
                            @foreach($plan['features'] as $feature)
                                <li class="mb-2">
                                    <i class="fas fa-check text-success me-2"></i>{{ $feature }}
                                </li>
                            @endforeach
                        </ul>
                        <div class="mt-auto">
                            <a href="{{ route('register') }}" class="btn btn-primary w-100">
                                {{ $plan['cta'] ?? 'Choisir ce plan' }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="card shadow-sm border-0 mt-5">
        <div class="card-body">
            <div class="row g-4 align-items-center">
                <div class="col-md-8">
                    <h5 class="fw-bold mb-2">Besoin d'un devis personnalisé ?</h5>
                    <p class="text-muted mb-0">
                        Pour les structures de santé, pharmacies, assureurs ou projets pilotes, nous pouvons adapter les modules (téléconsultation, rendez-vous, facturation, SMS) à vos besoins.
                    </p>
                </div>
                <div class="col-md-4 text-md-end">
                    <a href="{{ route('contact') }}" class="btn btn-outline-primary me-2">Contact commercial</a>
                    <a href="{{ route('services') }}" class="btn btn-gradient">Découvrir les services</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
