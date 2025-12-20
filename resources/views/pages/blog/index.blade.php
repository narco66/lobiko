@extends('layouts.app')
@section('title', 'Blog LOBIKO')

@section('content')
<div class="container py-4">
    <x-lobiko.page-header
        title="Blog LOBIKO"
        subtitle="Actualités santé digitale, produit et cas clients"
        :breadcrumbs="[
            ['label' => 'Accueil', 'href' => route('home')],
            ['label' => 'Blog']
        ]"
    />

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-body">
                    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-md-between">
                        <div>
                            <p class="text-uppercase text-primary fw-semibold small mb-1">Nouveautés produit</p>
                            <h2 class="h4 fw-bold mb-2">Plateforme unifiée : pharmacie, assurance et rendez-vous</h2>
                            <p class="text-muted mb-3">Découvrez comment nous connectons les pharmacies, les assurés et les médecins autour d'un dossier patient unique avec stocks temps réel, devis/factures synchronisés et téléconsultation sécurisée.</p>
                            <div class="d-flex align-items-center gap-3 small text-muted mb-3">
                                <span><i class="fas fa-user-circle me-1" aria-hidden="true"></i>Équipe Produit</span>
                                <span><i class="fas fa-clock me-1" aria-hidden="true"></i>5 min</span>
                                <span><i class="fas fa-tag me-1" aria-hidden="true"></i>Produit</span>
                            </div>
                            <a href="#" class="btn btn-primary btn-sm"><i class="fas fa-book-open me-2" aria-hidden="true"></i>Lire l'article</a>
                        </div>
                        <div class="mt-3 mt-md-0 ms-md-3">
                            <div class="rounded-3 bg-primary-subtle text-primary p-3 text-center">
                                <i class="fas fa-laptop-medical fa-2x mb-2" aria-hidden="true"></i>
                                <div class="small fw-semibold">Mise à jour Jan 2025</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-3">
                @php
                    $posts = [
                        [
                            'title' => 'Bonnes pratiques de sécurité pour la téléconsultation',
                            'excerpt' => 'Checklist encryption TLS, contrôles d’accès, journalisation et gestion des consentements patients.',
                            'tag' => 'Sécurité',
                            'time' => '4 min'
                        ],
                        [
                            'title' => 'Pharmacies : suivre vos stocks et commandes en temps réel',
                            'excerpt' => 'Flux commandes, préparation, livraison/retrait, alertes de rupture et rapprochement des fournisseurs.',
                            'tag' => 'Pharmacie',
                            'time' => '6 min'
                        ],
                        [
                            'title' => 'Assurance et tiers payant : réduire les délais de prise en charge',
                            'excerpt' => 'Synchronisation des devis/factures, statuts de remboursement et communication avec les assureurs.',
                            'tag' => 'Assurance',
                            'time' => '5 min'
                        ],
                        [
                            'title' => 'Mettre en place un portail patient moderne',
                            'excerpt' => 'Rendez-vous en ligne, notifications, dossiers partagés et paiement intégré.',
                            'tag' => 'Parcours patient',
                            'time' => '3 min'
                        ],
                    ];
                @endphp
                @foreach ($posts as $post)
                    <div class="col-md-6">
                        <div class="card h-100 shadow-sm border-0">
                            <div class="card-body d-flex flex-column">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="badge bg-primary-subtle text-primary">{{ $post['tag'] }}</span>
                                    <span class="text-muted small"><i class="fas fa-clock me-1" aria-hidden="true"></i>{{ $post['time'] }}</span>
                                </div>
                                <h5 class="fw-bold mb-2">{{ $post['title'] }}</h5>
                                <p class="text-muted small mb-3">{{ $post['excerpt'] }}</p>
                                <div class="mt-auto">
                                    <a href="#" class="text-decoration-none small fw-semibold">
                                        Lire <i class="fas fa-arrow-right ms-1" aria-hidden="true"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm border-0 mb-3">
                <div class="card-body">
                    <h6 class="fw-bold mb-2">Catégories</h6>
                    <div class="d-flex flex-wrap gap-2">
                        @foreach (['Produit', 'Pharmacie', 'Assurance', 'Parcours patient', 'Sécurité', 'Finance'] as $cat)
                            <a href="#" class="badge bg-light text-body border">{{ $cat }}</a>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="card shadow-sm border-0 mb-3">
                <div class="card-body">
                    <h6 class="fw-bold mb-3">Articles populaires</h6>
                    <ul class="list-unstyled mb-0">
                        <li class="mb-3">
                            <a href="#" class="fw-semibold d-block text-decoration-none">Mettre en place un workflow de facturation santé</a>
                            <span class="text-muted small">Finance • 4 min</span>
                        </li>
                        <li class="mb-3">
                            <a href="#" class="fw-semibold d-block text-decoration-none">Optimiser la prise de rendez-vous en ligne</a>
                            <span class="text-muted small">Parcours patient • 3 min</span>
                        </li>
                        <li class="mb-3">
                            <a href="#" class="fw-semibold d-block text-decoration-none">Sécuriser vos échanges de documents médicaux</a>
                            <span class="text-muted small">Sécurité • 5 min</span>
                        </li>
                        <li>
                            <a href="#" class="fw-semibold d-block text-decoration-none">Connecter pharmacies et médecins</a>
                            <span class="text-muted small">Pharmacie • 6 min</span>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h6 class="fw-bold mb-2">Restez informé</h6>
                    <p class="text-muted small mb-3">Recevez nos mises à jour produit, bonnes pratiques et études de cas (1 email par mois).</p>
                    <form>
                        <div class="mb-2">
                            <label class="form-label visually-hidden" for="blog-email">Email</label>
                            <input type="email" id="blog-email" class="form-control" placeholder="votre.email@example.com">
                        </div>
                        <button type="button" class="btn btn-primary w-100 btn-sm">S’abonner</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
