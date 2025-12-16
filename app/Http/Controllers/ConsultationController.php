<?php

namespace App\Http\Controllers;

use App\Http\Requests\ConsultationRequest;
use App\Models\Consultation;
use App\Models\RendezVous;
use App\Models\User;
use App\Models\StructureMedicale;
use App\Models\DossierMedical;
use App\Models\Ordonnance;
use App\Models\ActeMedical;
use App\Models\Devis;
use App\Models\Facture;
use App\Models\Notification;
use App\Services\ConsultationService;
use App\Services\FacturationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;

class ConsultationController extends Controller
{
    protected $consultationService;
    protected $facturationService;

    public function __construct(
        ConsultationService $consultationService,
        FacturationService $facturationService
    ) {
        $this->consultationService = $consultationService;
        $this->facturationService = $facturationService;
    }

    /**
     * Afficher la liste des consultations
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $query = Consultation::with(['patient', 'professionnel', 'structure']);

        // Filtrer selon le rôle
        if ($user->hasRole('patient')) {
            $query->where('patient_id', $user->id);
        } elseif ($user->isProfessional()) {
            $query->where('professionnel_id', $user->id);
        } elseif ($user->hasRole('gestionnaire-structure')) {
            $structures = $user->structures()->pluck('id');
            $query->whereIn('structure_id', $structures);
        }

        // Filtres
        if ($request->filled('date_debut')) {
            $query->where('date_consultation', '>=', $request->date_debut);
        }
        if ($request->filled('date_fin')) {
            $query->where('date_consultation', '<=', $request->date_fin);
        }
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        if ($request->filled('modalite')) {
            $query->where('modalite', $request->modalite);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('numero_consultation', 'like', "%{$search}%")
                    ->orWhere('motif_consultation', 'like', "%{$search}%")
                    ->orWhere('diagnostic_principal', 'like', "%{$search}%")
                    ->orWhereHas('patient', function ($q) use ($search) {
                        $q->where('nom', 'like', "%{$search}%")
                            ->orWhere('prenom', 'like', "%{$search}%");
                    });
            });
        }

        $consultations = $query->orderBy('date_consultation', 'desc')
            ->paginate(20);

        return view('consultations.index', compact('consultations'));
    }

    /**
     * Afficher le formulaire de création
     */
    public function create(Request $request)
    {
        Gate::authorize('create', Consultation::class);

        $rendezVousId = $request->query('rendez_vous');
        $patientId = $request->query('patient');

        $rendezVous = null;
        $patient = null;
        $dossierMedical = null;

        if ($rendezVousId) {
            $rendezVous = RendezVous::with(['patient.dossierMedical', 'structure'])
                ->findOrFail($rendezVousId);
            $patient = $rendezVous->patient;
            $dossierMedical = $patient->dossierMedical;
        } elseif ($patientId) {
            $patient = User::with('dossierMedical')->findOrFail($patientId);
            $dossierMedical = $patient->dossierMedical;
        }

        $user = $request->user();
        $structures = $user->isProfessional()
            ? $user->structures
            : StructureMedicale::active()->get();

        $actesMedicaux = ActeMedical::where('actif', true)
            ->orderBy('categorie')
            ->orderBy('libelle')
            ->get()
            ->groupBy('categorie');

        return view('consultations.create', compact(
            'rendezVous',
            'patient',
            'dossierMedical',
            'structures',
            'actesMedicaux'
        ));
    }

    /**
     * Enregistrer une nouvelle consultation
     */
    public function store(ConsultationRequest $request)
    {
        Gate::authorize('create', Consultation::class);

        DB::beginTransaction();
        try {
            // Créer la consultation
            $data = $request->validated();
            $data['professionnel_id'] = $request->user()->id;
            $data['numero_consultation'] = Consultation::generateNumero();

            // Gérer les champs JSON
            $data['signes_vitaux'] = $request->signes_vitaux;
            $data['symptomes_declares'] = $request->symptomes_declares;
            $data['actes_realises'] = $request->actes_realises;
            $data['examens_prescrits'] = $request->examens_prescrits;

            $consultation = Consultation::create($data);

            // Mettre à jour le rendez-vous si nécessaire
            if ($request->rendez_vous_id) {
                RendezVous::find($request->rendez_vous_id)
                    ->update(['statut' => 'termine']);
            }

            // Créer le devis si des actes sont réalisés
            if (!empty($request->actes_realises)) {
                $this->createDevis($consultation, $request->actes_realises);
            }

            // Créer l'ordonnance si des médicaments sont prescrits
            if ($request->has('prescriptions') && !empty($request->prescriptions)) {
                $this->createOrdonnance($consultation, $request->prescriptions);
            }

            // Mettre à jour le dossier médical
            $this->updateDossierMedical($consultation);

            // Envoyer une notification au patient
            Notification::create([
                'user_id' => $consultation->patient_id,
                'titre' => 'Nouvelle consultation',
                'message' => "Votre consultation du " . $consultation->date_consultation->format('d/m/Y') . " a été enregistrée.",
                'type' => 'consultation',
                'entite_type' => 'consultation',
                'entite_id' => $consultation->id,
            ]);

            DB::commit();

            return redirect()->route('consultations.show', $consultation)
                ->with('success', 'Consultation enregistrée avec succès.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Erreur lors de l\'enregistrement: ' . $e->getMessage());
        }
    }

