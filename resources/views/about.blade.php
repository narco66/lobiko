@extends('layouts.app')

@section('title', 'A propos de LOBIKO')

@section('content')
@php
    $metrics = [
        ['value' => '50k+', 'label' => 'Patients accompagnes', 'icon' => 'fa-heartbeat'],
        ['value' => '4 pays', 'label' => 'Presence terrain', 'icon' => 'fa-globe-africa'],
        ['value' => '2 500', 'label' => 'Professionnels verifies', 'icon' => 'fa-user-md'],
        ['value' => '97%', 'label' => 'Satisfaction moyenne', 'icon' => 'fa-face-smile'],
    ];

    $values = [
        ['title' => 'Proximite humaine', 'desc' => 'Accompagnement personnalise, au-dela des ecrans, pour chaque famille.', 'icon' => 'fa-hand-holding-heart'],
        ['title' => 'Fiabilite medicale', 'desc' => 'Reseau d\'experts certifies, dossiers securises, protocoles conformes.', 'icon' => 'fa-shield-heart'],
        ['title' => 'Impact local', 'desc' => 'Concu pour les realites africaines : connectivite, langue, mobilite.', 'icon' => 'fa-seedling'],
        ['title' => 'Accessibilite', 'desc' => 'Tarifs clairs, paiements mobiles et partenariats assurantiels.', 'icon' => 'fa-unlock-keyhole'],
    ];

    $timeline = [
        ['year' => '2021', 'title' => 'Idee et prototypes', 'desc' => 'Constat du manque d\'acces aux soins specialises et premiers tests de teleconsultation.'],
        ['year' => '2022', 'title' => 'Pilotes terrain', 'desc' => 'Lancements a Libreville et Douala, partenariats avec cliniques et pharmacies.'],
        ['year' => '2023', 'title' => 'Plateforme complete', 'desc' => 'Rendez-vous, ordonnances numeriques, pharmacie et assurance integrees.'],
        ['year' => '2024+', 'title' => 'Echelle panafricaine', 'desc' => 'Nouvelles villes, support 24/7 et reseau d\'experts elargi.'],
    ];

    $leaders = [
        ['name' => 'Pr. Marie Owono', 'role' => 'Fondatrice & CEO', 'focus' => 'Sante digitale, medecine interne', 'tag' => 'Vision strategique'],
        ['name' => 'Dr. Julien Mba', 'role' => 'CMO', 'focus' => 'Qualite clinique, telemedecine', 'tag' => 'Excellence medicale'],
        ['name' => 'Aline Nguema', 'role' => 'COO', 'focus' => 'Operations & partenariats', 'tag' => 'Execution terrain'],
    ];
@endphp

