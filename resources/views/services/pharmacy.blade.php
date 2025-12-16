@extends('layouts.app')

@section('title', 'Pharmacie en ligne | LOBIKO')

@section('content')
<div class="container py-5">
    <div class="row align-items-center mb-5">
        <div class="col-lg-7">
            <p class="text-uppercase text-primary fw-semibold small mb-2">Service</p>
            <h1 class="display-5 fw-bold mb-3">Pharmacie en ligne</h1>
            <p class="lead text-muted">
                Faites vérifier votre ordonnance, choisissez une pharmacie disponible, suivez la préparation et optez pour la
                livraison ou le retrait express.
            </p>
            <div class="d-flex flex-wrap gap-3 mt-3">
                <a href="{{ route('services.pharmacy.request') }}" class="btn btn-gradient btn-lg rounded-pill px-4">
                    <i class="fas fa-pills me-2"></i> Accéder à la pharmacie
                </a>
                <a href="{{ route('services.insurance') }}" class="btn btn-outline-primary btn-lg rounded-pill px-4">
                    Voir la prise en charge
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
                            Ordonnance électronique validée et contrôles d'interactions
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2"></i>
                            Stock en temps réel par pharmacie, substitutions autorisées
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2"></i>
                            Paiement mobile money/carte, suivi de commande
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2"></i>
                            Livraison à domicile ou point relais, preuve de remise
                        </li>
                        <li>
                            <i class="fas fa-check text-success me-2"></i>
                            Facturation patient/assurance, tiers payant si éligible
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
                            <i class="fas fa-clipboard-check"></i>
                        </div>
                        <h5 class="mb-0">Contrôle pharmaceutique</h5>
                    </div>
                    <p class="text-muted">
                        Validation par un pharmacien, substitutions conformes, traçabilité des lots et dates de péremption.
                    </p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-success text-white me-3" style="width:48px;height:48px;">
                            <i class="fas fa-truck"></i>
                        </div>
                        <h5 class="mb-0">Logistique</h5>
                    </div>
                    <p class="text-muted">
                        Retrait comptoir ou livraison, suivi en temps réel, confirmation par OTP ou signature.
                    </p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-warning text-white me-3" style="width:48px;height:48px;">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <h5 class="mb-0">Sécurité & conformité</h5>
                    </div>
                    <p class="text-muted">
                        Historique de délivrance, facturation transparente, conformité aux règles locales et assurance.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