    /**
     * Afficher une consultation
     */
    public function show(Consultation $consultation)
    {
        Gate::authorize('view', $consultation);

        $consultation->load([
            'patient.dossierMedical',
            'professionnel',
            'structure',
            'rendezVous',
            'ordonnances.lignes.produit',
            'facture.lignes',
            'devis.lignes',
            'evaluation'
        ]);

        return view('consultations.show', compact('consultation'));
    }

    /**
     * Afficher le formulaire d'édition
     */
    public function edit(Consultation $consultation)
    {
        Gate::authorize('update', $consultation);

        if ($consultation->valide) {
            return back()->with('error', 'Cette consultation a été validée et ne peut plus être modifiée.');
        }

        $consultation->load(['patient.dossierMedical', 'professionnel', 'structure']);

        $user = request()->user();
        $structures = $user->isProfessional()
            ? $user->structures
            : StructureMedicale::active()->get();

        $actesMedicaux = ActeMedical::where('actif', true)
            ->orderBy('categorie')
            ->orderBy('libelle')
            ->get()
            ->groupBy('categorie');

        return view('consultations.edit', compact('consultation', 'structures', 'actesMedicaux'));
    }

    /**
     * Mettre à jour une consultation
     */
    public function update(ConsultationRequest $request, Consultation $consultation)
    {
        Gate::authorize('update', $consultation);

        if ($consultation->valide) {
            return back()->with('error', 'Cette consultation a été validée et ne peut plus être modifiée.');
        }

        DB::beginTransaction();
        try {
            $data = $request->validated();

            // Gérer les champs JSON
            $data['signes_vitaux'] = $request->signes_vitaux;
            $data['symptomes_declares'] = $request->symptomes_declares;
            $data['actes_realises'] = $request->actes_realises;
            $data['examens_prescrits'] = $request->examens_prescrits;

            $consultation->update($data);

            // Mettre à jour le devis si nécessaire
            if (!empty($request->actes_realises)) {
                $this->updateDevis($consultation, $request->actes_realises);
            }

            // Mettre à jour l'ordonnance si nécessaire
            if ($request->has('prescriptions') && !empty($request->prescriptions)) {
                $this->updateOrdonnance($consultation, $request->prescriptions);
            }

            DB::commit();

            return redirect()->route('consultations.show', $consultation)
                ->with('success', 'Consultation mise à jour avec succès.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Erreur lors de la mise à jour: ' . $e->getMessage());
        }
    }

    /**
     * Valider une consultation
     */
    public function validate(Request $request, Consultation $consultation)
    {
        Gate::authorize('validate', $consultation);

        if ($consultation->valide) {
            return back()->with('info', 'Cette consultation est déjà validée.');
        }

        $consultation->validate(request()->user());

        // Générer le compte rendu PDF
        $consultation->generateReport();

        // Créer la facture si elle n'existe pas
        if (!$consultation->facture && $consultation->devis) {
            $this->facturationService->createFactureFromDevis($consultation->devis);
        }

        return back()->with('success', 'Consultation validée avec succès.');
    }

    /**
     * Télécharger le compte rendu
     */
    public function downloadReport(Consultation $consultation)
    {
        Gate::authorize('view', $consultation);

        if (!$consultation->compte_rendu_pdf) {
            $consultation->generateReport();
        }

        $path = storage_path("app/public/consultations/{$consultation->compte_rendu_pdf}");

        if (!file_exists($path)) {
            return back()->with('error', 'Le compte rendu n\'est pas disponible.');
        }

        return response()->download($path);
    }

    /**
     * Démarrer une téléconsultation
     */
    public function startTeleconsultation(Consultation $consultation)
    {
        Gate::authorize('update', $consultation);

        if ($consultation->modalite !== 'teleconsultation') {
            return back()->with('error', 'Cette consultation n\'est pas une téléconsultation.');
        }

        // Créer ou récupérer la session de téléconsultation
        $session = $this->consultationService->createTeleconsultationSession($consultation);

        return view('consultations.teleconsultation', compact('consultation', 'session'));
    }