<div class="container py-5">
    <x-lobiko.page-header
        title="A propos de LOBIKO"
        subtitle="Une plateforme de sante numerique concue pour l'Afrique"
        :breadcrumbs="[
            ['label' => 'Accueil', 'href' => route('home')],
            ['label' => 'A propos']
        ]"
    />

    <div class="row g-4 align-items-center mb-5">
        <div class="col-lg-7">
            <p class="lead text-muted mb-3">Nous connectons patients, medecins, pharmacies et assurances dans une experience fluide. LOBIKO rend les soins fiables, accessibles et humains, ou que vous soyez.</p>
            <div class="d-flex flex-wrap gap-3 mb-3">
                <a href="{{ route('services') }}" class="btn btn-gradient rounded-pill px-4">
                    <i class="fas fa-th-large me-2" aria-hidden="true"></i> Decouvrir nos services
                </a>
                <a href="{{ route('contact') }}" class="btn btn-outline-primary rounded-pill px-4">
                    <i class="fas fa-comment-medical me-2" aria-hidden="true"></i> Parler a l'equipe
                </a>
            </div>
            <div class="d-flex flex-wrap gap-4 text-muted small">
                <div><strong>+120</strong> consultations/jour</div>
                <div><strong>< 8 min</strong> pour obtenir un medecin</div>
                <div><strong>Support 24/7</strong></div>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="badge bg-primary-subtle text-primary me-3"><i class="fas fa-heartbeat" aria-hidden="true"></i></div>
                        <div>
                            <p class="text-muted small mb-0">Parcours integre</p>
                            <h5 class="fw-bold mb-0">Consultation > Ordonnance > Livraison</h5>
                        </div>
                    </div>
                    <p class="text-muted mb-3">Teleconsultation, prescription securisee, paiement mobile, livraison et suivi personnalise. Fonctionne meme avec une connectivite limitee.</p>
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <span class="badge bg-primary-subtle text-primary">Support 24/7</span>
                        <span class="badge bg-success-subtle text-success">Experts verifies</span>
                        <span class="badge bg-warning-subtle text-dark">Donnees protegees</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-lg-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <p class="text-uppercase text-primary fw-semibold small mb-2">Mission</p>
                    <h3 class="fw-bold mb-3">Rendre la sante moderne accessible a tous</h3>
                    <p class="text-muted mb-3">Nous construisons l'infrastructure de sante digitale qui relie medecins, pharmacies, laboratoires et assurances. Objectif : un parcours simple, transparent et securise pour chaque patient.</p>
                    <div class="d-flex flex-wrap gap-2">
                        <span class="badge bg-primary-subtle text-primary"><i class="fas fa-bolt me-1" aria-hidden="true"></i>Rendez-vous immediat</span>
                        <span class="badge bg-light text-body"><i class="fas fa-mobile-screen me-1" aria-hidden="true"></i>Mobile-first</span>
                        <span class="badge bg-success-subtle text-success"><i class="fas fa-lock me-1" aria-hidden="true"></i>Securite & conformite</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <p class="text-uppercase text-primary fw-semibold small mb-2">Ce que nous activons</p>
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <div class="p-3 border rounded h-100">
                                <i class="fas fa-video mb-2 text-primary" aria-hidden="true"></i>
                                <h6 class="fw-bold mb-1">Teleconsultation</h6>
                                <p class="text-muted small mb-0">Video HD, messagerie, partage de fichiers.</p>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="p-3 border rounded h-100">
                                <i class="fas fa-pills mb-2 text-success" aria-hidden="true"></i>
                                <h6 class="fw-bold mb-1">Pharmacie connectee</h6>
                                <p class="text-muted small mb-0">Ordonnances securisees, stocks temps reel.</p>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="p-3 border rounded h-100">
                                <i class="fas fa-shield-alt mb-2 text-warning" aria-hidden="true"></i>
                                <h6 class="fw-bold mb-1">Assurance</h6>
                                <p class="text-muted small mb-0">Prise en charge simplifiee, tiers payant.</p>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="p-3 border rounded h-100">
                                <i class="fas fa-ambulance mb-2 text-danger" aria-hidden="true"></i>
                                <h6 class="fw-bold mb-1">Urgences</h6>
                                <p class="text-muted small mb-0">Routage automatique vers la bonne structure.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="mb-5">
        <div class="d-flex flex-column flex-lg-row align-items-start align-items-lg-center justify-content-between mb-3">
            <div>
                <p class="text-uppercase text-primary fw-semibold small mb-1">Impact</p>
                <h3 class="fw-bold mb-0">Une communaute qui grandit chaque semaine</h3>
            </div>
            <a href="{{ route('search.professionals') }}" class="btn btn-outline-primary rounded-pill">Trouver un medecin <i class="fas fa-arrow-right ms-2" aria-hidden="true"></i></a>
        </div>
        <div class="row g-3">
            @foreach($metrics as $metric)
                <div class="col-6 col-lg-3">
                    <div class="p-3 border rounded h-100 text-start">
                        <div class="text-primary mb-2"><i class="fas {{ $metric['icon'] }}" aria-hidden="true"></i></div>
                        <h4 class="fw-bold mb-1">{{ $metric['value'] }}</h4>
                        <p class="text-muted mb-0">{{ $metric['label'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <div class="py-4">
        <div class="text-center mb-4">
            <p class="text-uppercase text-primary fw-semibold small mb-1">Valeurs</p>
            <h3 class="fw-bold">Ce qui guide chaque decision</h3>
            <p class="text-muted mb-0">Concu avec les patients, soignants et partenaires.</p>
        </div>
        <div class="row g-3">
            @foreach($values as $value)
                <div class="col-md-3 col-6">
                    <div class="p-3 border rounded h-100">
                        <div class="text-primary mb-2"><i class="fas {{ $value['icon'] }}" aria-hidden="true"></i></div>
                        <h6 class="fw-bold mb-1">{{ $value['title'] }}</h6>
                        <p class="text-muted small mb-0">{{ $value['desc'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <div class="row g-4 align-items-center py-4">
        <div class="col-lg-5">
            <p class="text-uppercase text-primary fw-semibold small mb-2">Parcours</p>
            <h3 class="fw-bold mb-3">Une evolution ancree sur le terrain</h3>
            <p class="text-muted mb-3">Chaque etape est co-construite avec les patients, medecins, pharmaciens et assureurs partenaires. Nous testons vite, apprenons avec le terrain et deployons de facon responsable.</p>
            <div class="d-flex gap-2 flex-wrap">
                <span class="badge bg-primary-subtle text-primary">Co-design</span>
                <span class="badge bg-success-subtle text-success">Qualite clinique</span>
                <span class="badge bg-warning-subtle text-dark">Innovation frugale</span>
            </div>
        </div>
        <div class="col-lg-7">
            <div class="border-start ps-3">
                @foreach($timeline as $item)
                    <div class="mb-3 position-relative">
                        <div class="position-absolute" style="left:-11px; top:6px; width:10px; height:10px; border-radius:50%; background:linear-gradient(135deg,#667eea,#764ba2);"></div>
                        <div class="d-flex align-items-center mb-1">
                            <span class="badge bg-dark me-2">{{ $item['year'] }}</span>
                            <strong>{{ $item['title'] }}</strong>
                        </div>
                        <p class="text-muted mb-0">{{ $item['desc'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="py-4">
        <div class="row g-4 align-items-center">
            <div class="col-lg-7">
                <p class="text-uppercase fw-semibold small mb-2 text-primary">Equipe</p>
                <h3 class="fw-bold mb-3">Une equipe pluridisciplinaire, ancree en Afrique</h3>
                <p class="text-muted mb-3">Medecins, ingenieurs, designers de service et operateurs terrain travaillent ensemble pour livrer une experience simple et fiable. Nous recrutons des talents passionnes par l'impact sante.</p>
                <a href="{{ route('careers') }}" class="btn btn-outline-primary rounded-pill">Rejoindre l'aventure <i class="fas fa-arrow-right ms-2" aria-hidden="true"></i></a>
            </div>
            <div class="col-lg-5">
                <div class="row g-3">
                    @foreach($leaders as $leader)
                        <div class="col-12">
                            <div class="p-3 border rounded d-flex align-items-center">
                                <div class="me-3 text-primary"><i class="fas fa-user" aria-hidden="true"></i></div>
                                <div>
                                    <h6 class="mb-1 fw-bold">{{ $leader['name'] }}</h6>
                                    <p class="mb-1 small text-muted">{{ $leader['role'] }} - {{ $leader['focus'] }}</p>
                                    <span class="badge bg-warning-subtle text-dark">{{ $leader['tag'] }}</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <div class="py-4">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <div class="row align-items-center g-3">
                    <div class="col-lg-8">
                        <p class="text-uppercase text-primary fw-semibold small mb-2">Pret a demarrer ?</p>
                        <h4 class="fw-bold mb-2">Essayez LOBIKO pour vos patients, votre structure ou votre assurance.</h4>
                        <p class="text-muted mb-0">Nous vous accompagnons de l'onboarding aux premiers resultats mesurables.</p>
                    </div>
                    <div class="col-lg-4 text-lg-end">
                        <div class="d-flex flex-wrap gap-2 justify-content-lg-end">
                            <a href="{{ route('register') }}" class="btn btn-gradient px-4 rounded-pill">Creer un compte</a>
                            <a href="{{ route('contact') }}" class="btn btn-outline-primary px-4 rounded-pill">Planifier un echange</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
