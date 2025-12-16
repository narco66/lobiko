<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\StructureMedicale;
use App\Models\ActeMedical;
use App\Models\Service;
use App\Models\Testimonial;
use App\Models\Article;
use App\Models\Statistique;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    /**
     * Affiche la page d'accueil publique
     */
    public function index()
    {
        // Récupération des statistiques avec cache de 1 heure
        $stats = Cache::remember('home_stats', 3600, function () {
            return [
                'total_patients' => User::role('patient')->count(),
                'total_medecins' => User::role('medecin')->count(),
                'total_consultations' => DB::table('consultations')->count(),
                'total_structures' => StructureMedicale::count(),
                'satisfaction_rate' => 98, // À calculer depuis les avis
            ];
        });

        // Services principaux
        $services = Cache::remember('home_services', 3600, function () {
            return [
                [
                    'icon' => 'fa-video',
                    'title' => 'Téléconsultation',
                    'description' => 'Consultez un médecin depuis chez vous 24h/24 et 7j/7',
                    'color' => 'primary'
                ],
                [
                    'icon' => 'fa-calendar-check',
                    'title' => 'Prise de Rendez-vous',
                    'description' => 'Réservez votre consultation en quelques clics',
                    'color' => 'success'
                ],
                [
                    'icon' => 'fa-prescription',
                    'title' => 'Ordonnances Électroniques',
                    'description' => 'Recevez vos ordonnances directement sur votre téléphone',
                    'color' => 'info'
                ],
                [
                    'icon' => 'fa-pills',
                    'title' => 'Pharmacie en Ligne',
                    'description' => 'Commandez vos médicaments et faites-vous livrer',
                    'color' => 'warning'
                ],
                [
                    'icon' => 'fa-shield-alt',
                    'title' => 'Assurance Santé',
                    'description' => 'Gestion simplifiée de vos prises en charge',
                    'color' => 'danger'
                ],
                [
                    'icon' => 'fa-ambulance',
                    'title' => 'Urgences Médicales',
                    'description' => 'Assistance d\'urgence géolocalisée 24h/24',
                    'color' => 'dark'
                ]
            ];
        });

        // Médecins en vedette
        $featuredDoctors = Cache::remember('featured_doctors', 3600, function () {
            return User::role('medecin')
                ->where('certification_verified', true)
                ->with('structurePrincipale')
                ->take(8)
                ->get();
        });

        // Témoignages
        $testimonials = Cache::remember('testimonials', 3600, function () {
            return Testimonial::where('is_published', true)
                ->with('user')
                ->latest()
                ->take(6)
                ->get();
        });

        // Articles de blog récents
        $articles = Cache::remember('recent_articles', 3600, function () {
            return Article::where('is_published', true)
                ->with('author')
                ->latest()
                ->take(3)
                ->get();
        });

        // Partenaires
        $partners = Cache::remember('partners', 3600, function () {
            return [
                ['name' => 'Airtel Money', 'logo' => 'airtel-money.png'],
                ['name' => 'MTN Mobile Money', 'logo' => 'mtn-momo.png'],
                ['name' => 'Orange Money', 'logo' => 'orange-money.png'],
                ['name' => 'Moov Money', 'logo' => 'moov-money.png'],
                ['name' => 'CNAMGS', 'logo' => 'cnamgs.png'],
                ['name' => 'Sunu Assurances', 'logo' => 'sunu.png'],
            ];
        });

        $specialities = User::activeProfessionals()
            ->whereNotNull('specialite')
            ->select('specialite')
            ->distinct()
            ->orderBy('specialite')
            ->pluck('specialite');

        $cities = StructureMedicale::query()
            ->whereNotNull('adresse_ville')
            ->select('adresse_ville')
            ->distinct()
            ->orderBy('adresse_ville')
            ->pluck('adresse_ville');

        return view('home.index', compact(
            'stats',
            'services',
            'featuredDoctors',
            'testimonials',
            'articles',
            'partners',
            'specialities',
            'cities'
        ));
    }

    /**
     * Page À propos
     */
    public function about()
    {
        $team = Cache::remember('team_members', 3600, function () {
            return [
                [
                    'name' => 'Dr. Jean-Marc Ndobo',
                    'role' => 'Directeur Médical',
                    'photo' => 'team1.jpg',
                    'bio' => 'Plus de 20 ans d\'expérience en médecine générale'
                ],
                [
                    'name' => 'Marie Owono',
                    'role' => 'Directrice Technique',
                    'photo' => 'team2.jpg',
                    'bio' => 'Experte en santé digitale et systèmes d\'information'
                ],
                [
                    'name' => 'Paul Mba',
                    'role' => 'Responsable Partenariats',
                    'photo' => 'team3.jpg',
                    'bio' => 'Spécialiste des relations avec les assurances'
                ],
            ];
        });

        $milestones = [
            ['year' => '2023', 'event' => 'Création de LOBIKO'],
            ['year' => '2024', 'event' => 'Lancement de la téléconsultation'],
            ['year' => '2025', 'event' => 'Extension dans 5 pays d\'Afrique'],
        ];

        return view('home.about', compact('team', 'milestones'));
    }

    /**
     * Page Services
     */
    public function services()
    {
        $services = Service::where('is_active', true)
            ->orderBy('order')
            ->get();

        return view('home.services', compact('services'));
    }

    /**
     * Page Contact
     */
    public function contact()
    {
        $offices = [
            [
                'city' => 'Libreville',
                'country' => 'Gabon',
                'address' => 'Immeuble Shell, Boulevard Triomphal',
                'phone' => '+241 01 23 45 67',
                'email' => 'gabon@lobiko.com',
                'is_headquarters' => true
            ],
            [
                'city' => 'Douala',
                'country' => 'Cameroun',
                'address' => 'Rue des Palmiers, Akwa',
                'phone' => '+237 233 42 42 42',
                'email' => 'cameroun@lobiko.com',
                'is_headquarters' => false
            ],
        ];

        return view('home.contact', compact('offices'));
    }

    /**
     * Traitement du formulaire de contact
     */
    public function submitContact(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'nullable|string|max:20',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:5000',
            'g-recaptcha-response' => 'nullable'
        ]);

        // Enregistrer le message en base de données
        DB::table('contact_messages')->insert([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'subject' => $validated['subject'],
            'message' => $validated['message'],
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'created_at' => now(),
        ]);

        // Envoyer un email aux administrateurs
        // Mail::to(config('mail.admin_email'))->queue(new ContactMessage($validated));

        return back()->with('success', 'Votre message a été envoyé avec succès. Nous vous répondrons dans les plus brefs délais.');
    }

    /**
     * Page Tarifs
     */
    public function pricing()
    {
        $plans = [
            [
                'name' => 'Basique',
                'price' => 0,
                'currency' => 'FCFA',
                'period' => 'mois',
                'features' => [
                    'Prise de rendez-vous',
                    '3 téléconsultations/mois',
                    'Ordonnances électroniques',
                    'Support par email',
                ],
                'is_popular' => false,
                'cta' => 'Commencer Gratuitement'
            ],
            [
                'name' => 'Premium',
                'price' => 5000,
                'currency' => 'FCFA',
                'period' => 'mois',
                'features' => [
                    'Téléconsultations illimitées',
                    'Priorité sur les rendez-vous',
                    'Livraison gratuite médicaments',
                    'Support 24/7',
                    'Dossier médical complet',
                    'Rappels automatiques',
                ],
                'is_popular' => true,
                'cta' => 'Essai Gratuit 7 jours'
            ],
            [
                'name' => 'Famille',
                'price' => 15000,
                'currency' => 'FCFA',
                'period' => 'mois',
                'features' => [
                    'Jusqu\'à 6 membres',
                    'Tous les avantages Premium',
                    'Gestionnaire de compte dédié',
                    'Bilans de santé annuels',
                    'Assurance santé intégrée',
                ],
                'is_popular' => false,
                'cta' => 'Contactez-nous'
            ],
        ];

        return view('home.pricing', compact('plans'));
    }

    /**
     * Page FAQ
     */
    public function faq()
    {
        $faqs = Cache::remember('faqs', 3600, function () {
            return [
                [
                    'category' => 'Général',
                    'questions' => [
                        [
                            'question' => 'Qu\'est-ce que LOBIKO ?',
                            'answer' => 'LOBIKO est une plateforme de santé digitale qui facilite l\'accès aux soins en Afrique en connectant patients, médecins, pharmacies et assurances.'
                        ],
                        [
                            'question' => 'Dans quels pays LOBIKO est-il disponible ?',
                            'answer' => 'LOBIKO est actuellement disponible au Gabon, au Cameroun, au Sénégal, en Côte d\'Ivoire et au Burkina Faso.'
                        ],
                    ]
                ],
                [
                    'category' => 'Téléconsultation',
                    'questions' => [
                        [
                            'question' => 'Comment fonctionne la téléconsultation ?',
                            'answer' => 'Après avoir pris rendez-vous, vous recevez un lien pour rejoindre la consultation vidéo à l\'heure prévue. Le médecin peut ensuite vous prescrire des médicaments électroniquement.'
                        ],
                        [
                            'question' => 'Les téléconsultations sont-elles remboursées ?',
                            'answer' => 'Oui, si vous avez une assurance partenaire, les téléconsultations peuvent être prises en charge selon votre contrat.'
                        ],
                    ]
                ],
                [
                    'category' => 'Paiement',
                    'questions' => [
                        [
                            'question' => 'Quels moyens de paiement acceptez-vous ?',
                            'answer' => 'Nous acceptons Mobile Money (Airtel, MTN, Orange, Moov), les cartes bancaires et les virements.'
                        ],
                        [
                            'question' => 'Mes paiements sont-ils sécurisés ?',
                            'answer' => 'Oui, tous les paiements sont cryptés et sécurisés selon les normes PCI-DSS.'
                        ],
                    ]
                ],
            ];
        });

        return view('home.faq', compact('faqs'));
    }

    /**
     * Recherche de professionnels de santé
     */
    public function searchProfessionals(Request $request)
    {
        $specialities = User::activeProfessionals()
            ->whereNotNull('specialite')
            ->select('specialite')
            ->distinct()
            ->orderBy('specialite')
            ->pluck('specialite');

        $cities = StructureMedicale::query()
            ->whereNotNull('adresse_ville')
            ->select('adresse_ville')
            ->distinct()
            ->orderBy('adresse_ville')
            ->pluck('adresse_ville');

        $query = User::activeProfessionals()
            ->with(['structurePrincipale', 'structures']);

        $query->when($request->filled('speciality'), function ($q) use ($request) {
            $speciality = $request->input('speciality');
            $q->where('specialite', 'like', "%{$speciality}%");
        });

        $query->when($request->filled('city'), function ($q) use ($request) {
            $city = $request->input('city');
            $q->where(function ($inner) use ($city) {
                $inner->where('adresse_ville', 'like', "%{$city}%")
                    ->orWhereHas('structures', function ($structureQuery) use ($city) {
                        $structureQuery->where('adresse_ville', 'like', "%{$city}%");
                    });
            });
        });

        $query->when($request->filled('search'), function ($q) use ($request) {
            $search = $request->input('search');
            $q->where(function ($inner) use ($search) {
                $inner->where('nom', 'like', "%{$search}%")
                    ->orWhere('prenom', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhereHas('structures', function ($structureQuery) use ($search) {
                        $structureQuery->where('nom_structure', 'like', "%{$search}%");
                    });
            });
        });

        $professionals = $query->paginate(12)->appends($request->query());

        return view('home.search', [
            'professionals' => $professionals,
            'specialities' => $specialities,
            'cities' => $cities,
            'filters' => $request->only(['speciality', 'city', 'search']),
        ]);
    }


    /**
     * Profil praticien public
     */
    public function doctorProfile(User $doctor)
    {
        $doctor->load(['structurePrincipale', 'structures']);

        $structure = $doctor->structurePrincipale ?? $doctor->structures->first();
        $city = $doctor->adresse_ville ?? ($structure->adresse_ville ?? '');

        $stats = [
            'experience' => $doctor->experience ?? 5,
            'note_moyenne' => $doctor->note_moyenne ?? null,
            'nombre_evaluations' => $doctor->nombre_evaluations ?? 0,
        ];

        return view('doctor.profile', [
            'doctor' => $doctor,
            'structure' => $structure,
            'city' => $city,
            'stats' => $stats,
        ]);
    }
}
