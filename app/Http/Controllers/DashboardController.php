<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Consultation;
use App\Models\RendezVous;
use App\Models\Facture;
use App\Models\Paiement;
use App\Models\Ordonnance;
use App\Models\CommandePharmacie;
use App\Models\StructureMedicale;
use App\Models\PriseEnCharge;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Afficher le tableau de bord selon le rôle
     */
    public function index(Request $request)
    {
        $user = $request->user();

        // Rediriger vers le bon tableau de bord selon le rôle
        if ($user->hasRole(['super-admin', 'admin'])) {
            return $this->adminDashboard($user);
        }

        if ($user->hasRole(['medecin', 'infirmier', 'sage-femme', 'dentiste', 'biologiste'])) {
            return $this->medicalDashboard($user);
        }

        if ($user->hasRole('patient')) {
            return $this->patientDashboard($user);
        }

        if ($user->hasRole('pharmacien')) {
            return $this->pharmacistDashboard($user);
        }

        if ($user->hasRole(['comptable', 'assureur', 'admin-assurance'])) {
            return $this->financialDashboard($user);
        }

        if ($user->hasRole('gestionnaire-structure')) {
            return $this->structureDashboard($user);
        }

        // Par défaut, tableau de bord basique
        return view('dashboard.default', compact('user'));
    }

    /**
     * Tableau de bord administrateur
     */
    private function adminDashboard(User $user)
    {
        $stats = [
            'users_total' => User::count(),
            'users_active' => User::where('statut_compte', 'actif')->count(),
            'users_new_month' => User::whereMonth('created_at', now()->month)->count(),
            'structures_total' => StructureMedicale::count(),
            'structures_verified' => StructureMedicale::where('verified', true)->count(),
            'consultations_today' => Consultation::whereDate('date_consultation', today())->count(),
            'consultations_month' => Consultation::whereMonth('date_consultation', now()->month)->count(),
            'revenue_today' => Facture::whereDate('date_facture', today())
                ->where('statut_paiement', 'paye')
                ->sum('montant_final'),
            'revenue_month' => Facture::whereMonth('date_facture', now()->month)
                ->where('statut_paiement', 'paye')
                ->sum('montant_final'),
        ];

        // Graphiques
        $charts = $this->getAdminCharts();

        // Activités récentes
        $recentActivities = $this->getRecentActivities();

        // Alertes système
        $systemAlerts = $this->getSystemAlerts();

        return view('dashboard.admin', compact('user', 'stats', 'charts', 'recentActivities', 'systemAlerts'));
    }

    /**
     * Tableau de bord médical (médecins, infirmiers, etc.)
     */
    private function medicalDashboard(User $user)
    {
        $today = today();
        $thisMonth = now()->month;

        $stats = [
            'appointments_today' => $user->rendezVousProfessionnel()
                ->whereDate('date_heure', $today)
                ->count(),
            'appointments_upcoming' => $user->rendezVousProfessionnel()
                ->where('date_heure', '>=', now())
                ->whereIn('statut', ['en_attente', 'confirme'])
                ->count(),
            'consultations_today' => $user->consultationsProfessionnel()
                ->whereDate('date_consultation', $today)
                ->count(),
            'consultations_month' => $user->consultationsProfessionnel()
                ->whereMonth('date_consultation', $thisMonth)
                ->count(),
            'patients_month' => $user->consultationsProfessionnel()
                ->whereMonth('date_consultation', $thisMonth)
                ->distinct('patient_id')
                ->count('patient_id'),
            'prescriptions_month' => $user->ordonnancesPrescripteur()
                ->whereMonth('date_prescription', $thisMonth)
                ->count(),
            'revenue_month' => Facture::where('praticien_id', $user->id)
                ->whereMonth('date_facture', $thisMonth)
                ->where('statut_paiement', 'paye')
                ->sum('montant_final'),
            'average_rating' => $user->note_moyenne,
        ];

        // Prochains rendez-vous
        $upcomingAppointments = $user->rendezVousProfessionnel()
            ->with(['patient', 'structure'])
            ->where('date_heure', '>=', now())
            ->whereIn('statut', ['en_attente', 'confirme'])
            ->orderBy('date_heure')
            ->limit(10)
            ->get();

        // Consultations récentes
        $recentConsultations = $user->consultationsProfessionnel()
            ->with(['patient', 'structure'])
            ->orderBy('date_consultation', 'desc')
            ->limit(10)
            ->get();

        // Graphiques de performance
        $performanceCharts = $this->getMedicalPerformanceCharts($user);

        return view('dashboard.medical', compact('user', 'stats', 'upcomingAppointments', 'recentConsultations', 'performanceCharts'));
    }

    /**
     * Tableau de bord patient
     */
    private function patientDashboard(User $user)
    {
        // Créer le dossier médical s'il n'existe pas
        $dossierMedical = $user->dossierMedical()->firstOrCreate(
            ['patient_id' => $user->id],
            ['numero_dossier' => 'DME-' . now()->format('Y') . '-' . str_pad($user->id, 6, '0', STR_PAD_LEFT)]
        );

        $stats = [
            'appointments_upcoming' => $user->rendezVousPatient()
                ->where('date_heure', '>=', now())
                ->whereIn('statut', ['en_attente', 'confirme'])
                ->count(),
            'consultations_total' => $user->consultationsPatient()->count(),
            'prescriptions_active' => $user->ordonnancesPatient()
                ->where('statut', 'active')
                ->where('valide_jusqu_au', '>=', today())
                ->count(),
            'unpaid_invoices' => $user->facturesPatient()
                ->whereIn('statut_paiement', ['en_attente', 'partiel'])
                ->count(),
            'total_spent' => $user->paiements()
                ->where('statut', 'confirme')
                ->sum('montant'),
            'insurance_savings' => $user->facturesPatient()
                ->sum('montant_pec'),
        ];

        // Prochains rendez-vous
        $upcomingAppointments = $user->rendezVousPatient()
            ->with(['professionnel', 'structure'])
            ->where('date_heure', '>=', now())
            ->whereIn('statut', ['en_attente', 'confirme'])
            ->orderBy('date_heure')
            ->limit(5)
            ->get();

// Ordonnances actives
$activePrescriptions = $user->ordonnancesPatient()
    ->with(['prescripteur', 'lignes.produit'])
    ->where('statut', 'active')
    ->where('valide_jusqu_au', '>=', today())
    ->orderBy('date_prescription', 'desc')
    ->limit(5)
    ->get();

        // Factures impayées
        $unpaidInvoices = $user->facturesPatient()
            ->with(['praticien', 'structure'])
            ->whereIn('statut_paiement', ['en_attente', 'partiel'])
            ->orderBy('date_echeance')
            ->limit(5)
            ->get();

        // Historique médical récent
        $medicalHistory = $user->consultationsPatient()
            ->with(['professionnel', 'structure'])
            ->orderBy('date_consultation', 'desc')
            ->limit(10)
            ->get();

        // Informations d'assurance
        $insurance = $user->contratAssuranceActif;

        return view('dashboard.patient', compact(
            'user',
            'dossierMedical',
            'stats',
            'upcomingAppointments',
            'activePrescriptions',
            'unpaidInvoices',
            'medicalHistory',
            'insurance'
        ));
    }

    /**
     * Tableau de bord pharmacien
     */
    private function pharmacistDashboard(User $user)
    {
        $structure = $user->structurePrincipale ?? $user->structures()->first();

        if (!$structure) {
            return redirect()->route('profile.complete')
                ->with('warning', 'Veuillez compléter votre profil et associer une pharmacie.');
        }

        $stats = [
            'prescriptions_pending' => Ordonnance::where('pharmacie_dispensatrice_id', $structure->id)
                ->where('statut', 'active')
                ->count(),
            'prescriptions_today' => Ordonnance::where('pharmacie_dispensatrice_id', $structure->id)
                ->whereDate('date_dispensation', today())
                ->count(),
            'orders_pending' => CommandePharmacie::where('pharmacie_id', $structure->id)
                ->whereIn('statut', ['en_attente', 'confirmee'])
                ->count(),
            'orders_ready' => CommandePharmacie::where('pharmacie_id', $structure->id)
                ->where('statut', 'prete')
                ->count(),
            'low_stock_items' => $structure->produitsPharmaceutiques()
                ->whereColumn('quantite_disponible', '<=', 'stock_minimum')
                ->count(),
            'expired_items' => $structure->produitsPharmaceutiques()
                ->where('date_peremption', '<=', today()->addDays(30))
                ->count(),
            'revenue_today' => Facture::where('structure_id', $structure->id)
                ->whereDate('date_facture', today())
                ->where('statut_paiement', 'paye')
                ->sum('montant_final'),
            'revenue_month' => Facture::where('structure_id', $structure->id)
                ->whereMonth('date_facture', now()->month)
                ->where('statut_paiement', 'paye')
                ->sum('montant_final'),
        ];

        // Ordonnances en attente
        $pendingPrescriptions = Ordonnance::with(['patient', 'prescripteur', 'lignes.produit'])
            ->where('pharmacie_dispensatrice_id', $structure->id)
            ->where('statut', 'active')
            ->orderBy('date_prescription', 'desc')
            ->limit(10)
            ->get();

        // Commandes en cours
        $activeOrders = CommandePharmacie::with(['patient', 'lignes.produit'])
            ->where('pharmacie_id', $structure->id)
            ->whereIn('statut', ['en_attente', 'confirmee', 'en_preparation', 'prete'])
            ->orderBy('date_commande', 'desc')
            ->limit(10)
            ->get();

        // Produits en rupture ou stock bas
        $stockAlerts = $structure->produitsPharmaceutiques()
            ->with('produit')
            ->where(function ($query) {
                $query->whereColumn('quantite_disponible', '<=', 'stock_minimum')
                    ->orWhere('quantite_disponible', 0);
            })
            ->orderBy('quantite_disponible')
            ->limit(20)
            ->get();

        return view('dashboard.pharmacist', compact('user', 'structure', 'stats', 'pendingPrescriptions', 'activeOrders', 'stockAlerts'));
    }

    /**
     * Tableau de bord financier (comptable, assureur)
     */
    private function financialDashboard(User $user)
    {
        $currentMonth = now()->month;
        $lastMonth = now()->subMonth()->month;

        $stats = [
            'invoices_pending' => Facture::whereIn('statut_paiement', ['en_attente', 'partiel'])->count(),
            'invoices_overdue' => Facture::where('statut_paiement', '!=', 'paye')
                ->where('date_echeance', '<', today())
                ->count(),
            'revenue_month' => Facture::whereMonth('date_facture', $currentMonth)
                ->where('statut_paiement', 'paye')
                ->sum('montant_final'),
            'revenue_last_month' => Facture::whereMonth('date_facture', $lastMonth)
                ->where('statut_paiement', 'paye')
                ->sum('montant_final'),
            'pec_pending' => PriseEnCharge::where('statut', 'en_attente')->count(),
            'pec_approved_month' => PriseEnCharge::whereMonth('validee_at', $currentMonth)
                ->where('statut', 'validee')
                ->count(),
            'reimbursements_pending' => DB::table('remboursements_assurance')
                ->where('statut', 'en_attente')
                ->count(),
            'total_reimbursements_month' => DB::table('remboursements_assurance')
                ->whereMonth('date_paiement', $currentMonth)
                ->where('statut', 'paye')
                ->sum('montant_rembourse'),
        ];

        // Calculer les variations
        $stats['revenue_variation'] = $stats['revenue_last_month'] > 0
            ? (($stats['revenue_month'] - $stats['revenue_last_month']) / $stats['revenue_last_month']) * 100
            : 0;

        // Graphiques financiers
        $financialCharts = $this->getFinancialCharts();

        // Factures en retard
        $overdueInvoices = Facture::with(['patient', 'praticien', 'structure'])
            ->where('statut_paiement', '!=', 'paye')
            ->where('date_echeance', '<', today())
            ->orderBy('date_echeance')
            ->limit(10)
            ->get();

        // PEC en attente (pour assureurs)
        $pendingPEC = null;
        if ($user->hasRole(['assureur', 'admin-assurance'])) {
            $pendingPEC = PriseEnCharge::with(['patient', 'contrat', 'prestataire'])
                ->where('statut', 'en_attente')
                ->orderBy('date_demande')
                ->limit(10)
                ->get();
        }

        return view('dashboard.financial', compact('user', 'stats', 'financialCharts', 'overdueInvoices', 'pendingPEC'));
    }

    /**
     * Tableau de bord gestionnaire de structure
     */
    private function structureDashboard(User $user)
    {
        $structure = $user->structurePrincipale ?? $user->structures()->first();

        if (!$structure) {
            return redirect()->route('profile.complete')
                ->with('warning', 'Veuillez compléter votre profil et associer une structure.');
        }

        $stats = [
            'staff_total' => $structure->personnel()->count(),
            'staff_active' => $structure->praticiensActifs()->count(),
            'appointments_today' => $structure->rendezVous()
                ->whereDate('date_heure', today())
                ->count(),
            'consultations_month' => $structure->consultations()
                ->whereMonth('date_consultation', now()->month)
                ->count(),
            'revenue_month' => $structure->factures()
                ->whereMonth('date_facture', now()->month)
                ->where('statut_paiement', 'paye')
                ->sum('montant_final'),
            'commission_month' => $structure->factures()
                ->whereMonth('date_facture', now()->month)
                ->where('statut_paiement', 'paye')
                ->sum('montant_final') * ($structure->commission_plateforme / 100),
            'average_rating' => $structure->note_moyenne,
            'total_evaluations' => $structure->nombre_evaluations,
        ];

        // Performance des praticiens
        $practitionersPerformance = $structure->praticiensActifs()
            ->withCount([
                'consultationsProfessionnel as consultations_month' => function ($query) {
                    $query->whereMonth('date_consultation', now()->month);
                }
            ])
            ->orderBy('consultations_month', 'desc')
            ->get();

        // Graphiques de performance
        $performanceCharts = $this->getStructurePerformanceCharts($structure);

        // Planning du jour
        $todaySchedule = $structure->rendezVous()
            ->with(['patient', 'professionnel'])
            ->whereDate('date_heure', today())
            ->orderBy('date_heure')
            ->get();

        return view('dashboard.structure', compact('user', 'structure', 'stats', 'practitionersPerformance', 'performanceCharts', 'todaySchedule'));
    }

    /**
     * Obtenir les graphiques pour l'admin
     */
    private function getAdminCharts(): array
    {
        // Évolution des inscriptions (30 derniers jours)
        $registrations = User::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Répartition par rôle
        $roleDistribution = DB::table('model_has_roles')
            ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->selectRaw('roles.name as role, COUNT(*) as count')
            ->groupBy('roles.name')
            ->get();

        // Revenus par jour (30 derniers jours)
        $dailyRevenue = Facture::selectRaw('DATE(date_facture) as date, SUM(montant_final) as total')
            ->where('statut_paiement', 'paye')
            ->where('date_facture', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Consultations par type
        $consultationTypes = Consultation::selectRaw('type, COUNT(*) as count')
            ->whereMonth('date_consultation', now()->month)
            ->groupBy('type')
            ->get();

        return [
            'registrations' => $registrations,
            'roleDistribution' => $roleDistribution,
            'dailyRevenue' => $dailyRevenue,
            'consultationTypes' => $consultationTypes,
        ];
    }

    /**
     * Obtenir les graphiques de performance médicale
     */
    private function getMedicalPerformanceCharts(User $user): array
    {
        // Consultations par jour (30 derniers jours)
        $dailyConsultations = $user->consultationsProfessionnel()
            ->selectRaw('DATE(date_consultation) as date, COUNT(*) as count')
            ->where('date_consultation', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Répartition par type de consultation
        $consultationTypes = $user->consultationsProfessionnel()
            ->selectRaw('type, COUNT(*) as count')
            ->whereMonth('date_consultation', now()->month)
            ->groupBy('type')
            ->get();

        // Évolution de la note moyenne
        $ratingsEvolution = DB::table('evaluations')
            ->selectRaw('DATE(created_at) as date, AVG(note_globale) as average')
            ->where('evalue_id', $user->id)
            ->where('type_evalue', 'praticien')
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return [
            'dailyConsultations' => $dailyConsultations,
            'consultationTypes' => $consultationTypes,
            'ratingsEvolution' => $ratingsEvolution,
        ];
    }

    /**
     * Obtenir les graphiques financiers
     */
    private function getFinancialCharts(): array
    {
        // Revenus mensuels (12 derniers mois)
        $monthlyRevenue = Facture::selectRaw('YEAR(date_facture) as year, MONTH(date_facture) as month, SUM(montant_final) as total')
            ->where('statut_paiement', 'paye')
            ->where('date_facture', '>=', now()->subMonths(12))
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        // Répartition des paiements par mode
        $paymentModes = Paiement::selectRaw('mode_paiement, COUNT(*) as count, SUM(montant) as total')
            ->where('statut', 'confirme')
            ->whereMonth('date_initiation', now()->month)
            ->groupBy('mode_paiement')
            ->get();

        // Taux de recouvrement
        $recoveryRate = [
            'total_invoiced' => Facture::whereMonth('date_facture', now()->month)->sum('montant_final'),
            'total_paid' => Facture::whereMonth('date_facture', now()->month)
                ->where('statut_paiement', 'paye')
                ->sum('montant_paye'),
        ];

        // Top 10 des structures par CA
        $topStructures = StructureMedicale::withSum(['factures as revenue' => function ($query) {
                $query->whereMonth('date_facture', now()->month)
                    ->where('statut_paiement', 'paye');
            }], 'montant_final')
            ->orderBy('revenue', 'desc')
            ->limit(10)
            ->get();

        return [
            'monthlyRevenue' => $monthlyRevenue,
            'paymentModes' => $paymentModes,
            'recoveryRate' => $recoveryRate,
            'topStructures' => $topStructures,
        ];
    }

    /**
     * Obtenir les graphiques de performance de structure
     */
    private function getStructurePerformanceCharts(StructureMedicale $structure): array
    {
        // Consultations par praticien ce mois
        $consultationsByPractitioner = DB::table('consultations')
            ->join('users', 'consultations.professionnel_id', '=', 'users.id')
            ->selectRaw('users.nom, users.prenom, COUNT(*) as count')
            ->where('consultations.structure_id', $structure->id)
            ->whereMonth('consultations.date_consultation', now()->month)
            ->groupBy('users.id', 'users.nom', 'users.prenom')
            ->orderBy('count', 'desc')
            ->get();

        // Évolution du CA (12 derniers mois)
        $revenueEvolution = $structure->factures()
            ->selectRaw('YEAR(date_facture) as year, MONTH(date_facture) as month, SUM(montant_final) as total')
            ->where('statut_paiement', 'paye')
            ->where('date_facture', '>=', now()->subMonths(12))
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        // Taux d'occupation par jour de la semaine
        $occupancyByDay = $structure->rendezVous()
            ->selectRaw('DAYNAME(date_heure) as day, COUNT(*) as count')
            ->whereMonth('date_heure', now()->month)
            ->whereIn('statut', ['confirme', 'termine'])
            ->groupBy('day')
            ->get();

        return [
            'consultationsByPractitioner' => $consultationsByPractitioner,
            'revenueEvolution' => $revenueEvolution,
            'occupancyByDay' => $occupancyByDay,
        ];
    }

    /**
     * Obtenir les activités récentes
     */
    private function getRecentActivities(): array
    {
        return DB::table('audit_logs')
            ->join('users', 'audit_logs.user_id', '=', 'users.id')
            ->select('audit_logs.*', 'users.nom', 'users.prenom')
            ->orderBy('audit_logs.created_at', 'desc')
            ->limit(20)
            ->get()
            ->toArray();
    }

    /**
     * Obtenir les alertes système
     */
    private function getSystemAlerts(): array
    {
        $alerts = [];

        // Vérifier les factures en retard
        $overdueCount = Facture::where('statut_paiement', '!=', 'paye')
            ->where('date_echeance', '<', today())
            ->count();

        if ($overdueCount > 0) {
            $alerts[] = [
                'type' => 'warning',
                'message' => "{$overdueCount} factures en retard de paiement",
                'link' => route('factures.overdue'),
            ];
        }

        // Vérifier les stocks bas (pharmacies)
        $lowStockCount = DB::table('stocks_pharmacie')
            ->whereColumn('quantite_disponible', '<=', 'stock_minimum')
            ->count();

        if ($lowStockCount > 0) {
            $alerts[] = [
                'type' => 'warning',
                'message' => "{$lowStockCount} produits en stock bas",
                'link' => route('stocks.alerts'),
            ];
        }

        // Vérifier les PEC en attente
        $pendingPECCount = PriseEnCharge::where('statut', 'en_attente')
            ->where('date_demande', '<', now()->subDays(2))
            ->count();

        if ($pendingPECCount > 0) {
            $alerts[] = [
                'type' => 'info',
                'message' => "{$pendingPECCount} prises en charge en attente depuis plus de 48h",
                'link' => route('pec.pending'),
            ];
        }

        // Vérifier les certifications expirées
        $expiredCertCount = User::where('certification_verified', true)
            ->where('certification_verified_at', '<', now()->subYear())
            ->count();

        if ($expiredCertCount > 0) {
            $alerts[] = [
                'type' => 'danger',
                'message' => "{$expiredCertCount} certifications professionnelles à renouveler",
                'link' => route('users.certifications'),
            ];
        }

        return $alerts;
    }
}
