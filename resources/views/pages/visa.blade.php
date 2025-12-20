@extends('layouts.app')
@section('title', 'Paiement par Visa')

@section('content')
<div class="container py-4">
    <x-lobiko.page-header
        title="Paiement par Visa"
        subtitle="Sécurisé, rapide et compatible avec vos paiements en ligne"
        :breadcrumbs="[
            ['label' => 'Accueil', 'href' => route('home')],
            ['label' => 'Visa']
        ]"
    />

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 mb-3">
                <div class="card-body">
                    <h5 class="fw-bold mb-2">Ce que vous pouvez payer</h5>
                    <ul class="text-muted mb-0">
                        <li>Consultations et téléconsultations.</li>
                        <li>Rendez-vous, actes médicaux et forfaits/packs.</li>
                        <li>Commandes pharmacie et livraisons.</li>
                        <li>Assurance (reste à charge, complémentaires) et factures.</li>
                    </ul>
                </div>
            </div>

            <div class="card shadow-sm border-0 mb-3">
                <div class="card-body">
                    <h5 class="fw-bold mb-2">Sécurité et conformité</h5>
                    <ul class="text-muted mb-0">
                        <li>Paiements chiffrés (TLS) et redirection vers la page sécurisée du prestataire.</li>
                        <li>3-D Secure / OTP selon votre banque.</li>
                        <li>Aucune conservation de vos données de carte sur LOBIKO.</li>
                    </ul>
                </div>
            </div>

            <div class="card shadow-sm border-0 mb-3">
                <div class="card-body">
                    <h5 class="fw-bold mb-2">Comment payer</h5>
                    <ol class="list-group list-group-flush">
                        <li class="list-group-item px-0">Choisissez Visa au moment du paiement.</li>
                        <li class="list-group-item px-0">Saisissez les informations carte (numéro, date, CVV) sur la page sécurisée.</li>
                        <li class="list-group-item px-0">Validez l’authentification (3DS/OTP si demandé).</li>
                        <li class="list-group-item px-0">Recevez la confirmation et votre reçu/facture dans votre espace.</li>
                    </ol>
                </div>
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h5 class="fw-bold mb-2">Support paiement</h5>
                    <p class="text-muted mb-2">En cas d’échec, double prélèvement suspect ou remboursement, contactez le support en précisant le montant, la date et les 4 derniers chiffres de la carte.</p>
                    <a class="btn btn-primary btn-sm" href="{{ route('contact') }}"><i class="fas fa-envelope me-2" aria-hidden="true"></i>Contacter le support</a>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm border-0 mb-3">
                <div class="card-header bg-light">
                    <h6 class="mb-0">Rappel</h6>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled small text-muted mb-0">
                        <li class="mb-2"><strong>Devise :</strong> selon votre banque, frais éventuels.</li>
                        <li class="mb-2"><strong>Reçu :</strong> disponible dans votre espace et par email.</li>
                        <li><strong>Assistance :</strong> support@lobiko.example</li>
                    </ul>
                </div>
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">Autres ressources</h6>
                    <span class="badge bg-primary-subtle text-primary">Paiement</span>
                </div>
                <div class="list-group list-group-flush">
                    <a class="list-group-item list-group-item-action d-flex justify-content-between align-items-center" href="{{ route('privacy') }}">
                        Confidentialité <i class="fas fa-arrow-right text-muted" aria-hidden="true"></i>
                    </a>
                    <a class="list-group-item list-group-item-action d-flex justify-content-between align-items-center" href="{{ route('terms') }}">
                        Conditions d’utilisation <i class="fas fa-arrow-right text-muted" aria-hidden="true"></i>
                    </a>
                    <a class="list-group-item list-group-item-action d-flex justify-content-between align-items-center" href="{{ route('help') }}">
                        Aide et support <i class="fas fa-arrow-right text-muted" aria-hidden="true"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