    /**
     * Créer un certificat médical
     */
    public function createCertificate(Request $request, Consultation $consultation)
    {
        Gate::authorize('update', $consultation);

        $request->validate([
            'type_certificat' => 'required|in:aptitude,inaptitude,medical,sport,travail',
            'contenu' => 'required|string',
            'duree_validite' => 'nullable|integer|min:1',
        ]);

        $filename = $consultation->createMedicalCertificate($request->all());

        return response()->download(storage_path("app/public/certificats/{$filename}"));
    }

    /**
     * Créer un arrêt de travail
     */
    public function createSickLeave(Request $request, Consultation $consultation)
    {
        Gate::authorize('update', $consultation);

        $request->validate([
            'duree' => 'required|integer|min:1|max:365',
            'motif' => 'required|string|max:500',
        ]);

        $filename = $consultation->createSickLeave($request->duree, $request->motif);

        return response()->download(storage_path("app/public/arrets/{$filename}"));
    }

    /**
     * Orienter vers un spécialiste
     */
    public function referToSpecialist(Request $request, Consultation $consultation)
    {
        Gate::authorize('update', $consultation);

        $request->validate([
            'specialite' => 'required|string',
            'motif' => 'required|string|max:500',
            'urgence' => 'boolean',
        ]);

        $consultation->referToSpecialist($request->specialite, $request->motif);

        // Créer une notification pour le patient
        Notification::create([
            'user_id' => $consultation->patient_id,
            'titre' => 'Orientation vers un spécialiste',
            'message' => "Vous avez été orienté vers un {$request->specialite}. Motif: {$request->motif}",
            'type' => 'consultation',
            'priorite' => $request->urgence ? 'haute' : 'normale',
            'entite_type' => 'consultation',
            'entite_id' => $consultation->id,
        ]);

        return back()->with('success', 'Patient orienté vers un spécialiste.');
    }

    /**
     * Planifier un suivi
     */
    public function scheduleFollowUp(Request $request, Consultation $consultation)
    {
        Gate::authorize('update', $consultation);

        $request->validate([
            'date' => 'required|date|after:today',
            'heure' => 'required|date_format:H:i',
            'motif' => 'required|string|max:500',
        ]);

        $dateTime = \Carbon\Carbon::parse($request->date . ' ' . $request->heure);

        $rdv = $consultation->scheduleFollowUp($dateTime, $request->motif);

        return redirect()->route('rendez-vous.show', $rdv)
            ->with('success', 'Rendez-vous de suivi planifié.');
    }

