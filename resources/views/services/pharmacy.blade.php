@extends('layouts.app')

@section('title', 'Pharmacie en ligne | LOBIKO')

@section('content')
<div class="container py-5">
    <x-lobiko.page-header
        title="Pharmacie en ligne"
        subtitle="Ordonnance verifiee, stocks en temps reel, livraison ou retrait"
        :breadcrumbs="[
            ['label' => 'Accueil', 'href' => route('home')],
            ['label' => 'Pharmacie']
        ]"
    />

    <div class="row align-items-center g-4 mb-4">
        <div class="col-lg-7">
            <p class="lead text-muted mb-3">
                Deposez votre ordonnance, choisissez une pharmacie disponible, suivez la preparation et payez en ligne (mobile money ou carte). Livraison ou retrait express.
            </p>
            <div class="d-flex flex-wrap gap-3">
                <a href="{{ route('services.pharmacy.request') }}" class="btn btn-gradient btn-lg rounded-pill px-4">
                    <i class="fas fa-pills me-2" aria-hidden="true"></i> Commander mes medicaments
                </a>
                <a href="{{ route('services.insurance') }}" class="btn btn-outline-primary btn-lg rounded-pill px-4">
                    Voir la prise en charge
                </a>
            </div>
            <div class="d-flex gap-4 mt-3 text-muted small">
                <div><i class="fas fa-shield-halved me-1" aria-hidden="true"></i>Controle pharmacien</div>
                <div><i class="fas fa-box-open me-1" aria-hidden="true"></i>Stock temps reel</div>
                <div><i class="fas fa-truck-fast me-1" aria-hidden="true"></i>Livraison ou retrait</div>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <h5 class="fw-bold mb-3">Fonctionnalites cles</h5>
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2"><i class="fas fa-check text-success me-2" aria-hidden="true"></i>Ordonnance verifiee et interactions controlees</li>
                        <li class="mb-2"><i class="fas fa-check text-success me-2" aria-hidden="true"></i>Stocks par pharmacie, substitutions autorisees</li>
                        <li class="mb-2"><i class="fas fa-check text-success me-2" aria-hidden="true"></i>Paiement mobile money / carte, suivi temps reel</li>
                        <li class="mb-2"><i class="fas fa-check text-success me-2" aria-hidden="true"></i>Livraison domicile ou relais, preuve de remise</li>
                        <li><i class="fas fa-check text-success me-2" aria-hidden="true"></i>Facturation patient/assurance, tiers payant si eligible</li>
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
                            <i class="fas fa-clipboard-check" aria-hidden="true"></i>
                        </div>
                        <h5 class="mb-0">Controle pharmaceutique</h5>
                    </div>
                    <p class="text-muted mb-0">Validation, substitutions conformes, tracabilite des lots et dates de peremption.</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-success text-white me-3" style="width:48px;height:48px;">
                            <i class="fas fa-truck" aria-hidden="true"></i>
                        </div>
                        <h5 class="mb-0">Logistique</h5>
                    </div>
                    <p class="text-muted mb-0">Retrait comptoir ou livraison, suivi temps reel, confirmation par OTP ou signature.</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-warning text-white me-3" style="width:48px;height:48px;">
                            <i class="fas fa-shield-alt" aria-hidden="true"></i>
                        </div>
                        <h5 class="mb-0">Securite & conformite</h5>
                    </div>
                    <p class="text-muted mb-0">Historique de delivrance, facturation transparente, conformite locale et assurance.</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mt-4">
        <div class="col-lg-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <h5 class="fw-bold mb-3">Etapes</h5>
                    <ol class="list-group list-group-flush">
                        <li class="list-group-item px-0">Deposez l'ordonnance ou selectionnez les produits.</li>
                        <li class="list-group-item px-0">Choisissez une pharmacie et validez les substitutions.</li>
                        <li class="list-group-item px-0">Payez (mobile money/carte) et suivez la preparation.</li>
                        <li class="list-group-item px-0">Recevez la commande (livraison/retrait) avec preuve.</li>
                    </ol>
                    <a href="{{ route('services.pharmacy.request') }}" class="btn btn-primary btn-sm mt-3">Demarrer</a>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <h5 class="fw-bold mb-3">FAQ rapide</h5>
                    <div class="mb-3">
                        <div class="fw-semibold">Ordonnance obligatoire ?</div>
                        <div class="text-muted small">Oui pour les medicaments soumis a prescription. Les OTC sont disponibles sans.</div>
                    </div>
                    <div class="mb-3">
                        <div class="fw-semibold">Delais moyens ?</div>
                        <div class="text-muted small">Preparation sous 30 min selon stock. Livraison selon zone.</div>
                    </div>
                    <div class="mb-3">
                        <div class="fw-semibold">Paiement et remboursement</div>
                        <div class="text-muted small">Recu et facture disponibles. Tiers payant si assurance valide.</div>
                    </div>
                    <a href="{{ route('help') }}" class="btn btn-outline-primary btn-sm">Plus de questions</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
