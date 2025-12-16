<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrdonnanceRequest;
use App\Models\Ordonnance;
use App\Models\OrdonnanceLigne;
use App\Models\Consultation;
use App\Models\ProduitPharmaceutique;
use App\Models\User;
use App\Services\OrdonnanceService;
use App\Services\NotificationService;
use App\Exports\OrdonnancesExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use PDF;

class OrdonnanceController extends Controller
{
    protected $ordonnanceService;
    protected $notificationService;

    public function __construct(
        OrdonnanceService $ordonnanceService,
        NotificationService $notificationService
    ) {
        $this->ordonnanceService = $ordonnanceService;
        $this->notificationService = $notificationService;

        $this->middleware('auth');
        $this->middleware('permission:ordonnances.view')->only(['index', 'show']);
        $this->middleware('permission:ordonnances.create')->only(['create', 'store']);
        $this->middleware('permission:ordonnances.edit')->only(['edit', 'update']);
        $this->middleware('permission:ordonnances.delete')->only(['destroy']);
    }

    /**
     * Afficher la liste des ordonnances
     */
    public function index(Request $request)
    {
        $query = Ordonnance::with(['patient', 'praticien', 'structure', 'lignes']);

        // Filtres selon le rôle
        if (Auth::user()->hasRole('patient')) {
            $query->where('patient_id', Auth::id());
        } elseif (Auth::user()->hasRole('praticien')) {
            $query->where('praticien_id', Auth::id());
        } elseif (Auth::user()->hasRole('pharmacien')) {
            // Afficher toutes les ordonnances pour vérification
            $query->whereHas('lignes', function($q) {
                $q->where('dispensee', false);
            });
        }

        // Filtres additionnels
        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        if ($request->filled('patient')) {
            $query->where('patient_id', $request->patient);
        }

        if ($request->filled('praticien')) {
            $query->where('praticien_id', $request->praticien);
        }

        if ($request->filled('date_debut')) {
            $query->whereDate('date_ordonnance', '>=', $request->date_debut);
        }

        if ($request->filled('date_fin')) {
            $query->whereDate('date_ordonnance', '<=', $request->date_fin);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('numero_ordonnance', 'like', "%{$search}%")
                  ->orWhere('diagnostic', 'like', "%{$search}%")
                  ->orWhereHas('patient', function($q2) use ($search) {
                      $q2->where('nom', 'like', "%{$search}%")
                         ->orWhere('prenom', 'like', "%{$search}%");
                  });
            });
        }

        // Tri
        $sortField = $request->get('sort', 'date_ordonnance');
        $sortDirection = $request->get('direction', 'desc');
        $query->orderBy($sortField, $sortDirection);

        $ordonnances = $query->paginate(15)->withQueryString();

        // Statistiques
        $stats = [
            'total' => Ordonnance::count(),
            'actives' => Ordonnance::where('statut', 'active')->count(),
            'dispensees' => Ordonnance::where('statut', 'dispensee')->count(),
            'expirees' => Ordonnance::where('statut', 'expiree')->count(),
        ];

        if ($request->ajax()) {
            return response()->json([
                'html' => view('ordonnances.table', compact('ordonnances'))->render(),
                'pagination' => $ordonnances->links()->render(),
                'stats' => $stats
            ]);
        }

        return view('ordonnances.index', compact('ordonnances', 'stats'));
    }

    /**
     * Afficher le formulaire de création
     */
    public function create(Request $request)
    {
        $consultation = null;
        if ($request->has('consultation_id')) {
            $consultation = Consultation::findOrFail($request->consultation_id);
        }

        $patients = User::role('patient')->get();
        $produits = ProduitPharmaceutique::where('stock_disponible', '>', 0)
                                         ->orderBy('nom_commercial')
                                         ->get();

        return view('ordonnances.create', compact('consultation', 'patients', 'produits'));
    }

    /**
     * Enregistrer une nouvelle ordonnance
     */
    public function store(OrdonnanceRequest $request)
    {
        DB::beginTransaction();

        try {
            // Créer l'ordonnance
            $ordonnanceData = $request->validated();
            $ordonnanceData['praticien_id'] = Auth::id();
            $ordonnanceData['structure_id'] = Auth::user()->structure_id;
            $ordonnanceData['statut'] = 'active';

            $ordonnance = Ordonnance::create($ordonnanceData);

            // Ajouter les lignes de l'ordonnance
            foreach ($request->lignes as $ligneData) {
                $produit = ProduitPharmaceutique::find($ligneData['produit_id']);

                $ligne = new OrdonnanceLigne([
                    'produit_id' => $produit->id,
                    'dci' => $produit->dci,
                    'nom_commercial' => $produit->nom_commercial,
                    'dosage' => $produit->dosage,
                    'forme' => $produit->forme,
                    'quantite' => $ligneData['quantite'],
                    'posologie' => $ligneData['posologie'],
                    'duree_traitement' => $ligneData['duree_traitement'] ?? null,
                    'unite_duree' => $ligneData['unite_duree'] ?? 'jours',
                    'voie_administration' => $ligneData['voie_administration'] ?? null,
                    'instructions_speciales' => $ligneData['instructions_speciales'] ?? null,
                    'substitution_autorisee' => $ligneData['substitution_autorisee'] ?? false,
                    'urgence' => $ligneData['urgence'] ?? false,
                    'prix_unitaire' => $produit->prix_unitaire,
                ]);

                $ordonnance->lignes()->save($ligne);
            }

            // Vérifier les interactions médicamenteuses
            $interactions = $ordonnance->verifierInteractionsMedicamenteuses();
            if (!empty($interactions)) {
                session()->flash('warning', 'Attention : Des interactions médicamenteuses ont été détectées.');
            }

            // Vérifier les contre-indications
            $contrIndications = $ordonnance->verifierContrIndications();
            if (!empty($contrIndications)) {
                session()->flash('danger', 'Attention : Des contre-indications ont été détectées.');
            }

            // Notifier le patient
            $this->notificationService->notifier(
                $ordonnance->patient_id,
                'Nouvelle ordonnance',
                "Une nouvelle ordonnance a été créée pour vous par Dr. {$ordonnance->praticien->nom}",
                'ordonnance',
                $ordonnance->id
            );

            DB::commit();

            return redirect()
                ->route('ordonnances.show', $ordonnance)
                ->with('success', 'Ordonnance créée avec succès');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur création ordonnance: ' . $e->getMessage());

            return back()
                ->withInput()
                ->with('error', 'Erreur lors de la création de l\'ordonnance');
        }
    }

    /**
     * Afficher une ordonnance
     */
    public function show(Ordonnance $ordonnance)
    {
        $this->authorize('view', $ordonnance);

        $ordonnance->load([
            'patient.dossierMedical',
            'praticien',
            'structure',
            'consultation',
            'lignes.produit',
            'dispensations',
            'commandes'
        ]);

        // Vérifier la validité
        if ($ordonnance->estExpiree() && $ordonnance->statut === 'active') {
            $ordonnance->update(['statut' => 'expiree']);
        }

        return view('ordonnances.show', compact('ordonnance'));
    }

    /**
     * Afficher le formulaire d'édition
     */
    public function edit(Ordonnance $ordonnance)
    {
        $this->authorize('update', $ordonnance);

        if ($ordonnance->statut === 'dispensee') {
            return back()->with('error', 'Une ordonnance dispensée ne peut pas être modifiée');
        }

        $patients = User::role('patient')->get();
        $produits = ProduitPharmaceutique::where('stock_disponible', '>', 0)
                                         ->orderBy('nom_commercial')
                                         ->get();

        return view('ordonnances.edit', compact('ordonnance', 'patients', 'produits'));
    }

    /**
     * Mettre à jour une ordonnance
     */
    public function update(OrdonnanceRequest $request, Ordonnance $ordonnance)
    {
        $this->authorize('update', $ordonnance);

        if ($ordonnance->statut === 'dispensee') {
            return back()->with('error', 'Une ordonnance dispensée ne peut pas être modifiée');
        }

        DB::beginTransaction();

        try {
            $ordonnance->update($request->validated());

            // Mettre à jour les lignes
            $ordonnance->lignes()->delete();

            foreach ($request->lignes as $ligneData) {
                $produit = ProduitPharmaceutique::find($ligneData['produit_id']);

                $ordonnance->lignes()->create([
                    'produit_id' => $produit->id,
                    'dci' => $produit->dci,
                    'nom_commercial' => $produit->nom_commercial,
                    'dosage' => $produit->dosage,
                    'forme' => $produit->forme,
                    'quantite' => $ligneData['quantite'],
                    'posologie' => $ligneData['posologie'],
                    'duree_traitement' => $ligneData['duree_traitement'] ?? null,
                    'unite_duree' => $ligneData['unite_duree'] ?? 'jours',
                    'voie_administration' => $ligneData['voie_administration'] ?? null,
                    'instructions_speciales' => $ligneData['instructions_speciales'] ?? null,
                    'substitution_autorisee' => $ligneData['substitution_autorisee'] ?? false,
                    'urgence' => $ligneData['urgence'] ?? false,
                    'prix_unitaire' => $produit->prix_unitaire,
                ]);
            }

            // Régénérer le QR code
            $ordonnance->generateQrCode();

            DB::commit();

            return redirect()
                ->route('ordonnances.show', $ordonnance)
                ->with('success', 'Ordonnance mise à jour avec succès');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur mise à jour ordonnance: ' . $e->getMessage());

            return back()
                ->withInput()
                ->with('error', 'Erreur lors de la mise à jour de l\'ordonnance');
        }
    }

    /**
     * Supprimer une ordonnance
     */
    public function destroy(Ordonnance $ordonnance)
    {
        $this->authorize('delete', $ordonnance);

        if ($ordonnance->statut === 'dispensee') {
            return back()->with('error', 'Une ordonnance dispensée ne peut pas être supprimée');
        }

        $ordonnance->delete();

        return redirect()
            ->route('ordonnances.index')
            ->with('success', 'Ordonnance supprimée avec succès');
    }

    /**
     * Renouveler une ordonnance
     */
    public function renouveler(Ordonnance $ordonnance)
    {
        $this->authorize('renouveler', $ordonnance);

        if (!$ordonnance->peutEtreRenouvelee()) {
            return back()->with('error', 'Cette ordonnance ne peut pas être renouvelée');
        }

        $nouvelleOrdonnance = $ordonnance->renouveler();

        if ($nouvelleOrdonnance) {
            // Notifier le patient
            $this->notificationService->notifier(
                $nouvelleOrdonnance->patient_id,
                'Ordonnance renouvelée',
                "Votre ordonnance a été renouvelée avec succès",
                'ordonnance',
                $nouvelleOrdonnance->id
            );

            return redirect()
                ->route('ordonnances.show', $nouvelleOrdonnance)
                ->with('success', 'Ordonnance renouvelée avec succès');
        }

        return back()->with('error', 'Erreur lors du renouvellement de l\'ordonnance');
    }

    /**
     * Dispenser une ordonnance
     */
    public function dispenser(Request $request, Ordonnance $ordonnance)
    {
        $this->authorize('dispenser', $ordonnance);

        $request->validate([
            'lignes' => 'required|array',
            'lignes.*.id' => 'required|exists:ordonnance_lignes,id',
            'lignes.*.quantite_dispensee' => 'required|integer|min:1',
        ]);

        DB::beginTransaction();

        try {
            $toutesDispensees = true;

            foreach ($request->lignes as $ligneData) {
                $ligne = OrdonnanceLigne::findOrFail($ligneData['id']);

                if ($ligne->ordonnance_id !== $ordonnance->id) {
                    throw new \Exception('Ligne d\'ordonnance invalide');
                }

                $ligne->marquerDispensee(
                    $ligneData['quantite_dispensee'],
                    Auth::user()->structure_id
                );

                if (!$ligne->estCompletementDispensee()) {
                    $toutesDispensees = false;
                }
            }

            if ($toutesDispensees) {
                $ordonnance->marquerDispensee(Auth::user()->structure_id);
            } else {
                $ordonnance->marquerPartiellemmentDispensee(
                    array_column($request->lignes, 'id')
                );
            }

            // Notifier le patient
            $this->notificationService->notifier(
                $ordonnance->patient_id,
                'Ordonnance dispensée',
                "Votre ordonnance {$ordonnance->numero_ordonnance} a été dispensée",
                'ordonnance',
                $ordonnance->id
            );

            DB::commit();

            return redirect()
                ->route('ordonnances.show', $ordonnance)
                ->with('success', 'Ordonnance dispensée avec succès');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur dispensation ordonnance: ' . $e->getMessage());

            return back()->with('error', 'Erreur lors de la dispensation de l\'ordonnance');
        }
    }

    /**
     * Vérifier une ordonnance via QR code
     */
    public function verify($numero)
    {
        $ordonnance = Ordonnance::where('numero_ordonnance', $numero)
                                ->with(['patient', 'praticien', 'lignes'])
                                ->firstOrFail();

        return view('ordonnances.verify', compact('ordonnance'));
    }

    /**
     * Télécharger une ordonnance en PDF
     */
    public function pdf(Ordonnance $ordonnance)
    {
        $this->authorize('view', $ordonnance);

        $pdf = PDF::loadView('ordonnances.pdf', compact('ordonnance'));

        return $pdf->download("ordonnance-{$ordonnance->numero_ordonnance}.pdf");
    }

    /**
     * Imprimer une ordonnance
     */
    public function print(Ordonnance $ordonnance)
    {
        $this->authorize('view', $ordonnance);

        return view('ordonnances.print', compact('ordonnance'));
    }

    /**
     * Exporter les ordonnances
     */
    public function export(Request $request)
    {
        $this->authorize('export', Ordonnance::class);

        return Excel::download(
            new OrdonnancesExport($request->all()),
            'ordonnances-' . date('Y-m-d') . '.xlsx'
        );
    }

    /**
     * Dupliquer une ordonnance
     */
    public function duplicate(Ordonnance $ordonnance)
    {
        $this->authorize('create', Ordonnance::class);

        $nouvelleOrdonnance = $ordonnance->replicate();
        $nouvelleOrdonnance->numero_ordonnance = Ordonnance::generateNumeroOrdonnance();
        $nouvelleOrdonnance->date_ordonnance = now();
        $nouvelleOrdonnance->statut = 'active';
        $nouvelleOrdonnance->save();

        foreach ($ordonnance->lignes as $ligne) {
            $nouvelleLigne = $ligne->replicate();
            $nouvelleLigne->ordonnance_id = $nouvelleOrdonnance->id;
            $nouvelleLigne->dispensee = false;
            $nouvelleLigne->save();
        }

        return redirect()
            ->route('ordonnances.edit', $nouvelleOrdonnance)
            ->with('info', 'Ordonnance dupliquée. Veuillez vérifier et modifier si nécessaire.');
    }
}
