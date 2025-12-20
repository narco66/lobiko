@extends('layouts.app')

@section('title', 'LOBIKO - Votre Santé, Notre Priorité')

@section('content')

<!-- Hero Section -->
<section class="hero-section position-relative overflow-hidden" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh;">
    <div class="container py-5">
        <div class="row align-items-center min-vh-100">
            <div class="col-lg-6 text-white">
                <h1 class="display-3 fw-bold mb-4 animate__animated animate__fadeInLeft">
                    Votre Santé,<br>
                    <span class="text-warning">Notre Priorité</span>
                </h1>
                <p class="lead mb-4 animate__animated animate__fadeInLeft animate__delay-1s">
                    LOBIKO révolutionne l'accès aux soins en Afrique. Consultez un médecin,
                    commandez vos médicaments et gérez votre santé depuis votre téléphone.
                </p>
                <div class="d-flex flex-column flex-sm-row gap-3 animate__animated animate__fadeInUp animate__delay-2s">
                    <a href="{{ route('register') }}" class="btn btn-warning btn-lg px-5 py-3 rounded-pill">
                        <i class="fas fa-user-plus me-2"></i> Commencer Gratuitement
                    </a>
                    <a href="#services" class="btn btn-outline-light btn-lg px-5 py-3 rounded-pill">
                        <i class="fas fa-play-circle me-2"></i> Découvrir
                    </a>
                </div>

                <!-- Stats rapides -->
                <div class="row mt-5 g-4">
                    <div class="col-6 col-md-3">
                        <div class="text-center">
                            <h3 class="fw-bold mb-0 counter" data-target="{{ $stats['total_patients'] }}">0</h3>
                            <small>Patients</small>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="text-center">
                            <h3 class="fw-bold mb-0 counter" data-target="{{ $stats['total_medecins'] }}">0</h3>
                            <small>Médecins</small>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="text-center">
                            <h3 class="fw-bold mb-0 counter" data-target="{{ $stats['total_consultations'] }}">0</h3>
                            <small>Consultations</small>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="text-center">
                            <h3 class="fw-bold mb-0">{{ $stats['satisfaction_rate'] }}%</h3>
                            <small>Satisfaction</small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="position-relative">
                    <img src="{{ asset('images/hero-doctor.png') }}" alt="Médecin LOBIKO"
                         class="img-fluid animate__animated animate__fadeInRight">

                    <!-- Floating cards -->
                    <div class="floating-card position-absolute top-0 start-0 bg-white rounded-3 shadow p-3 animate__animated animate__fadeInDown animate__delay-3s">
                        <div class="d-flex align-items-center">
                            <div class="avatar-sm bg-success rounded-circle me-3 d-flex align-items-center justify-content-center">
                                <i class="fas fa-check text-white"></i>
                            </div>
                            <div>
                                <small class="text-muted d-block">Consultation réussie</small>
                                <strong>Dr. Marie Owono</strong>
                            </div>
                        </div>
                    </div>

                    <div class="floating-card position-absolute bottom-0 end-0 bg-white rounded-3 shadow p-3 animate__animated animate__fadeInUp animate__delay-4s">
                        <div class="d-flex align-items-center">
                            <div class="avatar-sm bg-warning rounded-circle me-3 d-flex align-items-center justify-content-center">
                                <i class="fas fa-pills text-white"></i>
                            </div>
                            <div>
                                <small class="text-muted d-block">Médicaments livrés</small>
                                <strong>Pharmacie Centrale</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Waves animation -->
    <div class="wave-container">
        <svg class="waves" xmlns="http://www.w3.org/2000/svg" viewBox="0 24 150 28" preserveAspectRatio="none">
            <defs>
                <path id="wave" d="M-160 44c30 0 58-18 88-18s 58 18 88 18 58-18 88-18 58 18 88 18 v44h-352z" />
            </defs>
            <g class="parallax">
                <use href="#wave" x="48" y="0" fill="rgba(255,255,255,0.7)" />
                <use href="#wave" x="48" y="3" fill="rgba(255,255,255,0.5)" />
                <use href="#wave" x="48" y="5" fill="rgba(255,255,255,0.3)" />
                <use href="#wave" x="48" y="7" fill="#fff" />
            </g>
        </svg>
    </div>
