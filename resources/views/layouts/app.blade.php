<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="LOBIKO - Plateforme de santé digitale pour l'Afrique. Téléconsultation, prise de rendez-vous, ordonnances électroniques et livraison de médicaments.">
    <meta name="keywords" content="santé, médecin, téléconsultation, Afrique, Gabon, pharmacie, assurance maladie">

    <title>@yield('title', 'LOBIKO - Votre Santé, Notre Priorité')</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Animate.css -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">

    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <!-- Select2 -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <!-- Custom CSS -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

    <!-- Page Specific Styles -->
    @stack('styles')

    <style>
        :root {
            --primary-color: #667eea;
            --secondary-color: #764ba2;
            --success-color: #48bb78;
            --warning-color: #f6ad55;
            --danger-color: #f56565;
            --info-color: #4299e1;
            --light-color: #f7fafc;
            --dark-color: #2d3748;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            color: #333;
            line-height: 1.6;
            overflow-x: hidden;
        }

        /* Navbar Styles */
        .navbar-custom {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
        }

        .navbar-custom.scrolled {
            padding: 0.5rem 0;
            box-shadow: 0 2px 20px rgba(0, 0, 0, 0.1);
        }

        .navbar-brand {
            font-weight: 800;
            font-size: 1.8rem;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .nav-link {
            font-weight: 500;
            color: #333 !important;
            margin: 0 0.5rem;
            transition: all 0.3s ease;
            position: relative;
        }

        .nav-link:hover {
            color: var(--primary-color) !important;
        }

        .nav-link::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 50%;
            width: 0;
            height: 3px;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            transition: all 0.3s ease;
            transform: translateX(-50%);
        }

        .nav-link:hover::after,
        .nav-link.active::after {
            width: 80%;
        }

        /* Footer Styles */
        .footer {
            background: linear-gradient(135deg, #1a202c, #2d3748);
            color: #fff;
            padding: 4rem 0 2rem;
        }

        .footer-widget h5 {
            color: #fff;
            margin-bottom: 1.5rem;
            font-weight: 600;
            position: relative;
            padding-bottom: 0.5rem;
        }

        .footer-widget h5::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 50px;
            height: 3px;
            background: var(--warning-color);
        }

        .footer-link {
            color: #cbd5e0;
            text-decoration: none;
            transition: all 0.3s ease;
            display: inline-block;
            margin-bottom: 0.5rem;
        }

        .footer-link:hover {
            color: var(--warning-color);
            transform: translateX(5px);
        }

        /* Button Styles */
        .btn-gradient {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            border: none;
            transition: all 0.3s ease;
        }

        .btn-gradient:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
        }

        /* Loading Spinner */
        .loading-spinner {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.9);
            z-index: 9999;
            justify-content: center;
            align-items: center;
        }

        .spinner {
            width: 50px;
            height: 50px;
            border: 5px solid #f3f3f3;
            border-top: 5px solid var(--primary-color);
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Scroll to Top Button */
        #scrollToTop {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            border: none;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            font-size: 20px;
            cursor: pointer;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
            z-index: 1000;
        }

        #scrollToTop.show {
            opacity: 1;
            visibility: visible;
        }

        #scrollToTop:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .navbar-brand {
                font-size: 1.5rem;
            }

            .nav-link {
                margin: 0.25rem 0;
            }

            .footer {
                text-align: center;
            }

            .footer-widget h5::after {
                left: 50%;
                transform: translateX(-50%);
            }
        }
    </style>
