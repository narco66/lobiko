@extends('layouts.app')

@section('title', 'Assurance santé | LOBIKO')

@section('content')
<div class="container py-5">
    <div class="row align-items-center mb-5">
        <div class="col-lg-7">
            <p class="text-uppercase text-primary fw-semibold small mb-2">Service</p>
            <h1 class="display-5 fw-bold mb-3">Assurance santé intégrée</h1>
            <p class="lead text-muted">
                Vérifiez vos droits, simulez la prise en charge en temps réel, déclenchez une préautorisation ou un dossier
                de remboursement et suivez chaque étape.
            </p>
            <div class="d-flex flex-wrap gap-3 mt-3">
                <a href="{{ route('services.insurance.request') }}" class="btn btn-gradient btn-lg rounded-pill px-4">
                    <i class="fas fa-shield-heart me-2"></i> Activer ma prise en charge
                </a>
                <a href="{{ route('services.appointment') }}" class="btn btn-outline-primary btn-lg rounded-pill px-4">
                    Prendre un rendez-vous
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
                            Vérification contrat/bénéficiaire, plafonds, exclusions, carence
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2"></i>
                            Préautorisation, prise en charge tiers payant, suivi des statuts
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2"></i>
                            Simulation du reste à charge et devis détaillé
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2"></i>
                            Remboursements et litiges, pièces justificatives sécurisées
                        </li>
                        <li>
                            <i class="fas fa-check text-success me-2"></i>
                            Exports et historique pour l'assureur et l'assuré
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
                            <i class="fas fa-chart-pie"></i>
                        </div>
                        <h5 class="mb-0">Simulation</h5>
                    </div>
                    <p class="text-muted">
                        Tarifs issus des grilles, pourcentage de prise en charge, reste à payer immédiat, options d'acompte.
                    </p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-success text-white me-3" style="width:48px;height:48px;">
                            <i class="fas fa-file-contract"></i>
                        </div>
                        <h5 class="mb-0">Gestion des dossiers</h5>
                    </div>
                    <p class="text-muted">
                        Pièces jointes, échanges avec l'assureur, horodatage, statut clair (en attente, approuvé, rejeté).
                    </p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-warning text-white me-3" style="width:48px;height:48px;">
                            <i class="fas fa-hand-holding-dollar"></i>
                        </div>
                        <h5 class="mb-0">Paiements et tiers payant</h5>
                    </div>
                    <p class="text-muted">
                        Flux patient/assurance/subvention, reversements aux praticiens et structures, justificatifs téléchargeables.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