</section>

<!-- Search Section -->
<section class="search-section py-4 bg-white shadow-sm sticky-top" style="top: 76px; z-index: 1000;">
    <div class="container">
        @php
            $specialityOptions = ($specialities ?? collect())->filter()->unique()->values();
            $cityOptions = ($cities ?? collect())->filter()->unique()->values();
        @endphp
        <form action="{{ route('search.professionals') }}" method="GET" class="search-form">
            <div class="row g-3 align-items-center">
                <div class="col-md-3">
                    <select name="speciality" class="form-select form-select-lg">
                        <option value="">Toutes les spécialités</option>
                        @forelse($specialityOptions as $spec)
                            <option value="{{ $spec }}" @selected(request('speciality') === $spec)>{{ $spec }}</option>
                        @empty
                            @foreach(['Médecine générale', 'Cardiologie', 'Dermatologie', 'Pédiatrie'] as $spec)
                                <option value="{{ $spec }}" @selected(request('speciality') === $spec)>{{ $spec }}</option>
                            @endforeach
                        @endforelse
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="city" class="form-select form-select-lg">
                        <option value="">Toutes les villes</option>
                        @forelse($cityOptions as $city)
                            <option value="{{ $city }}" @selected(request('city') === $city)>{{ $city }}</option>
                        @empty
                            @foreach(['Libreville', 'Port-Gentil', 'Franceville', 'Oyem'] as $city)
                                <option value="{{ $city }}" @selected(request('city') === $city)>{{ $city }}</option>
                            @endforeach
                        @endforelse
                    </select>
                </div>
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control form-control-lg"
                           placeholder="Nom du médecin ou établissement..." value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary btn-lg w-100">
                        <i class="fas fa-search me-2"></i> Rechercher
                    </button>
                </div>
            </div>
        </form>
    </div>
</section>

<!-- Services Section -->
<section id="services" class="services-section py-5 bg-light">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="display-4 fw-bold mb-3">Nos Services</h2>
            <p class="lead text-muted">Tout ce dont vous avez besoin pour votre santé, au même endroit</p>
        </div>

        <div class="row g-4">
            @foreach($services as $service)
            <div class="col-lg-4 col-md-6">
                <div class="service-card h-100 bg-white rounded-4 shadow-sm p-4 text-center hover-lift">
                    <div class="service-icon mb-4">
                        <div class="icon-circle bg-{{ $service['color'] }} bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                            <i class="fas {{ $service['icon'] }} text-{{ $service['color'] }} fs-2"></i>
                        </div>
                    </div>
                    <h4 class="mb-3">{{ $service['title'] }}</h4>
                    <p class="text-muted">{{ $service['description'] }}</p>
                    <a href="{{ route('services') }}" class="btn btn-sm btn-outline-{{ $service['color'] }} rounded-pill mt-3">
                        En savoir plus <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- How It Works Section -->
<section class="how-it-works py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="display-4 fw-bold mb-3">Comment ça marche ?</h2>
            <p class="lead text-muted">Accédez aux soins en 4 étapes simples</p>
        </div>

        <div class="row g-4">
            <div class="col-md-3">
                <div class="step-card text-center">
                    <div class="step-number bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-4" style="width: 60px; height: 60px;">
                        <span class="fs-4 fw-bold">1</span>
                    </div>
                    <h5>Inscrivez-vous</h5>
                    <p class="text-muted small">Créez votre compte gratuit en quelques secondes</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="step-card text-center">
                    <div class="step-number bg-success text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-4" style="width: 60px; height: 60px;">
                        <span class="fs-4 fw-bold">2</span>
                    </div>
                    <h5>Choisissez un médecin</h5>
                    <p class="text-muted small">Trouvez le spécialiste adapté à vos besoins</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="step-card text-center">
                    <div class="step-number bg-warning text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-4" style="width: 60px; height: 60px;">
                        <span class="fs-4 fw-bold">3</span>
                    </div>
                    <h5>Consultez</h5>
                    <p class="text-muted small">En cabinet ou par téléconsultation</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="step-card text-center">
                    <div class="step-number bg-info text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-4" style="width: 60px; height: 60px;">
                        <span class="fs-4 fw-bold">4</span>
                    </div>
                    <h5>Recevez vos soins</h5>
                    <p class="text-muted small">Ordonnances et médicaments livrés</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Featured Doctors Section -->
