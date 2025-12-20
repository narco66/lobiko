@extends('layouts.app')
@section('title', 'Centre d’aide')

@section('content')
<div class="container py-4">
    <x-lobiko.page-header
        title="Centre d’aide"
        subtitle="Support, questions fréquentes et ressources"
        :breadcrumbs="[
            ['label' => 'Accueil', 'href' => route('home')],
            ['label' => 'Aide']
        ]"
    />

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 mb-3">
                <div class="card-body">
                    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-md-between">
                        <div>
                            <p class="text-uppercase text-primary fw-semibold small mb-1">Support</p>
                            <h2 class="h4 fw-bold mb-2">Besoin d’assistance ?</h2>
                            <p class="text-muted mb-3">Nous répondons aux questions sur les rendez-vous, la pharmacie, les assurances, la facturation et l’accès aux dossiers médicaux.</p>
                            <div class="d-flex flex-wrap gap-2">
                                <a class="btn btn-primary btn-sm" href="{{ route('contact') }}"><i class="fas fa-envelope me-2" aria-hidden="true"></i>Contacter le support</a>
                                <a class="btn btn-outline-primary btn-sm" href="{{ route('faq') }}"><i class="fas fa-circle-question me-2" aria-hidden="true"></i>Voir la FAQ</a>
                            </div>
                        </div>
                        <div class="mt-3 mt-md-0">
                            <div class="rounded-3 bg-primary-subtle text-primary p-3 text-center">
                                <i class="fas fa-headset fa-2x mb-2" aria-hidden="true"></i>
                                <div class="small fw-semibold">Réponse sous 24h</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-3">
                @php
                    $topics = [
                        [
                            'title' => 'Rendez-vous & téléconsultation',
                            'items' => [
                                'Prendre ou reprogrammer un rendez-vous',
                                'Préparer votre téléconsultation (audio/vidéo, documents)',
                                'Comprendre les notifications et rappels'
                            ],
                            'icon' => 'fa-calendar-check'
                        ],
                        [
                            'title' => 'Pharmacie & commandes',
                            'items' => [
                                'Suivre une commande et les statuts (préparation, prête, livraison)',
                                'Télécharger une ordonnance ou un bon',
                                'Alerte de stock ou produit indisponible'
                            ],
                            'icon' => 'fa-pills'
                        ],
                        [
                            'title' => 'Assurance & facturation',
                            'items' => [
                                'Demander une prise en charge ou un devis',
                                'Comprendre un reste à charge',
                                'Télécharger factures et justificatifs'
                            ],
                            'icon' => 'fa-file-invoice-dollar'
                        ],
                    ];
                @endphp
                @foreach($topics as $topic)
                    <div class="col-md-4">
                        <div class="card shadow-sm border-0 h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-2">
                                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-primary text-white me-3" style="width:42px;height:42px;">
                                        <i class="fas {{ $topic['icon'] }}" aria-hidden="true"></i>
                                    </div>
                                    <h6 class="mb-0">{{ $topic['title'] }}</h6>
                                </div>
                                <ul class="text-muted small ps-3 mb-0">
                                    @foreach($topic['items'] as $item)
                                        <li class="mb-1">{{ $item }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="card shadow-sm border-0 mt-3">
                <div class="card-body">
                    <h5 class="fw-bold mb-3">Questions rapides</h5>
                    <div class="accordion" id="helpFaq">
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="faq1">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapse1" aria-expanded="true" aria-controls="collapse1">
                                    Comment reprogrammer un rendez-vous ?
                                </button>
                            </h2>
                            <div id="collapse1" class="accordion-collapse collapse show" aria-labelledby="faq1" data-bs-parent="#helpFaq">
                                <div class="accordion-body text-muted">
                                    Depuis la liste des rendez-vous, cliquez sur « Modifier » puis choisissez un nouveau créneau. Une notification est envoyée automatiquement.
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="faq2">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse2" aria-expanded="false" aria-controls="collapse2">
                                    Comment suivre ma commande pharmacie ?
                                </button>
                            </h2>
                            <div id="collapse2" class="accordion-collapse collapse" aria-labelledby="faq2" data-bs-parent="#helpFaq">
                                <div class="accordion-body text-muted">
                                    Rendez-vous sur « Commandes pharma » : le statut s’affiche (en préparation, prête, livraison). Vous pouvez télécharger le bon et demander une modification si besoin.
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="faq3">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse3" aria-expanded="false" aria-controls="collapse3">
                                    Comment demander une prise en charge assurance ?
                                </button>
                            </h2>
                            <div id="collapse3" class="accordion-collapse collapse" aria-labelledby="faq3" data-bs-parent="#helpFaq">
                                <div class="accordion-body text-muted">
                                    Depuis l’espace Assurance, lancez une demande, joignez l’ordonnance ou l’acte et votre carte. Vous recevez le statut (approuvé/incomplet/rejeté) et le reste à charge estimé.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm border-0 mb-3">
                <div class="card-header bg-light">
                    <h6 class="mb-0">Contact rapide</h6>
                </div>
                <div class="card-body">
                    <p class="text-muted small mb-3">Nos équipes répondent du lundi au vendredi, 8h-18h.</p>
                    <a class="btn btn-primary w-100 mb-2" href="mailto:support@lobiko.example"><i class="fas fa-envelope-open-text me-2" aria-hidden="true"></i>support@lobiko.example</a>
                    <a class="btn btn-outline-primary w-100 mb-3" href="{{ route('contact') }}"><i class="fas fa-life-ring me-2" aria-hidden="true"></i>Formulaire de contact</a>
                    <div class="d-flex align-items-center text-muted small">
                        <i class="fas fa-clock me-2" aria-hidden="true"></i>Temps de réponse moyen : &lt;24h
                    </div>
                </div>
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">Raccourcis utiles</h6>
                    <span class="badge bg-primary-subtle text-primary">FAQ</span>
                </div>
                <div class="list-group list-group-flush">
                    <a class="list-group-item list-group-item-action d-flex justify-content-between align-items-center" href="{{ route('appointments.index') }}">
                        Mes rendez-vous <i class="fas fa-arrow-right text-muted" aria-hidden="true"></i>
                    </a>
                    <a class="list-group-item list-group-item-action d-flex justify-content-between align-items-center" href="{{ route('commandes-pharma.index') }}">
                        Commandes pharma <i class="fas fa-arrow-right text-muted" aria-hidden="true"></i>
                    </a>
                    <a class="list-group-item list-group-item-action d-flex justify-content-between align-items-center" href="{{ route('admin.assurances.index') }}">
                        Assurance &amp; prises en charge <i class="fas fa-arrow-right text-muted" aria-hidden="true"></i>
                    </a>
                    <a class="list-group-item list-group-item-action d-flex justify-content-between align-items-center" href="{{ route('admin.factures.index') }}">
                        Factures &amp; paiements <i class="fas fa-arrow-right text-muted" aria-hidden="true"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
