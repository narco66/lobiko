@extends('layouts.app')

@section('title', 'Urgences | LOBIKO')

@section('content')
<div class="container py-5">
    <div class="row align-items-center mb-5">
        <div class="col-lg-7">
            <p class="text-uppercase text-primary fw-semibold small mb-2">Service</p>
            <h1 class="display-5 fw-bold mb-3">Parcours urgences</h1>
            <p class="lead text-muted">
                Déclarer une urgence, partager la géolocalisation, être orienté vers la structure ou l'ambulance la plus proche,
                et suivre la prise en charge en temps réel.
            </p>
            <div class="d-flex flex-wrap gap-3 mt-3">
                <a href="{{ route('services.emergency.request') }}" class="btn btn-gradient btn-lg rounded-pill px-4">
                    <i class="fas fa-ambulance me-2"></i> Activer le mode urgence
                </a>
                <a href="{{ route('services.pharmacy') }}" class="btn btn-outline-primary btn-lg rounded-pill px-4">
                    Besoin de médicaments ?
                </a>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <h5 class="fw-bold mb-3">Fonctionnalités clés</h5>
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2"></i>
                            Bouton SOS avec localisation et infos vitales minimales
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2"></i>
                            Matching structure/ambulance selon distance et capacité
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2"></i>
                            Suivi du trajet, notifications aux proches autorisés
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2"></i>
                            Dossier d'urgence partagé au praticien dès l'arrivée
                        </li>
                        <li>
                            <i class="fas fa-check text-success me-2"></i>
                            Facturation/assurance alignées sur l'acte urgent
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
                        <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-danger text-white me-3" style="width:48px;height:48px;">
                            <i class="fas fa-bolt"></i>
                        </div>
                        <h5 class="mb-0">Priorisation</h5>
                    </div>
                    <p class="text-muted">
                        Questionnaire bref pour orienter vers la bonne filière (trauma, obstétrique, cardio...) et prioriser.
                    </p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-success text-white me-3" style="width:48px;height:48px;">
                            <i class="fas fa-map-marked-alt"></i>
                        </div>
                        <h5 class="mb-0">Géolocalisation</h5>
                    </div>
                    <p class="text-muted">
                        Estimation du temps d'arrivée, itinéraire partagé, mise à jour en direct pour la famille si autorisée.
                    </p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-warning text-white me-3" style="width:48px;height:48px;">
                            <i class="fas fa-file-medical-alt"></i>
                        </div>
                        <h5 class="mb-0">Dossier d'urgence</h5>
                    </div>
                    <p class="text-muted">
                        Antécédents clés, allergies, traitements en cours, contacts utiles : prêts pour l'équipe d'accueil.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