@if($featuredDoctors->count() > 0)
<section class="featured-doctors py-5 bg-light">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="display-4 fw-bold mb-3">Nos Médecins Partenaires</h2>
            <p class="lead text-muted">Des professionnels qualifiés à votre service</p>
        </div>

        <div class="row g-4">
            @foreach($featuredDoctors as $doctor)
            <div class="col-lg-3 col-md-6">
                <div class="doctor-card bg-white rounded-4 shadow-sm overflow-hidden hover-lift h-100">
                    <div class="doctor-image position-relative">
                        <img src="{{ $doctor->photo_url ?? asset('images/default-doctor.jpg') }}"
                             alt="{{ $doctor->nom }} {{ $doctor->prenom }}"
                             class="img-fluid" style="height: 250px; width: 100%; object-fit: cover;">
                        <div class="doctor-badge position-absolute top-0 end-0 m-3">
                            <span class="badge bg-success rounded-pill">
                                <i class="fas fa-check-circle me-1"></i> Vérifié
                            </span>
                        </div>
                    </div>
                    <div class="doctor-info p-4">
                        <h5 class="mb-1">Dr. {{ $doctor->prenom }} {{ $doctor->nom }}</h5>
                        <p class="text-primary mb-2">{{ $doctor->specialite->nom ?? 'Généraliste' }}</p>
                        <p class="text-muted small mb-3">
                            <i class="fas fa-map-marker-alt me-1"></i>
                            {{ $doctor->structure->ville ?? 'Libreville' }}
                        </p>
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="rating">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="fas fa-star {{ $i <= $doctor->rating ? 'text-warning' : 'text-muted' }}"></i>
                                @endfor
                            </div>
                            <a href="{{ route('doctor.profile', $doctor->id) }}" class="btn btn-sm btn-outline-primary rounded-pill">
                                Voir profil
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <div class="text-center mt-5">
            <a href="{{ route('search.professionals') }}" class="btn btn-primary btn-lg rounded-pill px-5">
                Voir tous les médecins <i class="fas fa-arrow-right ms-2"></i>
            </a>
        </div>
    </div>
</section>
@endif

<!-- Testimonials Section -->
@if($testimonials->count() > 0)
<section class="testimonials py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="display-4 fw-bold mb-3">Ce que disent nos patients</h2>
            <p class="lead text-muted">Des milliers de patients satisfaits</p>
        </div>

        <div class="testimonials-slider">
            <div class="row g-4">
                @foreach($testimonials as $testimonial)
                <div class="col-lg-4 col-md-6">
                    <div class="testimonial-card bg-white rounded-4 shadow-sm p-4 h-100">
                        <div class="testimonial-rating mb-3">
                            @for($i = 1; $i <= 5; $i++)
                                <i class="fas fa-star {{ $i <= $testimonial->rating ? 'text-warning' : 'text-muted' }}"></i>
                            @endfor
                        </div>
                        <p class="testimonial-text mb-4">
                            "{{ $testimonial->content }}"
                        </p>
                        <div class="testimonial-author d-flex align-items-center">
                            <img src="{{ $testimonial->user->photo_url ?? asset('images/default-avatar.jpg') }}"
                                 alt="{{ $testimonial->user->nom }}"
                                 class="rounded-circle me-3" style="width: 50px; height: 50px; object-fit: cover;">
                            <div>
                                <h6 class="mb-0">{{ $testimonial->user->prenom }} {{ $testimonial->user->nom }}</h6>
                                <small class="text-muted">{{ $testimonial->created_at->diffForHumans() }}</small>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</section>
@endif

