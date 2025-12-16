<?php

namespace App\Services;

use App\Models\PriseEnCharge;
use App\Models\ContratAssurance;
use App\Models\Facture;
use App\Models\Devis;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Exception;
use App\Services\NotificationService;

class AssuranceService
{
    protected $apiUrl;
    protected $apiKey;
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->apiUrl = config('services.assurance.api_url');
        $this->apiKey = config('services.assurance.api_key');
        $this->notificationService = $notificationService;
    }

    /**
     * Vérifier l'éligibilité d'un patient pour une PEC
     */
    public function verifierEligibilite(int $patientId, float $montant, string $typeActe = null): array
    {
        $contrats = ContratAssurance::where('patient_id', $patientId)
                                   ->actif()
                                   ->get();

        if ($contrats->isEmpty()) {
            return [
                'eligible' => false,
                'raison' => 'Aucun contrat d\'assurance actif trouvé',
                'contrats' => []
            ];
        }

        $eligibilites = [];
        $meilleurContrat = null;
        $meilleurMontantCouvert = 0;

        foreach ($contrats as $contrat) {
            $couverture = $contrat->calculerCouverture($montant, $typeActe);

            $eligibilites[] = [
                'contrat_id' => $contrat->id,
                'numero_contrat' => $contrat->numero_contrat,
                'assureur' => $contrat->assurance->name,
                'taux_couverture' => $contrat->taux_couverture,
                'montant_couvert' => $couverture['montant_couvert'],
                'reste_a_charge' => $couverture['reste_a_charge'],
                'plafond_restant' => $couverture['plafond_restant'],
                'eligible' => $couverture['montant_couvert'] > 0,
                'raison' => $couverture['raison'] ?? null
            ];

            if ($couverture['montant_couvert'] > $meilleurMontantCouvert) {
                $meilleurMontantCouvert = $couverture['montant_couvert'];
                $meilleurContrat = $contrat;
            }
        }

        return [
            'eligible' => $meilleurMontantCouvert > 0,
            'meilleur_contrat' => $meilleurContrat,
            'montant_couvert_max' => $meilleurMontantCouvert,
            'reste_a_charge_min' => $montant - $meilleurMontantCouvert,
            'contrats' => $eligibilites
        ];
    }

    /**
     * Créer une demande de PEC
     */
    public function creerDemandePEC(array $data): PriseEnCharge
    {
        DB::beginTransaction();

        try {
            // Vérifier le contrat
            $contrat = ContratAssurance::findOrFail($data['contrat_id']);

            if (!$contrat->estValide()) {
                throw new Exception('Le contrat d\'assurance n\'est pas valide');
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
                'praticien_id' => $data['praticien_id'],
                'structure_id' => $data['structure_id'] ?? null,
                'type_pec' => $data['type_pec'],
                'montant_demande' => $data['montant_demande'],
                'montant_accorde' => null, // Sera défini après validation
                'taux_pec' => $couverture['taux_applique'],
                'motif' => $data['motif'],
                'statut' => 'en_attente',
                'validite_jours' => $data['validite_jours'] ?? 30,
                'justificatifs' => $data['justificatifs'] ?? [],
            ]);

            // Si API disponible, envoyer la demande
            if ($this->apiUrl) {
                $this->envoyerDemandePEC($pec);
            }

            // Notifier l'assureur
            $this->notificationService->notifier(
                $contrat->assurance_id,
                'Nouvelle demande de PEC',
                "Nouvelle demande de prise en charge #{$pec->numero_pec}",
                'pec',
                $pec->id
            );

            DB::commit();
            return $pec;

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Erreur création PEC: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Envoyer une demande de PEC via API
     */
    public function envoyerDemandePEC(PriseEnCharge $pec): array
    {
        if (!$this->apiUrl) {
            return [
                'success' => false,
                'message' => 'API assurance non configurée'
            ];
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Accept' => 'application/json',
            ])->post($this->apiUrl . '/pec/demande', [
                'numero_pec' => $pec->numero_pec,
                'numero_contrat' => $pec->contrat->numero_contrat,
                'patient' => [
                    'nom' => $pec->patient->nom,
                    'prenom' => $pec->patient->prenom,
                    'date_naissance' => $pec->patient->date_naissance,
                    'numero_adherent' => $pec->contrat->metadata['numero_adherent'] ?? null,
                ],
                'praticien' => [
                    'nom' => $pec->praticien->nom,
                    'prenom' => $pec->praticien->prenom,
                    'specialite' => $pec->praticien->specialite,
                    'numero_ordre' => $pec->praticien->numero_ordre ?? null,
                ],
                'prestation' => [
                    'type' => $pec->type_pec,
                    'motif' => $pec->motif,
                    'montant' => $pec->montant_demande,
                    'date_prestation' => now()->format('Y-m-d'),
                ],
                'justificatifs' => $pec->justificatifs,
            ]);

            if ($response->successful()) {
                $data = $response->json();

                // Mettre à jour la PEC avec la réponse
                $pec->update([
                    'statut' => $data['statut'] ?? 'en_attente',
                    'montant_accorde' => $data['montant_accorde'] ?? null,
                    'commentaire_assurance' => $data['commentaire'] ?? null,
                    'date_reponse' => $data['statut'] !== 'en_attente' ? now() : null,
                    'metadata' => array_merge($pec->metadata ?? [], [
                        'api_response' => $data,
                        'api_reference' => $data['reference'] ?? null,
                    ]),
                ]);

                return [
                    'success' => true,
                    'statut' => $data['statut'],
                    'montant_accorde' => $data['montant_accorde'] ?? null,
                    'commentaire' => $data['commentaire'] ?? null,
                ];
            }

            Log::error('Erreur API assurance: ' . $response->body());

            return [
                'success' => false,
                'message' => 'Erreur lors de l\'envoi à l\'assurance'
            ];

        } catch (Exception $e) {
            Log::error('Exception API assurance: ' . $e->getMessage());

            return [
                'success' => false,
                'message' => 'Erreur technique lors de l\'envoi'
            ];
        }
    }

    /**
     * Valider une PEC
     */
    public function validerPEC(PriseEnCharge $pec, bool $accepter, float $montantAccorde = null, string $commentaire = null): bool
    {
        DB::beginTransaction();

        try {
            if ($accepter) {
                $pec->accepter($montantAccorde ?? $pec->montant_demande, $commentaire);

                // Mettre à jour le devis ou la facture
                $this->mettreAJourDocumentLie($pec, true);

                // Consommer le plafond du contrat
                if ($pec->contrat) {
                    $pec->contrat->consommerPlafond($pec->montant_accorde);
                }
            } else {
                $pec->refuser($commentaire ?? 'Demande refusée par l\'assurance');

                // Mettre à jour le devis ou la facture
                $this->mettreAJourDocumentLie($pec, false);
            }

            DB::commit();
            return true;

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Erreur validation PEC: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Mettre à jour le document lié (devis ou facture)
     */
    protected function mettreAJourDocumentLie(PriseEnCharge $pec, bool $acceptee): void
    {
        if ($pec->devis) {
            $pec->devis->update([
                'pec_id' => $acceptee ? $pec->id : null,
                'montant_pec' => $acceptee ? $pec->montant_accorde : 0,
                'reste_a_charge' => $pec->devis->montant_total - ($acceptee ? $pec->montant_accorde : 0),
            ]);
        }

        if ($pec->facture) {
            $pec->facture->update([
                'pec_id' => $acceptee ? $pec->id : null,
                'montant_pec' => $acceptee ? $pec->montant_accorde : 0,
                'reste_a_charge' => $pec->facture->montant_total - ($acceptee ? $pec->montant_accorde : 0),
            ]);
        }
    }

    /**
     * Vérifier le statut d'une PEC via API
     */
    public function verifierStatutPEC(PriseEnCharge $pec): array
    {
        if (!$this->apiUrl || $pec->statut !== 'en_attente') {
            return ['success' => false];
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Accept' => 'application/json',
            ])->get($this->apiUrl . '/pec/statut/' . $pec->numero_pec);

            if ($response->successful()) {
                $data = $response->json();

                // Mettre à jour si le statut a changé
                if ($data['statut'] !== $pec->statut) {
                    $pec->update([
                        'statut' => $data['statut'],
                        'montant_accorde' => $data['montant_accorde'] ?? $pec->montant_accorde,
                        'commentaire_assurance' => $data['commentaire'] ?? $pec->commentaire_assurance,
                        'date_reponse' => $data['date_reponse'] ?? now(),
                    ]);

                    // Notifier le patient
                    $this->notificationService->notifier(
                        $pec->patient_id,
                        'Mise à jour PEC',
                        "Le statut de votre PEC #{$pec->numero_pec} a été mis à jour",
                        'pec',
                        $pec->id
                    );
                }

                return [
                    'success' => true,
                    'statut' => $data['statut'],
                    'data' => $data
                ];
            }

            return ['success' => false];

        } catch (Exception $e) {
            Log::error('Erreur vérification statut PEC: ' . $e->getMessage());
            return ['success' => false];
        }
    }

    /**
     * Traiter les remboursements
     */
    public function traiterRemboursement(Facture $facture): bool
    {
        if (!$facture->pec || $facture->pec->statut !== 'acceptee') {
            return false;
        }

        DB::beginTransaction();

        try {
            $pec = $facture->pec;

            // Marquer la PEC comme utilisée
            $pec->marquerUtilisee();

            // Créer une demande de remboursement si nécessaire
            if ($this->apiUrl) {
                $this->envoyerDemandeRemboursement($facture, $pec);
            }

            // Mettre à jour le statut de remboursement de la facture
            $facture->update([
                'statut_remboursement' => 'en_cours',
                'date_demande_remboursement' => now(),
            ]);

            DB::commit();
            return true;

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Erreur traitement remboursement: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Envoyer une demande de remboursement
     */
    protected function envoyerDemandeRemboursement(Facture $facture, PriseEnCharge $pec): void
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Accept' => 'application/json',
            ])->post($this->apiUrl . '/remboursement/demande', [
                'numero_pec' => $pec->numero_pec,
                'numero_facture' => $facture->numero_facture,
                'montant_facture' => $facture->montant_total,
                'montant_remboursement' => $pec->montant_accorde,
                'date_soins' => $facture->date_facture,
                'praticien' => [
                    'nom' => $facture->praticien->nom,
                    'rib' => $facture->praticien->rib ?? null,
                ],
                'justificatifs' => [
                    'facture' => $facture->pdf_url ?? null,
                    'pec' => $pec->pdf_url ?? null,
                ],
            ]);

            if ($response->successful()) {
                $data = $response->json();

                $facture->update([
                    'numero_remboursement' => $data['numero_remboursement'] ?? null,
                    'metadata' => array_merge($facture->metadata ?? [], [
                        'remboursement_api' => $data,
                    ]),
                ]);
            }

        } catch (Exception $e) {
            Log::error('Erreur envoi demande remboursement: ' . $e->getMessage());
        }
    }

    /**
     * Détecter les fraudes potentielles
     */
    public function detecterFraudes(int $patientId, string $typeActe = null): array
    {
        $alertes = [];
        $periode = now()->subDays(30);

        // Vérifier les actes répétitifs
        $actesRepetitifs = PriseEnCharge::where('patient_id', $patientId)
            ->where('date_demande', '>=', $periode)
            ->where('type_pec', $typeActe)
            ->count();

        if ($actesRepetitifs > 3) {
            $alertes[] = [
                'type' => 'actes_repetitifs',
                'severite' => 'moyenne',
                'detail' => "Plus de 3 demandes pour le même type d'acte en 30 jours",
                'nombre' => $actesRepetitifs
            ];
        }

        // Vérifier les montants anormaux
        $montantMoyen = PriseEnCharge::where('type_pec', $typeActe)
            ->where('statut', 'acceptee')
            ->avg('montant_demande');

        $dernierePEC = PriseEnCharge::where('patient_id', $patientId)
            ->where('type_pec', $typeActe)
            ->latest()
            ->first();

        if ($dernierePEC && $montantMoyen) {
            $ecart = abs($dernierePEC->montant_demande - $montantMoyen) / $montantMoyen;

            if ($ecart > 2) { // Plus de 200% d'écart
                $alertes[] = [
                    'type' => 'montant_anormal',
                    'severite' => 'haute',
                    'detail' => "Montant demandé très supérieur à la moyenne",
                    'montant_demande' => $dernierePEC->montant_demande,
                    'montant_moyen' => $montantMoyen
                ];
            }
        }

        // Vérifier les multi-assurances
        $contratsActifs = ContratAssurance::where('patient_id', $patientId)
            ->actif()
            ->count();

        if ($contratsActifs > 2) {
            $alertes[] = [
                'type' => 'multi_assurance',
                'severite' => 'faible',
                'detail' => "Patient avec plusieurs contrats d'assurance actifs",
                'nombre_contrats' => $contratsActifs
            ];
        }

        return $alertes;
    }

    /**
     * Calculer les statistiques d'assurance
     */
    public function getStatistiques(array $filtres = []): array
    {
        $query = PriseEnCharge::query();

        // Appliquer les filtres
        if (isset($filtres['assurance_id'])) {
            $query->whereHas('contrat', function($q) use ($filtres) {
                $q->where('assurance_id', $filtres['assurance_id']);
            });
        }

        if (isset($filtres['date_debut'])) {
            $query->whereDate('date_demande', '>=', $filtres['date_debut']);
        }

        if (isset($filtres['date_fin'])) {
            $query->whereDate('date_demande', '<=', $filtres['date_fin']);
        }

        $totalPEC = (clone $query)->count();
        $pecAcceptees = (clone $query)->where('statut', 'acceptee')->count();
        $montantTotal = (clone $query)->sum('montant_demande');
        $montantAccorde = (clone $query)->where('statut', 'acceptee')->sum('montant_accorde');

        return [
            'total_pec' => $totalPEC,
            'pec_acceptees' => $pecAcceptees,
            'pec_refusees' => (clone $query)->where('statut', 'refusee')->count(),
            'pec_en_attente' => (clone $query)->where('statut', 'en_attente')->count(),
            'taux_acceptation' => $totalPEC > 0 ? round(($pecAcceptees / $totalPEC) * 100, 2) : 0,
            'montant_total_demande' => $montantTotal,
            'montant_total_accorde' => $montantAccorde,
            'taux_couverture_moyen' => $montantTotal > 0 ? round(($montantAccorde / $montantTotal) * 100, 2) : 0,
            'delai_traitement_moyen' => (clone $query)
                ->whereNotNull('date_reponse')
                ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, date_demande, date_reponse)) as delai')
                ->value('delai'),
            'top_types_pec' => (clone $query)
                ->select('type_pec', DB::raw('COUNT(*) as total'))
                ->groupBy('type_pec')
                ->orderByDesc('total')
                ->limit(5)
                ->pluck('total', 'type_pec'),
        ];
    }
}