    /**
     * Statistiques des consultations
     */
    public function statistics(Request $request)
    {
        Gate::authorize('viewAny', Consultation::class);

        $user = \Auth::user();
        $query = Consultation::query();

        // Filtrer selon le rôle
        if ($user->isProfessional()) {
            $query->where('professionnel_id', $user->id);
        } elseif ($user->hasRole('gestionnaire-structure')) {
            $structures = $user->structures()->pluck('id');
            $query->whereIn('structure_id', $structures);
        }

        // Période
        $startDate = $request->date_debut ?? now()->startOfMonth();
        $endDate = $request->date_fin ?? now()->endOfMonth();

        $query->whereBetween('date_consultation', [$startDate, $endDate]);

        // Statistiques générales
        $stats = [
            'total' => $query->count(),
            'par_type' => $query->groupBy('type')
                ->selectRaw('type, COUNT(*) as count')
                ->pluck('count', 'type'),
            'par_modalite' => $query->groupBy('modalite')
                ->selectRaw('modalite, COUNT(*) as count')
                ->pluck('count', 'modalite'),
            'duree_moyenne' => $query->avg('duree_effective'),
        ];

        // Top diagnostics
        $topDiagnostics = $query->groupBy('diagnostic_principal')
            ->selectRaw('diagnostic_principal, COUNT(*) as count')
            ->orderBy('count', 'desc')
            ->limit(10)
            ->get();

        // Évolution quotidienne
        $dailyEvolution = $query->groupBy('date')
            ->selectRaw('DATE(date_consultation) as date, COUNT(*) as count')
            ->orderBy('date')
            ->get();

        return view('consultations.statistics', compact(
            'stats',
            'topDiagnostics',
            'dailyEvolution',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Créer un devis pour la consultation
     */
    private function createDevis(Consultation $consultation, array $actes): void
    {
        $devis = Devis::create([
            'patient_id' => $consultation->patient_id,
            'praticien_id' => $consultation->professionnel_id,
            'structure_id' => $consultation->structure_id,
            'consultation_id' => $consultation->id,
            'date_emission' => now(),
            'date_validite' => now()->addDays(30),
            'statut' => 'emis',
        ]);

        $montantTotal = 0;
        foreach ($actes as $acte) {
            $acteMedical = ActeMedical::find($acte['id']);
            if ($acteMedical) {
                $montant = $acteMedical->tarif_base * ($acte['quantite'] ?? 1);
                $devis->lignes()->create([
                    'type' => 'acte',
                    'element_id' => $acteMedical->id,
                    'code' => $acteMedical->code_acte,
                    'libelle' => $acteMedical->libelle,
                    'quantite' => $acte['quantite'] ?? 1,
                    'prix_unitaire' => $acteMedical->tarif_base,
                    'montant_ht' => $montant,
                    'montant_ttc' => $montant,
                    'montant_final' => $montant,
                ]);
                $montantTotal += $montant;
            }
        }

        $devis->update([
            'montant_ht' => $montantTotal,
            'montant_ttc' => $montantTotal,
            'montant_final' => $montantTotal,
            'reste_a_charge' => $montantTotal,
        ]);
    }

    /**
     * Mettre à jour le devis de la consultation
     */
    private function updateDevis(Consultation $consultation, array $actes): void
    {
        if (!$consultation->devis) {
            $this->createDevis($consultation, $actes);
            return;
        }

        // Supprimer les anciennes lignes
        $consultation->devis->lignes()->delete();

        // Ajouter les nouvelles lignes
        $montantTotal = 0;
        foreach ($actes as $acte) {
            $acteMedical = ActeMedical::find($acte['id']);
            if ($acteMedical) {
                $montant = $acteMedical->tarif_base * ($acte['quantite'] ?? 1);
                $consultation->devis->lignes()->create([
                    'type' => 'acte',
                    'element_id' => $acteMedical->id,
                    'code' => $acteMedical->code_acte,
                    'libelle' => $acteMedical->libelle,
                    'quantite' => $acte['quantite'] ?? 1,
                    'prix_unitaire' => $acteMedical->tarif_base,
                    'montant_ht' => $montant,
                    'montant_ttc' => $montant,
                    'montant_final' => $montant,
                ]);
                $montantTotal += $montant;
            }
        }

        $consultation->devis->update([
            'montant_ht' => $montantTotal,
            'montant_ttc' => $montantTotal,
            'montant_final' => $montantTotal,
            'reste_a_charge' => $montantTotal,
        ]);
    }

    /**
     * Créer une ordonnance pour la consultation
     */
    private function createOrdonnance(Consultation $consultation, array $prescriptions): void
    {
        $ordonnance = $consultation->createPrescription($prescriptions);
    }

    /**
     * Mettre à jour l'ordonnance de la consultation
     */
    private function updateOrdonnance(Consultation $consultation, array $prescriptions): void
    {
        $ordonnance = $consultation->ordonnances()->first();

        if (!$ordonnance) {
            $this->createOrdonnance($consultation, $prescriptions);
            return;
        }

        // Supprimer les anciennes lignes
        $ordonnance->lignes()->delete();

        // Ajouter les nouvelles lignes
        foreach ($prescriptions as $prescription) {
            $ordonnance->lignes()->create($prescription);
        }
    }

    /**
     * Mettre à jour le dossier médical
     */
    private function updateDossierMedical(Consultation $consultation): void
    {
        $dossier = DossierMedical::firstOrCreate(
            ['patient_id' => $consultation->patient_id],
            ['numero_dossier' => 'DME-' . now()->format('Y') . '-' . str_pad($consultation->patient_id, 6, '0', STR_PAD_LEFT)]
        );

        // Mettre à jour les constantes si fournies
        if ($consultation->signes_vitaux) {
            $vitaux = $consultation->signes_vitaux;
            $dossier->update([
                'tension_habituelle_sys' => $vitaux['ta_sys'] ?? $dossier->tension_habituelle_sys,
                'tension_habituelle_dia' => $vitaux['ta_dia'] ?? $dossier->tension_habituelle_dia,
                'poids_habituel' => $vitaux['poids'] ?? $dossier->poids_habituel,
                'taille_cm' => $vitaux['taille'] ?? $dossier->taille_cm,
            ]);

            // Calculer l'IMC
            if (isset($vitaux['poids']) && isset($vitaux['taille'])) {
                $imc = $vitaux['poids'] / pow($vitaux['taille'] / 100, 2);
                $dossier->update(['imc' => round($imc, 2)]);
            }
        }

        // Mettre à jour la dernière consultation
        $dossier->update([
            'derniere_consultation' => $consultation->date_consultation,
            'nombre_consultations' => $dossier->nombre_consultations + 1,
        ]);
    }
}