<!-- Mobile App Section -->
<section class="mobile-app py-5 bg-primary text-white">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h2 class="display-4 fw-bold mb-4">
                    Téléchargez l'application LOBIKO
                </h2>
                <p class="lead mb-4">
                    Accédez à tous nos services depuis votre smartphone.
                    Disponible sur iOS et Android.
                </p>
                <ul class="list-unstyled mb-4">
                    <li class="mb-2">
                        <i class="fas fa-check-circle text-warning me-2"></i>
                        Consultations vidéo HD
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-check-circle text-warning me-2"></i>
                        Notifications en temps réel
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-check-circle text-warning me-2"></i>
                        Mode hors ligne disponible
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-check-circle text-warning me-2"></i>
                        Paiement sécurisé intégré
                    </li>
                </ul>
                <div class="d-flex gap-3">
                    <a href="#" class="btn btn-dark btn-lg rounded-pill px-4">
                        <i class="fab fa-apple me-2"></i> App Store
                    </a>
                    <a href="#" class="btn btn-success btn-lg rounded-pill px-4">
                        <i class="fab fa-google-play me-2"></i> Google Play
                    </a>
                </div>
            </div>
            <div class="col-lg-6">
                <img src="{{ asset('images/mobile-app-mockup.png') }}" alt="Application LOBIKO" class="img-fluid">
            </div>
        </div>
    </div>
</section>

<!-- Blog Section -->
@if($articles->count() > 0)
<section class="blog py-5 bg-light">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="display-4 fw-bold mb-3">Actualités Santé</h2>
            <p class="lead text-muted">Restez informé sur les dernières actualités médicales</p>
        </div>

        <div class="row g-4">
            @foreach($articles as $article)
            <div class="col-lg-4 col-md-6">
                <article class="blog-card bg-white rounded-4 shadow-sm overflow-hidden hover-lift h-100">
                    <div class="blog-image">
                        <img src="{{ $article->featured_image ?? asset('images/default-blog.jpg') }}"
                             alt="{{ $article->title }}"
                             class="img-fluid" style="height: 200px; width: 100%; object-fit: cover;">
                    </div>
                    <div class="blog-content p-4">
                        <div class="blog-meta mb-3">
                            <span class="badge bg-primary rounded-pill">{{ $article->category->name ?? 'Santé' }}</span>
                            <small class="text-muted ms-2">{{ $article->created_at->format('d M Y') }}</small>
                        </div>
                        <h5 class="mb-3">{{ $article->title }}</h5>
                        <p class="text-muted mb-3">{{ Str::limit($article->excerpt, 100) }}</p>
                        <a href="{{ route('blog.show', $article->slug) }}" class="btn btn-sm btn-outline-primary rounded-pill">
                            Lire la suite <i class="fas fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                </article>
            </div>
            @endforeach
        </div>

        <div class="text-center mt-5">
            <a href="{{ route('blog.index') }}" class="btn btn-primary btn-lg rounded-pill px-5">
                Voir tous les articles <i class="fas fa-arrow-right ms-2"></i>
            </a>
        </div>
    </div>
</section>
@endif

<!-- Partners Section -->
<section class="partners py-5" id="partners">
    <div class="container">
        <div class="text-center mb-4">
            <p class="text-uppercase text-primary fw-semibold small mb-2">Partenaires</p>
            <h2 class="display-5 fw-bold mb-2">Ils renforcent le parcours de soins</h2>
            <p class="lead text-muted mb-0">Structures, pharmacies, assureurs et acteurs tech engagés à nos côtés.</p>
        </div>

        <div class="row g-4 align-items-stretch">
            @foreach($partners as $partner)
                <div class="col-6 col-md-4 col-lg-3">
                    <div class="h-100 p-3 border rounded text-center shadow-sm bg-white partner-card">
                        <img src="{{ asset('images/partners/' . $partner['logo']) }}"
                             alt="{{ $partner['name'] }}"
                             class="img-fluid mb-3 grayscale hover-color" style="max-height: 80px;">
                        <h6 class="fw-semibold mb-1">{{ $partner['name'] }}</h6>
                        @if(!empty($partner['sector']))
                            <p class="text-muted small mb-2">{{ $partner['sector'] }}</p>
                        @endif
                        <span class="badge bg-primary-subtle text-primary small">Partenaire</span>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="text-center mt-4">
            <a href="{{ route('partners') }}" class="btn btn-outline-primary rounded-pill px-4">
                Devenir partenaire
            </a>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="cta-section py-5 bg-gradient text-white" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
    <div class="container text-center">
        <h2 class="display-4 fw-bold mb-4">Prêt à prendre soin de votre santé ?</h2>
        <p class="lead mb-5">Rejoignez des milliers de patients qui font confiance à LOBIKO</p>
        <div class="d-flex justify-content-center gap-3">
            <a href="{{ route('register') }}" class="btn btn-warning btn-lg rounded-pill px-5 py-3">
                <i class="fas fa-user-plus me-2"></i> Créer un compte gratuit
            </a>
            <a href="{{ route('contact') }}" class="btn btn-outline-light btn-lg rounded-pill px-5 py-3">
                <i class="fas fa-phone me-2"></i> Nous contacter
            </a>
        </div>
    </div>
