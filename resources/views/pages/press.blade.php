@extends('layouts.app')
@section('title', 'Espace presse')

@section('content')
<div class="container py-4">
    <x-lobiko.page-header
        title="Espace presse"
        subtitle="Kit presse, communiqués et contacts médias"
        :breadcrumbs="[
            ['label' => 'Accueil', 'href' => route('home')],
            ['label' => 'Presse']
        ]"
    />

    <div class="row g-3">
        <div class="col-lg-8">
            <div class="card shadow-sm mb-3">
                <div class="card-body">
                    <h5 class="fw-bold mb-2">À propos de LOBIKO</h5>
                    <p class="mb-2">LOBIKO est une plateforme de santé numérique qui connecte patients, professionnels, pharmacies et assureurs autour dƒ?Tun dossier médical unifié, des rendez-vous, de la téléconsultation, de la gestion pharmaceutique et de la facturation.</p>
                    <p class="mb-0">Nous proposons une expérience intégrée : prise de rendez-vous en ligne, ordonnances numériques, suivi des stocks pharmacie, tiers payant assurance, factures et devis, le tout sécurisé et conforme aux bonnes pratiques.</p>
                </div>
            </div>

            <div class="card shadow-sm mb-3">
                <div class="card-body">
                    <h5 class="fw-bold mb-3">Communiqués récents</h5>
                    <div class="d-flex align-items-start mb-3">
                        <div class="badge bg-primary-subtle text-primary me-3"><i class="fas fa-bullhorn me-1" aria-hidden="true"></i>Jan 2025</div>
                        <div>
                            <div class="fw-semibold">Lancement du module pharmacie unifié</div>
                            <div class="text-muted small">Suivi de stock, commandes en ligne, fournisseurs et mouvements en temps réel.</div>
                        </div>
                    </div>
                    <div class="d-flex align-items-start mb-3">
                        <div class="badge bg-primary-subtle text-primary me-3"><i class="fas fa-bullhorn me-1" aria-hidden="true"></i>Dec 2024</div>
                        <div>
                            <div class="fw-semibold">Ouverture du portail assurance</div>
                            <div class="text-muted small">Gestion des prises en charge, devis et factures synchronisés avec les partenaires.</div>
                        </div>
                    </div>
                    <div class="d-flex align-items-start">
                        <div class="badge bg-primary-subtle text-primary me-3"><i class="fas fa-bullhorn me-1" aria-hidden="true"></i>Sep 2024</div>
                        <div>
                            <div class="fw-semibold">Rendez-vous et téléconsultation améliorés</div>
                            <div class="text-muted small">Planification en ligne, rappels automatiques et salle de téléconsultation sécurisée.</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm mb-3">
                <div class="card-body">
                    <h5 class="fw-bold mb-3">Données clés</h5>
                    <div class="row g-3">
                        <div class="col-6 col-md-3">
                            <div class="p-3 border rounded text-center h-100">
                                <div class="fw-bold fs-4">30k+</div>
                                <div class="text-muted small">patients suivis</div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="p-3 border rounded text-center h-100">
                                <div class="fw-bold fs-4">1k</div>
                                <div class="text-muted small">professionnels</div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="p-3 border rounded text-center h-100">
                                <div class="fw-bold fs-4">450</div>
                                <div class="text-muted small">structures &amp; pharmacies</div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="p-3 border rounded text-center h-100">
                                <div class="fw-bold fs-4">99.9%</div>
                                <div class="text-muted small">disponibilité</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm mb-3">
                <div class="card-body">
                    <h5 class="fw-bold mb-3">Ressources médias</h5>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="border rounded p-3 h-100">
                                <div class="d-flex align-items-center mb-2">
                                    <div class="badge bg-primary text-white me-2"><i class="fas fa-download" aria-hidden="true"></i></div>
                                    <div class="fw-semibold">Kit presse</div>
                                </div>
                                <p class="text-muted small mb-3">Présentation de LOBIKO, visuels clés, chiffres et positionnement.</p>
                                <a class="btn btn-primary btn-sm w-100" href="mailto:presse@lobiko.example?subject=Demande%20kit%20presse%20LOBIKO">
                                    <i class="fas fa-cloud-download-alt me-2" aria-hidden="true"></i>Demander le kit
                                </a>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="border rounded p-3 h-100">
                                <div class="d-flex align-items-center mb-2">
                                    <div class="badge bg-primary text-white me-2"><i class="fas fa-image" aria-hidden="true"></i></div>
                                    <div class="fw-semibold">Logos &amp; charte</div>
                                </div>
                                <p class="text-muted small mb-3">Variantes logo, palette, typographies et usages autorisés.</p>
                                <a class="btn btn-outline-primary btn-sm w-100" href="mailto:presse@lobiko.example?subject=Demande%20logos%20LOBIKO">
                                    <i class="fas fa-paperclip me-2" aria-hidden="true"></i>Recevoir les assets
                                </a>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="border rounded p-3 h-100">
                                <div class="d-flex align-items-center mb-2">
                                    <div class="badge bg-primary text-white me-2"><i class="fas fa-mobile-alt" aria-hidden="true"></i></div>
                                    <div class="fw-semibold">Captures produit</div>
                                </div>
                                <p class="text-muted small mb-3">Écrans clefs : rendez-vous, dossier patient, pharmacie, facturation.</p>
                                <a class="btn btn-outline-secondary btn-sm w-100" href="mailto:presse@lobiko.example?subject=Demande%20captures%20LOBIKO">
                                    <i class="fas fa-envelope me-2" aria-hidden="true"></i>Demander un pack
                                </a>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="border rounded p-3 h-100">
                                <div class="d-flex align-items-center mb-2">
                                    <div class="badge bg-primary text-white me-2"><i class="fas fa-film" aria-hidden="true"></i></div>
                                    <div class="fw-semibold">Interviews &amp; démos</div>
                                </div>
                                <p class="text-muted small mb-3">Sessions avec lƒ?Téquipe fondatrice ou démonstrations produit guidées.</p>
                                <a class="btn btn-outline-secondary btn-sm w-100" href="mailto:presse@lobiko.example?subject=Planifier%20une%20interview%20LOBIKO">
                                    <i class="fas fa-calendar-check me-2" aria-hidden="true"></i>Planifier
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="fw-bold mb-3">Porte-parole</h5>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="p-3 border rounded h-100">
                                <div class="fw-semibold mb-1">Narcisse Odoua</div>
                                <div class="text-muted small mb-2">Fondateur &amp; CEO</div>
                                <p class="small mb-2">Vision produit, innovation santé numérique, stratégie partenariats.</p>
                                <a class="text-decoration-none small" href="mailto:presse@lobiko.example?subject=Interview%20CEO%20LOBIKO"><i class="fas fa-envelope me-1" aria-hidden="true"></i>Contacter</a>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 border rounded h-100">
                                <div class="fw-semibold mb-1">Équipe Médicale</div>
                                <div class="text-muted small mb-2">Médecins référents</div>
                                <p class="small mb-2">Parcours patient, sécurité des données cliniques et bonnes pratiques.</p>
                                <a class="text-decoration-none small" href="mailto:presse@lobiko.example?subject=Interview%20clinique%20LOBIKO"><i class="fas fa-envelope me-1" aria-hidden="true"></i>Contacter</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm mb-3">
                <div class="card-header bg-light">
                    <h6 class="mb-0">Contact presse</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-start mb-3">
                        <span class="badge bg-primary-subtle text-primary me-3"><i class="fas fa-user me-1" aria-hidden="true"></i>Press</span>
                        <div>
                            <div class="fw-semibold">Équipe Communication</div>
                            <div class="text-muted small">presse@lobiko.example</div>
                            <div class="text-muted small">+241 77 79 06 54</div>
                        </div>
                    </div>
                    <a class="btn btn-primary w-100 mb-2" href="mailto:presse@lobiko.example">
                        <i class="fas fa-envelope-open-text me-2" aria-hidden="true"></i>Écrire à l'équipe
                    </a>
                    <a class="btn btn-outline-primary w-100" href="{{ route('contact') }}">
                        <i class="fas fa-life-ring me-2" aria-hidden="true"></i>Passer par le support
                    </a>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-header bg-light d-flex align-items-center justify-content-between">
                    <h6 class="mb-0">Ressources rapides</h6>
                    <span class="badge bg-primary-subtle text-primary">Press kit</span>
                </div>
                <div class="list-group list-group-flush">
                    <a class="list-group-item list-group-item-action d-flex justify-content-between align-items-center" href="mailto:presse@lobiko.example?subject=Demande%20kit%20presse%20LOBIKO">
                        Kit presse <i class="fas fa-arrow-right text-muted" aria-hidden="true"></i>
                    </a>
                    <a class="list-group-item list-group-item-action d-flex justify-content-between align-items-center" href="mailto:presse@lobiko.example?subject=Demande%20logos%20LOBIKO">
                        Logos &amp; charte <i class="fas fa-arrow-right text-muted" aria-hidden="true"></i>
                    </a>
                    <a class="list-group-item list-group-item-action d-flex justify-content-between align-items-center" href="mailto:presse@lobiko.example?subject=Planifier%20une%20interview%20LOBIKO">
                        Interviews <i class="fas fa-arrow-right text-muted" aria-hidden="true"></i>
                    </a>
                    <a class="list-group-item list-group-item-action d-flex justify-content-between align-items-center" href="mailto:presse@lobiko.example?subject=Demande%20captures%20LOBIKO">
                        Captures produit <i class="fas fa-arrow-right text-muted" aria-hidden="true"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
