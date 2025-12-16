@extends('layouts.app')

@section('title', 'Téléconsultation | LOBIKO')

@section('content')
@php
    $specialities = [
        'Médecine générale', 'Pédiatrie', 'Gynécologie', 'Dermatologie',
        'Cardiologie', 'ORL', 'Psychologie', 'Autre'
    ];
    $checklist = [
        ['icon' => 'fa-video', 'title' => 'Visio HD sécurisée', 'text' => 'Salle virtuelle chiffrée, accessible sur mobile ou desktop.'],
        ['icon' => 'fa-file-shield', 'title' => 'Ordonnance numérique', 'text' => 'Prescription électronique partageable avec la pharmacie.'],
        ['icon' => 'fa-clock', 'title' => 'Rendez-vous rapide', 'text' => 'Créneaux disponibles sous 15 minutes en moyenne.'],
    ];
    $requirements = [
        ['icon' => 'fa-wifi', 'title' => 'Connexion stable', 'text' => 'Débit 4G ou Wi-Fi conseillé pour la vidéo.'],
        ['icon' => 'fa-mobile-screen', 'title' => 'Appareil prêt', 'text' => 'Caméra + micro activés. Utilisez un casque si possible.'],
        ['icon' => 'fa-passport', 'title' => 'Pièce et antécédents', 'text' => 'Ayez carte d’identité, carnet de santé ou anciens examens.'],
    ];
@endphp

