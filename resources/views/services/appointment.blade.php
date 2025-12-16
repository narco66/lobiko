@extends('layouts.app')

@section('title', 'Rendez-vous | LOBIKO')

@section('content')
<div class="container py-5">
    <div class="row align-items-center mb-5">
        <div class="col-lg-7">
            <p class="text-uppercase text-primary fw-semibold small mb-2">Service</p>
            <h1 class="display-5 fw-bold mb-3">Prise de rendez-vous</h1>
            <p class="lead text-muted">
                Réservez un créneau en présentiel ou en visio, avec confirmation immédiate, rappels automatiques et règles
                de disponibilité des praticiens synchronisées.
            </p>
            <div class="d-flex flex-wrap gap-3 mt-3">
                <a href="{{ route('appointments.create') }}" class="btn btn-gradient btn-lg rounded-pill px-4">
                    <i class="fas fa-calendar-plus me-2"></i> Réserver maintenant
                </a>
                <a href="{{ route('services.teleconsultation') }}" class="btn btn-outline-primary btn-lg rounded-pill px-4">
                    Voir la téléconsultation
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
                            Agenda temps réel, créneaux multi-ressources (praticien, salle, équipement)
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2"></i>
                            Rappels SMS/mail, gestion des retards et no-show
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2"></i>
                            Préqualification du motif, durée adaptée, documents à fournir
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2"></i>
                            Paiement sécurisé (avance, acompte ou post-paiement si autorisé)
                        </li>
                        <li>
                            <i class="fas fa-check text-success me-2"></i>
                            Compatibilité assurance et disponibilité en téléconsultation
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
                            <i class="fas fa-layer-group"></i>
                        </div>
                        <h5 class="mb-0">Motifs et durées</h5>
                    </div>
                    <p class="text-muted">
                        Spécialité, motif, durée, lieu (cabinet, clinique, domicile, visio) pour orienter le patient au bon endroit.
                    </p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-success text-white me-3" style="width:48px;height:48px;">
                            <i class="fas fa-bell"></i>
                        </div>
                        <h5 class="mb-0">Rappels</h5>
                    </div>
                    <p class="text-muted">
                        Notifications multi-canaux, lien d'accès visio, possibilité de replanifier ou d'annuler selon la politique.
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
                        <h5 class="mb-0">Accès et logistique</h5>
                    </div>
                    <p class="text-muted">
                        Plan, géolocalisation, estimation du temps de trajet, options de transport ou téléconsultation si trop loin.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
