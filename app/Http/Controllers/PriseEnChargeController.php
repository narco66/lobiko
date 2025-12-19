<?php

namespace App\Http\Controllers;

use App\Http\Requests\PriseEnChargeRequest;
use App\Models\PriseEnCharge;
use App\Models\ContratAssurance;
use App\Models\Devis;
use App\Models\Facture;
use App\Models\User;
use App\Services\AssuranceService;
use App\Services\NotificationService;
use App\Exports\PrisesEnChargeExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use PDF;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class PriseEnChargeController extends Controller
{
    use AuthorizesRequests;

    protected $assuranceService;
    protected $notificationService;

    public function __construct(
        AssuranceService $assuranceService,
        NotificationService $notificationService
    ) {
        parent::__construct();
        $this->assuranceService = $assuranceService;
        $this->notificationService = $notificationService;

        $this->middleware('auth');
        $this->middleware('permission:pec.view')->only(['index', 'show']);
        $this->middleware('permission:pec.create')->only(['create', 'store']);
        $this->middleware('permission:pec.validate')->only(['accepter', 'refuser']);
        $this->middleware('permission:pec.cancel')->only(['annuler']);
    }

    /**
     * Afficher la liste des prises en charge
     */
    public function index(Request $request)
    {
        $query = PriseEnCharge::with(['patient', 'praticien', 'contrat.assurance', 'facture']);

        // Filtres selon le rôle
        if (Auth::user()->hasRole('patient')) {
            $query->where('patient_id', Auth::id());
        } elseif (Auth::user()->hasRole('praticien')) {
            $query->where('praticien_id', Auth::id());
        } elseif (Auth::user()->hasRole('assureur')) {
            $query->whereHas('contrat', function($q) {
                $q->where('assurance_id', Auth::id());
            });
        }

        // Filtres additionnels
        if ($request->filled('statut')) {
            if ($request->statut === 'expirees') {
                $query->expirees();
            } else {
                $query->where('statut', $request->statut);
            }
        }

        if ($request->filled('patient')) {
            $query->where('patient_id', $request->patient);
        }

        if ($request->filled('praticien')) {
            $query->where('praticien_id', $request->praticien);
        }

        if ($request->filled('date_debut')) {
            $query->whereDate('date_demande', '>=', $request->date_debut);
        }

        if ($request->filled('date_fin')) {
            $query->whereDate('date_demande', '<=', $request->date_fin);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('numero_pec', 'like', "%{$search}%")
                  ->orWhere('motif', 'like', "%{$search}%")
                  ->orWhereHas('patient', function($q2) use ($search) {
                      $q2->where('nom', 'like', "%{$search}%")
                         ->orWhere('prenom', 'like', "%{$search}%");
                  });
            });
        }

        // Tri
        $sortField = $request->get('sort', 'date_demande');
        $sortDirection = $request->get('direction', 'desc');
        $query->orderBy($sortField, $sortDirection);

        $pecs = $query->paginate(15)->withQueryString();

        // Statistiques
        $stats = $this->getStatistiques();

        if ($request->ajax()) {
            return response()->json([
                'html' => view('pec.table', compact('pecs'))->render(),
                'pagination' => $pecs->links()->render(),
                'stats' => $stats
            ]);
        }

        return view('pec.index', compact('pecs', 'stats'));
    }

    /**
     * Afficher le formulaire de création
     */
    public function create(Request $request)
    {
        $devis = null;
        $facture = null;
        $patient = null;
        $contrats = collect();

        if ($request->has('devis_id')) {
            $devis = Devis::findOrFail($request->devis_id);
            $patient = $devis->patient;
        } elseif ($request->has('facture_id')) {
            $facture = Facture::findOrFail($request->facture_id);
            $patient = $facture->patient;
        } elseif ($request->has('patient_id')) {
            $patient = User::findOrFail($request->patient_id);
        }

        if ($patient) {
            $contrats = ContratAssurance::where('patient_id', $patient->id)
                                       ->actif()
                                       ->get();
        }

        $patients = User::role('patient')->get();
        $praticiens = User::role('praticien')->get();

        return view('pec.create', compact('devis', 'facture', 'patient', 'contrats', 'patients', 'praticiens'));
    }

    /**
     * Enregistrer une nouvelle prise en charge
     */
    public function store(PriseEnChargeRequest $request)
    {
        DB::beginTransaction();

        try {
            $data = $request->validated();

            // Vérifier le contrat d'assurance
            $contrat = ContratAssurance::findOrFail($data['contrat_id']);

            if (!$contrat->estValide()) {
                return back()
                    ->withInput()
                    ->with('error', 'Le contrat d\'assurance n\'est pas valide');
            }

            // Calculer la couverture
            $couverture = $contrat->calculerCouverture(
                $data['montant_demande'],
                $data['type_pec'] ?? null
            );

            // Créer la PEC
            $pec = PriseEnCharge::create([
                'contrat_id' => $contrat->id,
                'devis_id' => $data['devis_id'] ?? null,
                'facture_id' => $data['facture_id'] ?? null,
                'patient_id' => $contrat->patient_id,
                'praticien_id' => $data['praticien_id'] ?? Auth::id(),
                'structure_id' => $data['structure_id'] ?? Auth::user()->structure_id,
                'type_pec' => $data['type_pec'],
                'montant_demande' => $data['montant_demande'],
                'montant_accorde' => $couverture['montant_couvert'],
                'taux_pec' => $couverture['taux_applique'],
                'motif' => $data['motif'],
                'statut' => 'en_attente',
                'validite_jours' => $data['validite_jours'] ?? 30,
                'justificatifs' => $data['justificatifs'] ?? [],
            ]);

            // Si API assurance disponible, envoyer la demande
            if (config('services.assurance.api_enabled')) {
                $response = $this->assuranceService->envoyerDemandePEC($pec);

                if ($response['success']) {
                    $pec->update([
                        'statut' => $response['statut'],
                        'montant_accorde' => $response['montant_accorde'] ?? $pec->montant_accorde,
                        'commentaire_assurance' => $response['commentaire'] ?? null,
                        'date_reponse' => now(),
                    ]);
                }
            }

            // Notifier l'assureur
            $this->notificationService->notifier(
                $contrat->assurance_id,
                'Nouvelle demande de PEC',
                "Nouvelle demande de prise en charge #{$pec->numero_pec} pour {$pec->patient->nom_complet}",
                'pec',
                $pec->id
            );

            // Notifier le patient
            $this->notificationService->notifier(
                $pec->patient_id,
                'Demande de PEC créée',
                "Votre demande de prise en charge #{$pec->numero_pec} a été créée",
                'pec',
                $pec->id
            );

            DB::commit();

            return redirect()
                ->route('pec.show', $pec)
                ->with('success', 'Demande de prise en charge créée avec succès');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur création PEC: ' . $e->getMessage());

            return back()
                ->withInput()
                ->with('error', 'Erreur lors de la création de la prise en charge');
        }
    }

    /**
     * Afficher une prise en charge
     */
    public function show(PriseEnCharge $pec)
    {
        $this->authorize('view', $pec);

        $pec->load([
            'patient',
            'praticien',
            'structure',
            'contrat.assurance',
            'devis',
            'facture',
            'litiges'
        ]);

        // Vérifier l'expiration
        if ($pec->estExpiree() && $pec->statut === 'acceptee') {
            $pec->update(['statut' => 'expiree']);
        }

        return view('pec.show', compact('pec'));
    }

    /**
     * Accepter une prise en charge
     */
    public function accepter(Request $request, PriseEnCharge $pec)
    {
        $this->authorize('validate', $pec);

        if ($pec->statut !== 'en_attente') {
            return back()->with('error', 'Cette PEC ne peut plus être acceptée');
        }

        $request->validate([
            'montant_accorde' => 'nullable|numeric|min:0|max:' . $pec->montant_demande,
            'commentaire' => 'nullable|string|max:500',
        ]);

        DB::beginTransaction();

        try {
            $pec->accepter(
                $request->montant_accorde,
                $request->commentaire
            );

            // Mettre à jour le devis ou la facture si lié
            if ($pec->devis) {
                $pec->devis->update([
                    'pec_id' => $pec->id,
                    'montant_pec' => $pec->montant_accorde,
                    'reste_a_charge' => $pec->devis->montant_total - $pec->montant_accorde,
                ]);
            }

            if ($pec->facture) {
                $pec->facture->update([
                    'pec_id' => $pec->id,
                    'montant_pec' => $pec->montant_accorde,
                    'reste_a_charge' => $pec->facture->montant_total - $pec->montant_accorde,
                ]);
            }

            DB::commit();

            return redirect()
                ->route('pec.show', $pec)
                ->with('success', 'Prise en charge acceptée');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur acceptation PEC: ' . $e->getMessage());

            return back()->with('error', 'Erreur lors de l\'acceptation de la PEC');
        }
    }

    /**
     * Refuser une prise en charge
     */
    public function refuser(Request $request, PriseEnCharge $pec)
    {
        $this->authorize('validate', $pec);

        if ($pec->statut !== 'en_attente') {
            return back()->with('error', 'Cette PEC ne peut plus être refusée');
        }

        $request->validate([
            'motif_refus' => 'required|string|max:500',
        ]);

        DB::beginTransaction();

        try {
            $pec->refuser($request->motif_refus);

            // Mettre à jour le devis ou la facture si lié
            if ($pec->devis) {
                $pec->devis->update([
                    'pec_id' => null,
                    'montant_pec' => 0,
                    'reste_a_charge' => $pec->devis->montant_total,
                ]);
            }

            if ($pec->facture) {
                $pec->facture->update([
                    'pec_id' => null,
                    'montant_pec' => 0,
                    'reste_a_charge' => $pec->facture->montant_total,
                ]);
            }

            DB::commit();

            return redirect()
                ->route('pec.show', $pec)
                ->with('success', 'Prise en charge refusée');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur refus PEC: ' . $e->getMessage());

            return back()->with('error', 'Erreur lors du refus de la PEC');
        }
    }

    /**
     * Annuler une prise en charge
     */
    public function annuler(Request $request, PriseEnCharge $pec)
    {
        $this->authorize('cancel', $pec);

        if (in_array($pec->statut, ['utilisee', 'annulee'])) {
            return back()->with('error', 'Cette PEC ne peut plus être annulée');
        }

        $request->validate([
            'motif_annulation' => 'required|string|max:500',
        ]);

        $pec->annuler($request->motif_annulation);

        return redirect()
            ->route('pec.show', $pec)
            ->with('success', 'Prise en charge annulée');
    }

    /**
     * Vérifier l'éligibilité d'un patient
     */
    public function verifierEligibilite(Request $request)
    {
        $request->validate([
            'patient_id' => 'required|exists:users,id',
            'montant' => 'required|numeric|min:0',
            'type_acte' => 'nullable|string',
        ]);

        $contrats = ContratAssurance::where('patient_id', $request->patient_id)
                                   ->actif()
                                   ->get();

        $eligibilites = [];

        foreach ($contrats as $contrat) {
            $couverture = $contrat->calculerCouverture(
                $request->montant,
                $request->type_acte
            );

            $eligibilites[] = [
                'contrat' => $contrat,
                'couverture' => $couverture,
                'eligible' => $couverture['montant_couvert'] > 0,
            ];
        }

        return response()->json([
            'success' => true,
            'eligibilites' => $eligibilites,
        ]);
    }

    /**
     * Télécharger une PEC en PDF
     */
    public function pdf(PriseEnCharge $pec)
    {
        $this->authorize('view', $pec);

        $pdf = PDF::loadView('pec.pdf', compact('pec'));

        return $pdf->download("pec-{$pec->numero_pec}.pdf");
    }

    /**
     * Exporter les PEC
     */
    public function export(Request $request)
    {
        $this->authorize('export', PriseEnCharge::class);

        return Excel::download(
            new PrisesEnChargeExport($request->all()),
            'prises-en-charge-' . date('Y-m-d') . '.xlsx'
        );
    }

    /**
     * Tableau de bord des PEC
     */
    public function dashboard(Request $request)
    {
        $this->authorize('viewDashboard', PriseEnCharge::class);

        $periode = $request->get('periode', 'mois');
        $stats = $this->getStatistiquesDashboard($periode);

        return view('pec.dashboard', compact('stats', 'periode'));
    }

    /**
     * API pour graphiques
     */
    public function statsApi(Request $request)
    {
        $this->authorize('viewDashboard', PriseEnCharge::class);

        $type = $request->get('type', 'evolution');
        $periode = $request->get('periode', 'mois');

        switch ($type) {
            case 'evolution':
                $data = $this->getEvolutionPEC($periode);
                break;
            case 'repartition':
                $data = $this->getRepartitionPEC();
                break;
            case 'taux':
                $data = $this->getTauxAcceptation($periode);
                break;
            default:
                $data = [];
        }

        return response()->json($data);
    }

    /**
     * Obtenir les statistiques
     */
    private function getStatistiques()
    {
        $query = PriseEnCharge::query();

        // Filtrer selon le rôle
        if (Auth::user()->hasRole('patient')) {
            $query->where('patient_id', Auth::id());
        } elseif (Auth::user()->hasRole('praticien')) {
            $query->where('praticien_id', Auth::id());
        } elseif (Auth::user()->hasRole('assureur')) {
            $query->whereHas('contrat', function($q) {
                $q->where('assurance_id', Auth::id());
            });
        }

        return [
            'total' => $query->count(),
            'en_attente' => (clone $query)->enAttente()->count(),
            'acceptees' => (clone $query)->acceptees()->count(),
            'refusees' => (clone $query)->refusees()->count(),
            'montant_total' => (clone $query)->sum('montant_demande'),
            'montant_accorde' => (clone $query)->acceptees()->sum('montant_accorde'),
            'taux_acceptation' => $query->count() > 0
                ? round(((clone $query)->acceptees()->count() / $query->count()) * 100, 2)
                : 0,
        ];
    }

    /**
     * Obtenir les statistiques du dashboard
     */
    private function getStatistiquesDashboard($periode)
    {
        $dateDebut = match($periode) {
            'jour' => now()->startOfDay(),
            'semaine' => now()->startOfWeek(),
            'mois' => now()->startOfMonth(),
            'annee' => now()->startOfYear(),
            default => now()->startOfMonth(),
        };

        $query = PriseEnCharge::where('date_demande', '>=', $dateDebut);

        return [
            'nouvelles' => (clone $query)->count(),
            'acceptees' => (clone $query)->acceptees()->count(),
            'refusees' => (clone $query)->refusees()->count(),
            'montant_demande' => (clone $query)->sum('montant_demande'),
            'montant_accorde' => (clone $query)->acceptees()->sum('montant_accorde'),
            'delai_moyen' => (clone $query)->whereNotNull('date_reponse')
                ->select(DB::raw('AVG(TIMESTAMPDIFF(HOUR, date_demande, date_reponse)) as delai'))
                ->value('delai'),
            'top_praticiens' => (clone $query)
                ->select('praticien_id', DB::raw('COUNT(*) as total'))
                ->with('praticien')
                ->groupBy('praticien_id')
                ->orderByDesc('total')
                ->limit(5)
                ->get(),
            'top_structures' => (clone $query)
                ->select('structure_id', DB::raw('COUNT(*) as total'))
                ->with('structure')
                ->groupBy('structure_id')
                ->orderByDesc('total')
                ->limit(5)
                ->get(),
        ];
    }

    /**
     * Obtenir l'évolution des PEC
     */
    private function getEvolutionPEC($periode)
    {
        $dateDebut = match($periode) {
            'semaine' => now()->subWeeks(4),
            'mois' => now()->subMonths(6),
            'annee' => now()->subYears(2),
            default => now()->subMonths(6),
        };

        $groupBy = match($periode) {
            'semaine' => 'DATE(date_demande)',
            'mois' => 'WEEK(date_demande)',
            'annee' => 'MONTH(date_demande)',
            default => 'WEEK(date_demande)',
        };

        return PriseEnCharge::where('date_demande', '>=', $dateDebut)
            ->select(DB::raw("{$groupBy} as periode"), 'statut', DB::raw('COUNT(*) as total'))
            ->groupBy('periode', 'statut')
            ->orderBy('periode')
            ->get()
            ->groupBy('periode');
    }

    /**
     * Obtenir la répartition des PEC
     */
    private function getRepartitionPEC()
    {
        return [
            'par_statut' => PriseEnCharge::select('statut', DB::raw('COUNT(*) as total'))
                ->groupBy('statut')
                ->pluck('total', 'statut'),
            'par_type' => PriseEnCharge::select('type_pec', DB::raw('COUNT(*) as total'))
                ->groupBy('type_pec')
                ->pluck('total', 'type_pec'),
        ];
    }

    /**
     * Obtenir le taux d'acceptation
     */
    private function getTauxAcceptation($periode)
    {
        $dateDebut = match($periode) {
            'jour' => now()->subDays(30),
            'semaine' => now()->subWeeks(12),
            'mois' => now()->subMonths(12),
            'annee' => now()->subYears(5),
            default => now()->subMonths(12),
        };

        return PriseEnCharge::where('date_demande', '>=', $dateDebut)
            ->select(
                DB::raw('DATE_FORMAT(date_demande, "%Y-%m") as mois'),
                DB::raw('COUNT(*) as total'),
                DB::raw('SUM(CASE WHEN statut = "acceptee" THEN 1 ELSE 0 END) as acceptees')
            )
            ->groupBy('mois')
            ->orderBy('mois')
            ->get()
            ->map(function ($item) {
                $item->taux = $item->total > 0
                    ? round(($item->acceptees / $item->total) * 100, 2)
                    : 0;
                return $item;
            });
    }
}