</head>
<body>
    @php
        use Illuminate\Support\Facades\Gate;
    @endphp
    <!-- Loading Spinner -->
    <div class="loading-spinner" id="loadingSpinner">
        <div class="spinner"></div>
    </div>

    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light navbar-custom fixed-top">
        <div class="container">
            <a class="navbar-brand" href="{{ route('home') }}">
                <i class="fas fa-heartbeat me-2"></i>LOBIKO
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav mx-auto">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">
                            Accueil
                        </a>
                    </li>
                    @auth
                        @php
                            $canSeeBackend = auth()->user()?->hasAnyRole(['Super Admin', 'Admin', 'super-admin', 'admin']) || auth()->user()?->can('users.view') || Gate::check('viewAny', \App\Models\User::class);
                        @endphp
                        @if($canSeeBackend)
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="#" id="adminDropdown" role="button" data-bs-toggle="dropdown">
                                    Backend / Admin
                                </a>
                                <ul class="dropdown-menu" aria-labelledby="adminDropdown">
                                    <li><a class="dropdown-item" href="{{ route('dashboard') }}"><i class="fas fa-gauge-high me-2"></i>Dashboard</a></li>
                                    <li><a class="dropdown-item" href="{{ route('users.index') }}"><i class="fas fa-users me-2"></i>Utilisateurs</a></li>
                                    <li><a class="dropdown-item" href="{{ route('dossiers-medicaux.index') }}"><i class="fas fa-notes-medical me-2"></i>Dossiers médicaux</a></li>
                                    <li><a class="dropdown-item" href="{{ route('consultations.index') }}"><i class="fas fa-stethoscope me-2"></i>Consultations</a></li>
                                    @if(Route::has('admin.structures.index'))
                                        <li><hr class="dropdown-divider"></li>
                                        <li><a class="dropdown-item" href="{{ route('admin.structures.index') }}"><i class="fas fa-hospital me-2"></i>Structures</a></li>
                                        <li><a class="dropdown-item" href="{{ route('admin.doctors.index') }}"><i class="fas fa-user-md me-2"></i>Médecins</a></li>
                                        <li><a class="dropdown-item" href="{{ route('admin.specialties.index') }}"><i class="fas fa-sitemap me-2"></i>Spécialités</a></li>
                                        <li><a class="dropdown-item" href="{{ route('admin.services.index') }}"><i class="fas fa-layer-group me-2"></i>Services</a></li>
                                        <li><a class="dropdown-item" href="{{ route('partners') }}"><i class="fas fa-handshake me-2"></i>Partenaires</a></li>
                                    @endif
                                    @if(Route::has('admin.structures.index'))
                                        <li><hr class="dropdown-divider"></li>
                                        <li><a class="dropdown-item" href="{{ route('admin.structures.index') }}"><i class="fas fa-hospital me-2"></i>Structures</a></li>
                                        <li><a class="dropdown-item" href="{{ route('admin.doctors.index') }}"><i class="fas fa-user-md me-2"></i>Médecins</a></li>
                                        <li><a class="dropdown-item" href="{{ route('admin.specialties.index') }}"><i class="fas fa-sitemap me-2"></i>Spécialités</a></li>
                                        <li><a class="dropdown-item" href="{{ route('admin.services.index') }}"><i class="fas fa-layer-group me-2"></i>Services</a></li>
                                        <li><a class="dropdown-item" href="{{ route('admin.payments.index') }}"><i class="fas fa-credit-card me-2"></i>Paiements</a></li>
                                        <li><a class="dropdown-item" href="{{ route('teleconsultation.index') }}"><i class="fas fa-video me-2"></i>Téléconsultation</a></li>
                                    @endif
                                    @if(Route::has('admin.blog.posts.index') && auth()->check())
                                        <li><hr class="dropdown-divider"></li>
                                        <li><a class="dropdown-item {{ request()->routeIs('admin.blog.posts.*') ? 'active' : '' }}" href="{{ route('admin.blog.posts.index') }}"><i class="fas fa-newspaper me-2"></i>Articles (Blog)</a></li>
                                        <li><a class="dropdown-item {{ request()->routeIs('admin.blog.categories.*') ? 'active' : '' }}" href="{{ route('admin.blog.categories.index') }}"><i class="fas fa-folder-tree me-2"></i>Catégories</a></li>
                                        <li><a class="dropdown-item {{ request()->routeIs('admin.blog.tags.*') ? 'active' : '' }}" href="{{ route('admin.blog.tags.index') }}"><i class="fas fa-tags me-2"></i>Tags</a></li>
                                        <li><a class="dropdown-item {{ request()->routeIs('admin.blog.media.*') ? 'active' : '' }}" href="{{ route('admin.blog.media.index') }}"><i class="fas fa-photo-video me-2"></i>Médias</a></li>
                                    @endif
                                    @if(Route::has('ordonnances.index'))
                                        <li><a class="dropdown-item" href="{{ route('ordonnances.index') }}"><i class="fas fa-file-prescription me-2"></i>Ordonnances</a></li>
                                    @endif
                                    @if(Route::has('factures.index'))
                                        <li><a class="dropdown-item" href="{{ route('factures.index') }}"><i class="fas fa-file-invoice-dollar me-2"></i>Factures</a></li>
                                    @endif
                                </ul>
                            </li>
                        @else
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                                    Backend / Admin
                                </a>
                            </li>
                        @endcan
                    @endauth
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="servicesDropdown" role="button" data-bs-toggle="dropdown">
                            Services
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="servicesDropdown">
                            <li><a class="dropdown-item" href="{{ route('services.teleconsultation') }}">
                                <i class="fas fa-video me-2"></i> T?l?consultation
                            </a></li>
                            <li><a class="dropdown-item" href="{{ route('appointments.create') }}">
                                <i class="fas fa-calendar me-2"></i> Planifier un rendez-vous
                            </a></li>
                            <li><a class="dropdown-item" href="{{ route('appointments.index') }}">
                                <i class="fas fa-calendar-check me-2"></i> Mes rendez-vous
                            </a></li>
                            <li><a class="dropdown-item" href="{{ route('teleconsultation.index') }}">
                                <i class="fas fa-video-camera me-2"></i> Mes t?l?consultations
                            </a></li>
                            <li><a class="dropdown-item" href="{{ route('services.pharmacy') }}">
                                <i class="fas fa-pills me-2"></i> Pharmacie en ligne
                            </a></li>
                            <li><a class="dropdown-item" href="{{ route('services.insurance') }}">
                                <i class="fas fa-shield-alt me-2"></i> Assurance sant?
                            </a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="{{ route('services') }}">
                                <i class="fas fa-th-large me-2"></i> Tous les services
                            </a></li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('search.professionals') ? 'active' : '' }}" href="{{ route('search.professionals') }}">
                            Trouver un médecin
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('pricing') ? 'active' : '' }}" href="{{ route('pricing') }}">
                            Tarifs
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('about') ? 'active' : '' }}" href="{{ route('about') }}">
                            À propos
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('contact') ? 'active' : '' }}" href="{{ route('contact') }}">
                            Contact
                        </a>
                    </li>
                </ul>

                <div class="d-flex gap-2">
                    @guest
                        <a href="{{ route('login') }}" class="btn btn-outline-primary rounded-pill px-4">
                            <i class="fas fa-sign-in-alt me-2"></i> Connexion
                        </a>
                        <a href="{{ route('register') }}" class="btn btn-gradient rounded-pill px-4">
                            <i class="fas fa-user-plus me-2"></i> S'inscrire
                        </a>
                    @else
                        <div class="dropdown">
                            <button class="btn btn-outline-primary rounded-pill dropdown-toggle" type="button" id="userDropdown" data-bs-toggle="dropdown">
                                <i class="fas fa-user-circle me-2"></i> {{ Auth::user()->prenom }}
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                                <li><a class="dropdown-item" href="{{ route('dashboard') }}">
                                    <i class="fas fa-tachometer-alt me-2"></i> Tableau de bord
                                </a></li>
                                <li><a class="dropdown-item" href="{{ route('profile.edit') }}">
                                    <i class="fas fa-user-edit me-2"></i> Mon profil
                                </a></li>
                                <li><a class="dropdown-item" href="{{ route('appointments.index') }}">
                                    <i class="fas fa-calendar-check me-2"></i> Mes rendez-vous
                                </a></li>
                                <li><a class="dropdown-item" href="{{ route('teleconsultation.index') }}">
                                    <i class="fas fa-video me-2"></i> Mes t?l?consultations
                                </a></li>
                                <li><a class="dropdown-item" href="{{ route('prescriptions.index') }}">
                                    <i class="fas fa-prescription me-2"></i> Mes ordonnances
                                </a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="dropdown-item">
                                            <i class="fas fa-sign-out-alt me-2"></i> D?connexion
                                        </button>
                                    </form>
                                    <a class="dropdown-item" href="{{ route('logout.get') }}">
                                        <i class="fas fa-sign-out-alt me-2"></i> D?connexion (alternative)
                                    </a>
                                </li>
                            </ul>
                        </div>
                    @endguest
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main style="padding-top: 76px;">
        @hasSection('content')
            @yield('content')
        @else
            {{ $slot ?? '' }}
        @endif
    </main>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-4 col-md-6">
                    <div class="footer-widget">
                        <h3 class="text-white mb-3">
                            <i class="fas fa-heartbeat me-2"></i>LOBIKO
                        </h3>
                        <p class="mb-4">
                            Votre plateforme de santé digitale en Afrique.
                            Accédez aux meilleurs soins médicaux depuis votre téléphone.
                        </p>
                        <div class="social-links">
                            <a href="#" class="btn btn-outline-light btn-sm rounded-circle me-2">
                                <i class="fab fa-facebook-f"></i>
                            </a>
                            <a href="#" class="btn btn-outline-light btn-sm rounded-circle me-2">
                                <i class="fab fa-twitter"></i>
                            </a>
                            <a href="#" class="btn btn-outline-light btn-sm rounded-circle me-2">
                                <i class="fab fa-instagram"></i>
                            </a>
                            <a href="#" class="btn btn-outline-light btn-sm rounded-circle me-2">
                                <i class="fab fa-linkedin-in"></i>
                            </a>
                            <a href="#" class="btn btn-outline-light btn-sm rounded-circle">
                                <i class="fab fa-youtube"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <div class="col-lg-2 col-md-6">
                    <div class="footer-widget">
                        <h5>Services</h5>
                        <ul class="list-unstyled">
                            <li><a href="{{ route('services.teleconsultation') }}" class="footer-link">Téléconsultation</a></li>
                            <li><a href="{{ route('appointments.create') }}" class="footer-link">Rendez-vous</a></li>
                            <li><a href="{{ route('services.pharmacy') }}" class="footer-link">Pharmacie</a></li>
                            <li><a href="{{ route('services.insurance') }}" class="footer-link">Assurance</a></li>
                            <li><a href="{{ route('services.emergency') }}" class="footer-link">Urgences</a></li>
                        </ul>
                    </div>
                </div>

                <div class="col-lg-2 col-md-6">
                    <div class="footer-widget">
                        <h5>Entreprise</h5>
                        <ul class="list-unstyled">
                            <li><a href="{{ route('about') }}" class="footer-link">À propos</a></li>
                            <li><a href="{{ route('careers') }}" class="footer-link">Carrières</a></li>
                            <li><a href="{{ route('partners') }}" class="footer-link">Partenaires</a></li>
                            <li><a href="{{ route('blog.index') }}" class="footer-link">Blog</a></li>
                            <li><a href="{{ route('press') }}" class="footer-link">Presse</a></li>
                        </ul>
                    </div>
                </div>

                <div class="col-lg-2 col-md-6">
                    <div class="footer-widget">
                        <h5>Support</h5>
                        <ul class="list-unstyled">
                            <li><a href="{{ route('faq') }}" class="footer-link">FAQ</a></li>
                            <li><a href="{{ route('help') }}" class="footer-link">Centre d'aide</a></li>
                            <li><a href="{{ route('contact') }}" class="footer-link">Contact</a></li>
                            <li><a href="{{ route('privacy') }}" class="footer-link">Confidentialité</a></li>
                            <li><a href="{{ route('terms') }}" class="footer-link">CGU</a></li>
                        </ul>
                    </div>
                </div>

                <div class="col-lg-2 col-md-6">
                    <div class="footer-widget">
                        <h5>Contact</h5>
                        <ul class="list-unstyled">
                            <li class="mb-2">
                                <i class="fas fa-map-marker-alt me-2"></i>
                                Libreville, Gabon
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-phone me-2"></i>
                                +241 01 23 45 67
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-envelope me-2"></i>
                                contact@lobiko.com
                            </li>
                            <li>
                                <i class="fas fa-clock me-2"></i>
                                24h/24, 7j/7
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <hr class="my-4 border-secondary">

            <div class="row align-items-center">
                <div class="col-md-6">
                    <p class="mb-0">
                        &copy; 2025 LOBIKO. Tous droits réservés.
                    </p>
                </div>
                <div class="col-md-6 text-md-end">
                    <div class="payment-methods">
                        <img src="{{ asset('images/payments/visa.png') }}" alt="Visa" class="payment-icon">
                        <img src="{{ asset('images/payments/mastercard.png') }}" alt="Mastercard" class="payment-icon">
                        <img src="{{ asset('images/payments/airtel-money.png') }}" alt="Airtel Money" class="payment-icon">
                        <img src="{{ asset('images/payments/mtn-momo.png') }}" alt="MTN Mobile Money" class="payment-icon">
                        <img src="{{ asset('images/payments/orange-money.png') }}" alt="Orange Money" class="payment-icon">
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!-- Scroll to Top Button -->
    <button id="scrollToTop" onclick="scrollToTop()">
        <i class="fas fa-chevron-up"></i>
    </button>

    <!-- WhatsApp Float Button -->
    <a href="https://wa.me/24101234567" target="_blank" class="whatsapp-float">
        <i class="fab fa-whatsapp"></i>
    </a>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Custom JS -->
    <script>
        // Navbar scroll effect
        window.addEventListener('scroll', function() {
            const navbar = document.querySelector('.navbar-custom');
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }

            // Show/hide scroll to top button
            const scrollBtn = document.getElementById('scrollToTop');
            if (window.scrollY > 300) {
                scrollBtn.classList.add('show');
            } else {
                scrollBtn.classList.remove('show');
            }
        });

        // Scroll to top function
        function scrollToTop() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        }

        // Initialize Select2
        $(document).ready(function() {
            $('.select2').select2({
                theme: 'bootstrap-5'
            });
        });

        // Show loading spinner
        function showLoading() {
            document.getElementById('loadingSpinner').style.display = 'flex';
        }

        // Hide loading spinner
        function hideLoading() {
            document.getElementById('loadingSpinner').style.display = 'none';
        }

        // Flash messages with SweetAlert
        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Succès!',
                text: "{{ session('success') }}",
                timer: 3000,
                showConfirmButton: false
            });
        @endif

        @if(session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Erreur!',
                text: "{{ session('error') }}",
                timer: 3000,
                showConfirmButton: false
            });
        @endif

        @if(session('warning'))
            Swal.fire({
                icon: 'warning',
                title: 'Attention!',
                text: "{{ session('warning') }}",
                timer: 3000,
                showConfirmButton: false
            });
        @endif

        @if(session('info'))
            Swal.fire({
                icon: 'info',
                title: 'Information',
                text: "{{ session('info') }}",
                timer: 3000,
                showConfirmButton: false
            });
        @endif
    </script>

    <style>
        .payment-icon {
            height: 30px;
            margin: 0 5px;
            opacity: 0.7;
            filter: grayscale(100%);
            transition: all 0.3s ease;
        }

        .payment-icon:hover {
            opacity: 1;
            filter: grayscale(0%);
        }

        .whatsapp-float {
            position: fixed;
            bottom: 80px;
            right: 20px;
            background: #25d366;
            color: white;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            text-decoration: none;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            transition: all 0.3s ease;
            z-index: 999;
        }

        .whatsapp-float:hover {
            background: #128c7e;
            color: white;
            transform: scale(1.1);
        }
    </style>

    <!-- Page Specific Scripts -->
    @stack('scripts')
</body>
</html>
