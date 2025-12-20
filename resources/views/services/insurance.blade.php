@extends('layouts.app')

@section('title', 'Assurance sante | LOBIKO')

@section('content')
<div class="container py-5">
    <div class="row align-items-center mb-5">
        <div class="col-lg-7">
            <p class="text-uppercase text-primary fw-semibold small mb-2">Service</p>
            <h1 class="display-5 fw-bold mb-3">Assurance sante integree</h1>
            <p class="lead text-muted">
                Verifiez vos droits, simulez la prise en charge en temps reel, declenchez une preautorisation ou un dossier
                de remboursement et suivez chaque etape.
            </p>
            <div class="d-flex flex-wrap gap-3 mt-3">
                <a href="{{ route('services.insurance.request') }}" class="btn btn-gradient btn-lg rounded-pill px-4">
                    <i class="fas fa-shield-heart me-2" aria-hidden="true"></i> Activer ma prise en charge
                </a>
                <a href="{{ route('services.appointment') }}" class="btn btn-outline-primary btn-lg rounded-pill px-4">
                    Prendre un rendez-vous
                </a>
            </div>
            <div class="d-flex gap-4 mt-3 text-muted small">
                <div><i class="fas fa-lock me-1" aria-hidden="true"></i>Donnees securisees</div>
                <div><i class="fas fa-clock me-1" aria-hidden="true"></i>Reponse en quelques minutes</div>
                <div><i class="fas fa-paperclip me-1" aria-hidden="true"></i>Dossiers et justificatifs centralises</div>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <h5 class="fw-bold mb-3">Fonctionnalites cles</h5>
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2" aria-hidden="true"></i>
                            Verification contrat/beneficiaire, plafonds, exclusions, carence
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2" aria-hidden="true"></i>
                            Preautorisation, prise en charge tiers payant, suivi des statuts
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2" aria-hidden="true"></i>
                            Simulation du reste a charge et devis detaille
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2" aria-hidden="true"></i>
                            Remboursements et litiges, pieces justificatives securisees
                        </li>
                        <li>
                            <i class="fas fa-check text-success me-2" aria-hidden="true"></i>
                            Exports et historique pour l'assureur et l'assure
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
                            <i class="fas fa-chart-pie" aria-hidden="true"></i>
                        </div>
                        <h5 class="mb-0">Simulation</h5>
                    </div>
                    <p class="text-muted">
                        Tarifs issus des grilles, pourcentage de prise en charge, reste a payer immediat, options d'acompte.
                    </p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-success text-white me-3" style="width:48px;height:48px;">
                            <i class="fas fa-file-contract" aria-hidden="true"></i>
                        </div>
                        <h5 class="mb-0">Gestion des dossiers</h5>
                    </div>
                    <p class="text-muted">
                        Pieces jointes, echanges avec l'assureur, horodatage, statut clair (en attente, approuve, rejete).
                    </p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-warning text-white me-3" style="width:48px;height:48px;">
                            <i class="fas fa-hand-holding-dollar" aria-hidden="true"></i>
                        </div>
                        <h5 class="mb-0">Paiements et tiers payant</h5>
                    </div>
                    <p class="text-muted">
                        Flux patient/assurance/subvention, reversements aux praticiens et structures, justificatifs telechargeables.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mt-4">
        <div class="col-lg-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <h5 class="fw-bold mb-3">Comment ca marche</h5>
                    <ol class="list-group list-group-flush">
                        <li class="list-group-item px-0"><strong>1.</strong> Soumettez votre demande de prise en charge avec les justificatifs.</li>
                        <li class="list-group-item px-0"><strong>2.</strong> Nous verifions droits, plafonds et exclusions avec l'assureur.</li>
                        <li class="list-group-item px-0"><strong>3.</strong> Vous recevez le statut (approuve, incomplet, rejete) et les actions a faire.</li>
                        <li class="list-group-item px-0"><strong>4.</strong> Suivi des remboursements et generation des justificatifs.</li>
                    </ol>
                    <a href="{{ route('services.insurance.request') }}" class="btn btn-primary btn-sm mt-3">
                        Demarrer ma demande
                    </a>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <h5 class="fw-bold mb-3">FAQ rapide</h5>
                    <div class="mb-3">
                        <div class="fw-semibold">Quels documents fournir ?</div>
                        <div class="text-muted small">Ordonnance ou acte, carte/contrat, piece d'identite, devis si necessaire.</div>
                    </div>
                    <div class="mb-3">
                        <div class="fw-semibold">Delais de reponse ?</div>
                        <div class="text-muted small">La plupart des demandes recoivent une reponse en moins de 24h selon l'assureur.</div>
                    </div>
                    <div class="mb-3">
                        <div class="fw-semibold">Tiers payant</div>
                        <div class="text-muted small">Si accepte, aucun avance de frais sur la partie couverte. Le reste a charge est indique.</div>
                    </div>
                    <a href="{{ route('services.insurance.request') }}" class="btn btn-outline-primary btn-sm">
                        Poser une question
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
