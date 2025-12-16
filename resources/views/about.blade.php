@extends('layouts.app')

@section('title', 'À propos de LOBIKO')

@section('content')
@php
    $metrics = [
        ['value' => '50k+', 'label' => 'Patients accompagnés', 'icon' => 'fa-heartbeat'],
        ['value' => '4 pays', 'label' => 'Présence terrain', 'icon' => 'fa-globe-africa'],
        ['value' => '2 500', 'label' => 'Professionnels vérifiés', 'icon' => 'fa-user-md'],
        ['value' => '97%', 'label' => 'Satisfaction moyenne', 'icon' => 'fa-face-smile'],
    ];

    $values = [
        ['title' => 'Proximité humaine', 'desc' => 'Un accompagnement personnalisé, au-delà des écrans, pour chaque famille.', 'icon' => 'fa-hand-holding-heart', 'tone' => 'primary'],
        ['title' => 'Fiabilité médicale', 'desc' => 'Réseau d’experts certifiés, dossiers sécurisés, protocoles conformes aux standards.', 'icon' => 'fa-shield-heart', 'tone' => 'success'],
        ['title' => 'Impact local', 'desc' => 'Des solutions pensées pour les réalités africaines : connectivité, langue, mobilité.', 'icon' => 'fa-seedling', 'tone' => 'warning'],
        ['title' => 'Accessibilité', 'desc' => 'Tarifs clairs, paiements mobiles et partenariats assurantiels pour lever les freins.', 'icon' => 'fa-unlock-keyhole', 'tone' => 'info'],
    ];

    $timeline = [
        ['year' => '2021', 'title' => 'L’idée prend racine', 'desc' => 'Constat du manque d’accès aux soins spécialisés et premiers prototypes en téléconsultation.'],
        ['year' => '2022', 'title' => 'Pilote terrain', 'desc' => 'Lancements à Libreville et Douala, partenariats avec cliniques et pharmacies locales.'],
        ['year' => '2023', 'title' => 'Plateforme complète', 'desc' => 'Rendez-vous, ordonnances électroniques, pharmacie et assurance intégrée.'],
        ['year' => '2024+', 'title' => 'Échelle panafricaine', 'desc' => 'Extension à de nouvelles villes, support 24/7 et réseau d’experts élargi.'],
    ];

    $leaders = [
        ['name' => 'Pr. Marie Owono', 'role' => 'Fondatrice & CEO', 'focus' => 'Santé digitale, médecine interne', 'tag' => 'Vision stratégique'],
        ['name' => 'Dr. Julien Mba', 'role' => 'CMO', 'focus' => 'Qualité clinique, télémédecine', 'tag' => 'Excellence médicale'],
        ['name' => 'Aline Nguema', 'role' => 'COO', 'focus' => 'Opérations & partenariats', 'tag' => 'Exécution terrain'],
    ];
@endphp

<section class="about-hero position-relative overflow-hidden">
    <div class="about-hero__bg"></div>
    <div class="container position-relative py-5">
        <div class="row align-items-center g-4">
            <div class="col-lg-7">
                <p class="text-uppercase fw-semibold text-primary small mb-2">À propos de LOBIKO</p>
                <h1 class="display-4 fw-bold mb-3">La santé numérique pensée pour l’Afrique</h1>
                <p class="lead text-muted mb-4">
                    Nous connectons patients, médecins, pharmacies et assurances dans une même expérience fluide.
                    LOBIKO rend les soins fiables, accessibles et humains, où que vous soyez.
                </p>
                <div class="d-flex flex-wrap gap-3">
                    <a href="{{ route('services') }}" class="btn btn-gradient px-4 py-3 rounded-pill">
                        <i class="fas fa-th-large me-2"></i> Découvrir nos services
                    </a>
                    <a href="{{ route('contact') }}" class="btn btn-outline-primary px-4 py-3 rounded-pill">
                        <i class="fas fa-comment-medical me-2"></i> Parler à l’équipe
                    </a>
                </div>
                <div class="d-flex flex-wrap gap-4 mt-4">
                    <div>
                        <small class="text-uppercase text-muted fw-semibold">Patients conseillés</small>
                        <h4 class="fw-bold mb-0">+120 consultations/jour</h4>
                    </div>
                    <div>
                        <small class="text-uppercase text-muted fw-semibold">Temps moyen</small>
                        <h4 class="fw-bold mb-0">&lt; 8 minutes pour obtenir un médecin</h4>
                    </div>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="about-hero__card shadow-lg">
                    <div class="d-flex align-items-center mb-3">
                        <div class="avatar-circle me-3 bg-gradient">
                            <i class="fas fa-heartbeat text-white"></i>
                        </div>
                        <div>
                            <p class="mb-0 text-muted small">Plateforme intégrée</p>
                            <h5 class="fw-bold mb-0">Consultation • Ordonnance • Livraison</h5>
                        </div>
                    </div>
                    <p class="mb-4 text-muted">
                        Un parcours fluide : téléconsultation, prescription sécurisée, paiement mobile, livraison de médicaments
                        et suivi personnalisé. Pensé pour fonctionner même avec une connectivité limitée.
                    </p>
                    <div class="d-flex align-items-center gap-3">
                        <div class="badge bg-primary rounded-pill">Support 24/7</div>
                        <div class="badge bg-success rounded-pill">Experts vérifiés</div>
                        <div class="badge bg-warning rounded-pill">Données protégées</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="about-hero__shape about-hero__shape--left"></div>
    <div class="about-hero__shape about-hero__shape--right"></div>
