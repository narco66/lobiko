@extends('layouts.app')

@section('title', 'Trouver un professionnel | LOBIKO')

@section('content')
<div class="container py-5">
    <div class="row align-items-center mb-5">
        <div class="col-lg-7">
            <p class="text-uppercase text-primary fw-semibold small mb-2">Recherche</p>
            <h1 class="display-5 fw-bold mb-3">Trouver un professionnel de santé</h1>
            <p class="lead text-muted">
                Filtrez par spécialité, localisation, disponibilité, langue, téléconsultation, assurance acceptée et tarifs.
                Comparez les profils, avis vérifiés et créneaux immédiats.
            </p>
            <div class="d-flex flex-wrap gap-3 mt-3">
                <a href="{{ route('appointments.create') }}" class="btn btn-gradient btn-lg rounded-pill px-4">
                    <i class="fas fa-calendar-check me-2"></i> Prendre rendez-vous
                </a>
                <a href="{{ route('services.teleconsultation') }}" class="btn btn-outline-primary btn-lg rounded-pill px-4">
                    Voir la téléconsultation
                </a>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <h5 class="fw-bold mb-3">Filtres disponibles</h5>
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2"></i>
                            Spécialité, sous-spécialité, expérience
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2"></i>
                            Ville/quartier, distance, téléconsultation possible
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2"></i>
                            Assurance acceptée, prix, modes de paiement
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2"></i>
                            Langues parlées, genre si souhaité
                        </li>
                        <li>
                            <i class="fas fa-check text-success me-2"></i>
                            Notes et avis vérifiés, disponibilité immédiate
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-primary text-white me-3" style="width:48px;height:48px;">
                            <i class="fas fa-search"></i>
                        </div>
                        <h5 class="mb-0">Recherche guidée</h5>
                    </div>
                    <p class="text-muted">
                        Suggestions intelligentes selon le motif, l'historique et la localisation du patient.
                    </p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-success text-white me-3" style="width:48px;height:48px;">
                            <i class="fas fa-user-md"></i>
                        </div>
                        <h5 class="mb-0">Profils vérifiés</h5>
                    </div>
                    <p class="text-muted">
                        Diplômes et certifications validés, notation, disponibilités, modalités de consultation.
                    </p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-warning text-white me-3" style="width:48px;height:48px;">
                            <i class="fas fa-route"></i>
                        </div>
                        <h5 class="mb-0">Géolocalisation</h5>
                    </div>
                    <p class="text-muted">
                        Distance, itinéraire, temps de trajet, alternatives en téléconsultation si trop éloigné.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