</section>

@endsection

@push('styles')
<style>
    /* Hero Section Styles */
    .hero-section {
        position: relative;
    }

    .wave-container {
        position: absolute;
        bottom: 0;
        left: 0;
        width: 100%;
        overflow: hidden;
        line-height: 0;
    }

    .waves {
        position: relative;
        width: 100%;
        height: 15vh;
        margin-bottom: -7px;
        min-height: 100px;
        max-height: 150px;
    }

    .parallax > use {
        animation: move-forever 25s cubic-bezier(.55,.5,.45,.5) infinite;
    }

    .parallax > use:nth-child(1) {
        animation-delay: -2s;
        animation-duration: 7s;
    }

    .parallax > use:nth-child(2) {
        animation-delay: -3s;
        animation-duration: 10s;
    }

    .parallax > use:nth-child(3) {
        animation-delay: -4s;
        animation-duration: 13s;
    }

    .parallax > use:nth-child(4) {
        animation-delay: -5s;
        animation-duration: 20s;
    }

    @keyframes move-forever {
        0% {
            transform: translate3d(-90px, 0, 0);
        }
        100% {
            transform: translate3d(85px, 0, 0);
        }
    }

    /* Floating Cards */
    .floating-card {
        animation: float 3s ease-in-out infinite;
    }

    @keyframes float {
        0%, 100% {
            transform: translateY(0);
        }
        50% {
            transform: translateY(-20px);
        }
    }

    /* Counter Animation */
    .counter {
        font-size: 2.5rem;
    }

    /* Hover Effects */
    .hover-lift {
        transition: all 0.3s ease;
    }

    .hover-lift:hover {
        transform: translateY(-10px);
        box-shadow: 0 10px 30px rgba(0,0,0,0.1) !important;
    }

    /* Service Cards */
    .service-card {
        transition: all 0.3s ease;
        border: 1px solid transparent;
    }

    .service-card:hover {
        border-color: var(--bs-primary);
        transform: translateY(-5px);
    }

    /* Partner Logos */
    .grayscale {
        filter: grayscale(100%);
        opacity: 0.6;
        transition: all 0.3s ease;
    }

    .grayscale:hover {
        filter: grayscale(0%);
        opacity: 1;
    }

    /* Avatar */
    .avatar-sm {
        width: 40px;
        height: 40px;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .hero-section {
            text-align: center;
        }

        .display-3 {
            font-size: 2.5rem;
        }

        .counter {
            font-size: 1.8rem;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    // Counter Animation
    document.addEventListener('DOMContentLoaded', function() {
        const counters = document.querySelectorAll('.counter');
        const speed = 200;

        const animateCounter = (counter) => {
            const target = +counter.getAttribute('data-target');
            const increment = target / speed;

            const updateCount = () => {
                const count = +counter.innerText;
                if (count < target) {
                    counter.innerText = Math.ceil(count + increment);
                    setTimeout(updateCount, 1);
                } else {
                    counter.innerText = target.toLocaleString();
                }
            };

            updateCount();
        };

        // Intersection Observer for counter animation
        const observerOptions = {
            threshold: 0.5,
            rootMargin: '0px'
        };

        const counterObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    animateCounter(entry.target);
                    counterObserver.unobserve(entry.target);
                }
            });
        }, observerOptions);

        counters.forEach(counter => {
            counterObserver.observe(counter);
        });
    });

    // Smooth scroll
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
</script>
@endpush