</section>

<section class="py-5 bg-light">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-6">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body p-4">
                        <p class="text-uppercase text-primary fw-semibold small mb-2">Mission</p>
                        <h2 class="h3 fw-bold mb-3">Rendre la santé moderne accessible à tous</h2>
                        <p class="text-muted mb-4">
                            Nous construisons l’infrastructure de santé digitale qui relie médecins, pharmacies,
                            laboratoires et assurances. Objectif : un parcours simple, transparent et sécurisé pour chaque patient.
                        </p>
                        <div class="d-flex flex-wrap gap-3">
                            <div class="chip"><i class="fas fa-bolt me-2 text-warning"></i>Rendez-vous immédiat</div>
                            <div class="chip"><i class="fas fa-mobile-screen me-2 text-primary"></i>Mobile-first</div>
                            <div class="chip"><i class="fas fa-lock me-2 text-success"></i>Sécurité & conformité</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body p-4">
                        <p class="text-uppercase text-primary fw-semibold small mb-2">Ce que nous activons</p>
                        <div class="row g-3">
                            <div class="col-sm-6">
                                <div class="pill h-100">
                                    <i class="fas fa-video mb-2 text-primary"></i>
                                    <h6 class="fw-bold mb-1">Téléconsultation</h6>
                                    <p class="text-muted small mb-0">Vidéo HD, messagerie, partage de fichiers.</p>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="pill h-100">
                                    <i class="fas fa-pills mb-2 text-success"></i>
                                    <h6 class="fw-bold mb-1">Pharmacie connectée</h6>
                                    <p class="text-muted small mb-0">Ordonnances sécurisées et livraison suivie.</p>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="pill h-100">
                                    <i class="fas fa-shield-alt mb-2 text-warning"></i>
                                    <h6 class="fw-bold mb-1">Assurance santé</h6>
                                    <p class="text-muted small mb-0">Couverture simplifiée, tiers-payant mobile.</p>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="pill h-100">
                                    <i class="fas fa-ambulance mb-2 text-danger"></i>
                                    <h6 class="fw-bold mb-1">Urgences</h6>
                                    <p class="text-muted small mb-0">Canal prioritaire avec routage automatique.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="d-flex flex-column flex-lg-row align-items-start align-items-lg-center justify-content-between mb-4">
            <div>
                <p class="text-uppercase text-primary fw-semibold small mb-2">Impact mesurable</p>
                <h2 class="h3 fw-bold mb-0">Une communauté qui grandit chaque semaine</h2>
            </div>
            <a href="{{ route('search.professionals') }}" class="btn btn-outline-primary rounded-pill">
                Trouver un médecin <i class="fas fa-arrow-right ms-2"></i>
            </a>
        </div>
        <div class="row g-4">
            @foreach($metrics as $metric)
                <div class="col-6 col-lg-3">
                    <div class="metric-card h-100">
                        <div class="metric-icon">
                            <i class="fas {{ $metric['icon'] }}"></i>
                        </div>
                        <h3 class="fw-bold mb-1">{{ $metric['value'] }}</h3>
                        <p class="text-muted mb-0">{{ $metric['label'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

<section class="py-5 bg-light">
    <div class="container">
        <div class="text-center mb-4">
            <p class="text-uppercase text-primary fw-semibold small mb-2">Nos engagements</p>
            <h2 class="h3 fw-bold">Les valeurs qui guident chaque décision</h2>
            <p class="text-muted mb-0">Nous concevons la plateforme avec et pour les patients, les soignants et les partenaires.</p>
        </div>
        <div class="row g-4">
            @foreach($values as $value)
                <div class="col-lg-3 col-md-6">
                    <div class="value-card h-100">
                        <div class="value-icon text-{{ $value['tone'] }}">
                            <i class="fas {{ $value['icon'] }}"></i>
                        </div>
                        <h5 class="fw-bold mb-2">{{ $value['title'] }}</h5>
                        <p class="text-muted mb-0">{{ $value['desc'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="row g-4 align-items-center">
            <div class="col-lg-5">
                <p class="text-uppercase text-primary fw-semibold small mb-2">Parcours</p>
                <h2 class="h3 fw-bold mb-3">Une évolution ancrée sur le terrain</h2>
                <p class="text-muted mb-4">
                    Chaque étape a été co-construite avec les patients, médecins, pharmaciens et assureurs partenaires.
                    Notre approche : tester vite, apprendre avec le terrain, déployer de façon responsable.
                </p>
                <div class="d-flex gap-2 flex-wrap">
                    <span class="badge rounded-pill bg-primary">Co-design</span>
                    <span class="badge rounded-pill bg-success">Qualité clinique</span>
                    <span class="badge rounded-pill bg-warning text-dark">Innovation frugale</span>
                </div>
            </div>
            <div class="col-lg-7">
                <div class="timeline">
                    @foreach($timeline as $item)
                        <div class="timeline-item">
                            <div class="timeline-dot"></div>
                            <div class="timeline-content">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="badge bg-dark">{{ $item['year'] }}</span>
                                    <strong>{{ $item['title'] }}</strong>
                                </div>
                                <p class="text-muted mb-0">{{ $item['desc'] }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-5 bg-primary text-white">
    <div class="container">
        <div class="row g-4 align-items-center">
            <div class="col-lg-7">
                <p class="text-uppercase fw-semibold small mb-2 text-warning">Équipe</p>
                <h2 class="h3 fw-bold mb-3">Une équipe pluridisciplinaire, ancrée en Afrique</h2>
                <p class="mb-4">
                    Médecins, ingénieurs, designers de service et opérateurs terrain travaillent ensemble pour livrer une
                    expérience simple et fiable. Nous recrutons des talents passionnés par l’impact santé.
                </p>
                <a href="{{ route('careers') }}" class="btn btn-light text-primary rounded-pill">
                    Rejoindre l’aventure <i class="fas fa-arrow-right ms-2"></i>
                </a>
            </div>
            <div class="col-lg-5">
                <div class="row g-3">
                    @foreach($leaders as $leader)
                        <div class="col-12">
                            <div class="leader-card">
                                <div class="avatar-circle bg-light text-primary me-3">
                                    <i class="fas fa-user"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1 fw-bold">{{ $leader['name'] }}</h6>
                                    <p class="mb-1 small text-white-50">{{ $leader['role'] }} • {{ $leader['focus'] }}</p>
                                    <span class="badge bg-warning text-dark">{{ $leader['tag'] }}</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-5 bg-light">
    <div class="container">
        <div class="cta-card shadow-sm border-0">
            <div class="row align-items-center g-3">
                <div class="col-lg-8">
                    <p class="text-uppercase text-primary fw-semibold small mb-2">Prêt à démarrer ?</p>
                    <h3 class="fw-bold mb-2">Essayez LOBIKO pour vos patients, votre structure ou votre assurance.</h3>
                    <p class="text-muted mb-0">Nous vous accompagnons de l’onboarding aux premiers résultats mesurables.</p>
                </div>
                <div class="col-lg-4 text-lg-end">
                    <div class="d-flex flex-wrap gap-2 justify-content-lg-end">
                        <a href="{{ route('register') }}" class="btn btn-gradient px-4 rounded-pill">
                            Créer un compte
                        </a>
                        <a href="{{ route('contact') }}" class="btn btn-outline-primary px-4 rounded-pill">
                            Planifier un échange
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('styles')
<style>
    .about-hero {
        min-height: 80vh;
        background: linear-gradient(120deg, rgba(102, 126, 234, 0.08), rgba(118, 75, 162, 0.1));
    }
    .about-hero__bg {
        position: absolute;
        inset: 0;
        background: radial-gradient(circle at 20% 20%, rgba(102,126,234,0.25), transparent 35%),
                    radial-gradient(circle at 80% 10%, rgba(118,75,162,0.2), transparent 30%),
                    radial-gradient(circle at 50% 80%, rgba(240,180,41,0.15), transparent 35%);
        filter: blur(20px);
        opacity: 0.9;
    }
    .about-hero__shape {
        position: absolute;
        width: 240px;
        height: 240px;
        border-radius: 40%;
        background: linear-gradient(135deg, rgba(102,126,234,0.15), rgba(118,75,162,0.18));
        opacity: 0.7;
    }
    .about-hero__shape--left { top: 10%; left: -60px; transform: rotate(-12deg); }
    .about-hero__shape--right { bottom: -40px; right: -40px; transform: rotate(18deg); }
    .about-hero__card {
        background: #fff;
        border-radius: 18px;
        padding: 28px;
        border: 1px solid rgba(0,0,0,0.04);
    }
    .bg-gradient {
        background: linear-gradient(135deg, #667eea, #764ba2);
    }
    .avatar-circle {
        width: 56px;
        height: 56px;
        border-radius: 50%;
        display: grid;
        place-items: center;
        font-size: 22px;
    }
    .chip {
        display: inline-flex;
        align-items: center;
        padding: 8px 12px;
        border-radius: 999px;
        background: rgba(102,126,234,0.08);
        color: #2d3748;
        font-weight: 600;
        font-size: 0.9rem;
    }
    .pill {
        padding: 14px;
        border-radius: 14px;
        background: #f9fafc;
        border: 1px solid rgba(102,126,234,0.08);
    }
    .metric-card {
        background: #fff;
        border-radius: 16px;
        padding: 20px;
        border: 1px solid rgba(0,0,0,0.04);
        box-shadow: 0 12px 40px rgba(0,0,0,0.06);
        text-align: left;
    }
    .metric-icon {
        width: 46px;
        height: 46px;
        border-radius: 12px;
        background: rgba(102,126,234,0.12);
        color: #667eea;
        display: grid;
        place-items: center;
        margin-bottom: 12px;
    }
    .value-card {
        background: #fff;
        border-radius: 16px;
        padding: 20px;
        border: 1px solid rgba(0,0,0,0.04);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        height: 100%;
    }
    .value-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 12px 35px rgba(0,0,0,0.08);
    }
    .value-icon {
        width: 40px;
        height: 40px;
        border-radius: 12px;
        background: rgba(102,126,234,0.12);
        display: grid;
        place-items: center;
        font-size: 18px;
        margin-bottom: 10px;
    }
    .timeline {
        border-left: 2px solid rgba(102,126,234,0.3);
        padding-left: 20px;
    }
    .timeline-item { position: relative; margin-bottom: 18px; }
    .timeline-dot {
        position: absolute;
        left: -30px;
        top: 6px;
        width: 14px;
        height: 14px;
        border-radius: 50%;
        background: linear-gradient(135deg, #667eea, #764ba2);
        box-shadow: 0 0 0 6px rgba(102,126,234,0.1);
    }
    .timeline-content {
        background: #fff;
        border: 1px solid rgba(0,0,0,0.04);
        border-radius: 12px;
        padding: 12px 14px;
        box-shadow: 0 8px 24px rgba(0,0,0,0.05);
    }
    .leader-card {
        background: rgba(255,255,255,0.08);
        border-radius: 14px;
        padding: 12px 14px;
        display: flex;
        align-items: center;
        backdrop-filter: blur(4px);
        border: 1px solid rgba(255,255,255,0.15);
    }
    .cta-card {
        border-radius: 18px;
        padding: 28px;
        background: #fff;
    }
    @media (max-width: 768px) {
        .about-hero { min-height: auto; }
        .about-hero__shape { display: none; }
        .metric-card, .value-card, .timeline-content, .cta-card { text-align: left; }
    }
</style>
@endpush