<section class="tc-hero position-relative overflow-hidden">
    <div class="tc-hero__gradient"></div>
    <div class="container position-relative py-5">
        <div class="row align-items-center g-4">
            <div class="col-lg-7">
                <p class="text-uppercase text-primary fw-semibold small mb-2">Service</p>
                <h1 class="display-5 fw-bold mb-3">Téléconsultation sécurisée en quelques minutes</h1>
                <p class="lead text-muted mb-4">
                    Consultez un médecin vérifié par vidéo, partagez vos documents et recevez une ordonnance numérique.
                    Paiement mobile ou carte, prise en charge assurance si éligible.
                </p>
                <div class="d-flex flex-wrap gap-3">
                    <a href="#tc-fast-form" class="btn btn-gradient btn-lg rounded-pill px-4">
                        <i class="fas fa-video me-2"></i> Démarrer une téléconsultation
                    </a>
                    <a href="{{ route('appointments.create') }}" class="btn btn-outline-light btn-lg rounded-pill px-4">
                        Voir le formulaire complet
                    </a>
                </div>
                <div class="d-flex flex-wrap gap-4 mt-4 text-white">
                    <div>
                        <small class="text-uppercase fw-semibold text-white-50">Temps moyen</small>
                        <h5 class="mb-0">Moins de 10 minutes</h5>
                    </div>
                    <div>
                        <small class="text-uppercase fw-semibold text-white-50">Disponibilité</small>
                        <h5 class="mb-0">24h/24 • 7j/7</h5>
                    </div>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="tc-card shadow-lg" id="tc-fast-form">
                    <h5 class="fw-bold mb-2">Prise en charge immédiate</h5>
                    <p class="text-muted small mb-3">Remplissez les champs essentiels, nous vous connectons rapidement.</p>
                    <form method="POST" action="{{ route('appointments.store') }}" class="row g-3">
                        @csrf
                        <input type="hidden" name="mode" value="teleconsultation">
                        <div class="col-12">
                            <label class="form-label" for="tc_full_name">Nom complet</label>
                            <input type="text" id="tc_full_name" name="full_name" class="form-control @error('full_name') is-invalid @enderror"
                                   value="{{ old('full_name') }}" required>
                            @error('full_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="tc_phone">Téléphone</label>
                            <input type="text" id="tc_phone" name="phone" class="form-control @error('phone') is-invalid @enderror"
                                   value="{{ old('phone') }}" required>
                            @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="tc_email">Email (optionnel)</label>
                            <input type="email" id="tc_email" name="email" class="form-control @error('email') is-invalid @enderror"
                                   value="{{ old('email') }}">
                            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="tc_speciality">Spécialité souhaitée</label>
                            <select id="tc_speciality" name="speciality" class="form-select @error('speciality') is-invalid @enderror" required>
                                <option value="">Sélectionner</option>
                                @foreach($specialities as $spec)
                                    <option value="{{ $spec }}" @selected(old('speciality') === $spec)>{{ $spec }}</option>
                                @endforeach
                            </select>
                            @error('speciality') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="tc_preferred_date">Date souhaitée</label>
                            <input type="date" id="tc_preferred_date" name="preferred_date" class="form-control @error('preferred_date') is-invalid @enderror"
                                   value="{{ old('preferred_date') ?? now()->format('Y-m-d') }}" required>
                            @error('preferred_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label" for="tc_notes">Commentaire (motif, symptômes, assurance)</label>
                            <textarea id="tc_notes" name="notes" class="form-control @error('notes') is-invalid @enderror" rows="3"
                                      placeholder="Ex: fièvre, toux, besoin d’un renouvellement d’ordonnance...">{{ old('notes') }}</textarea>
                            @error('notes') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-12 d-flex justify-content-end gap-2">
                            <a href="{{ route('services') }}" class="btn btn-outline-secondary">Annuler</a>
                            <button type="submit" class="btn btn-gradient">Continuer</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="tc-hero__shape tc-hero__shape--left"></div>
    <div class="tc-hero__shape tc-hero__shape--right"></div>
</section>

<section class="py-5">
    <div class="container">
        <div class="row g-4">
            @foreach($checklist as $item)
                <div class="col-md-4">
                    <div class="tc-tile h-100">
                        <div class="tc-tile__icon">
                            <i class="fas {{ $item['icon'] }}"></i>
                        </div>
                        <h5 class="fw-bold mb-2">{{ $item['title'] }}</h5>
                        <p class="text-muted mb-0">{{ $item['text'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

<section class="py-5 bg-light">
    <div class="container">
        <div class="text-center mb-4">
            <p class="text-uppercase text-primary fw-semibold small mb-2">Comment ça marche</p>
            <h2 class="h3 fw-bold">3 étapes pour consulter</h2>
        </div>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="tc-step h-100">
                    <span class="tc-step__number">1</span>
                    <h6 class="fw-bold mb-1">Demande en ligne</h6>
                    <p class="text-muted mb-0">Choix de la spécialité et du créneau. Confirmation instantanée.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="tc-step h-100">
                    <span class="tc-step__number">2</span>
                    <h6 class="fw-bold mb-1">Accès à la salle</h6>
                    <p class="text-muted mb-0">Lien vidéo envoyé par email/SMS. Tests audio/vidéo guidés.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="tc-step h-100">
                    <span class="tc-step__number">3</span>
                    <h6 class="fw-bold mb-1">Ordonnance et suivi</h6>
                    <p class="text-muted mb-0">Prescription numérique, pharmacie connectée, rendez-vous de contrôle.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="row g-4 align-items-center">
            <div class="col-lg-5">
                <p class="text-uppercase text-primary fw-semibold small mb-2">Avant de commencer</p>
                <h3 class="h4 fw-bold mb-3">Vérifiez ces points pour une expérience fluide</h3>
                <p class="text-muted mb-4">
                    Nous réduisons les échecs de connexion grâce à des tests pré-appel et un support réactif. Si besoin,
                    nous basculons vers l’audio ou replanifions en quelques clics.
                </p>
                <div class="d-flex flex-wrap gap-2">
                    <span class="badge bg-success">Support 24/7</span>
                    <span class="badge bg-warning text-dark">Audio fallback</span>
                    <span class="badge bg-info text-dark">Salle sécurisée</span>
                </div>
            </div>
            <div class="col-lg-7">
                <div class="row g-3">
                    @foreach($requirements as $item)
                        <div class="col-md-4">
                            <div class="tc-req h-100">
                                <div class="tc-req__icon">
                                    <i class="fas {{ $item['icon'] }}"></i>
                                </div>
                                <h6 class="fw-bold mb-1">{{ $item['title'] }}</h6>
                                <p class="text-muted small mb-0">{{ $item['text'] }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-5 bg-primary text-white">
    <div class="container">
        <div class="row align-items-center g-3">
            <div class="col-lg-8">
                <h3 class="h4 fw-bold mb-2">Besoin d’aide ou d’un accompagnement spécifique ?</h3>
                <p class="mb-0">Notre équipe peut ouvrir la salle pour vous, inviter un proche et partager les documents nécessaires.</p>
            </div>
            <div class="col-lg-4 text-lg-end">
                <a href="{{ route('contact') }}" class="btn btn-light text-primary rounded-pill">
                    Contacter le support
                </a>
            </div>
        </div>
    </div>
</section>
@endsection

@push('styles')
<style>
    .tc-hero {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: #fff;
    }
    .tc-hero__gradient {
        position: absolute;
        inset: 0;
        background: radial-gradient(circle at 20% 20%, rgba(255,255,255,0.12), transparent 35%),
                    radial-gradient(circle at 80% 10%, rgba(255,255,255,0.12), transparent 30%);
        opacity: 0.7;
    }
    .tc-hero__shape {
        position: absolute;
        width: 220px;
        height: 220px;
        border-radius: 40%;
        background: rgba(255,255,255,0.06);
    }
    .tc-hero__shape--left { top: 10%; left: -60px; transform: rotate(-15deg); }
    .tc-hero__shape--right { bottom: -40px; right: -40px; transform: rotate(18deg); }
    .tc-card {
        background: #fff;
        border-radius: 16px;
        padding: 22px;
        border: 1px solid rgba(0,0,0,0.05);
    }
    .tc-tile {
        background: #fff;
        border-radius: 14px;
        padding: 18px;
        border: 1px solid rgba(0,0,0,0.05);
        box-shadow: 0 12px 30px rgba(0,0,0,0.05);
    }
    .tc-tile__icon {
        width: 46px;
        height: 46px;
        border-radius: 12px;
        background: rgba(102,126,234,0.12);
        color: #667eea;
        display: grid;
        place-items: center;
        margin-bottom: 10px;
    }
    .tc-step {
        background: #fff;
        border: 1px solid rgba(0,0,0,0.05);
        border-radius: 14px;
        padding: 18px;
        position: relative;
        box-shadow: 0 12px 30px rgba(0,0,0,0.05);
    }
    .tc-step__number {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: linear-gradient(135deg, #667eea, #764ba2);
        color: #fff;
        font-weight: 700;
        margin-bottom: 10px;
    }
    .tc-req {
        background: #fff;
        border-radius: 12px;
        padding: 14px;
        border: 1px solid rgba(0,0,0,0.05);
        box-shadow: 0 10px 24px rgba(0,0,0,0.04);
    }
    .tc-req__icon {
        width: 38px;
        height: 38px;
        border-radius: 10px;
        background: rgba(255,255,255,0.2);
        color: #fff;
        display: grid;
        place-items: center;
        margin-bottom: 8px;
        backdrop-filter: blur(2px);
    }
    @media (max-width: 768px) {
        .tc-hero { text-align: left; }
        .tc-hero__shape { display: none; }
    }
</style>
@endpush
