<?php

namespace App\Services;

use App\Models\Ordonnance;
use App\Models\OrdonnanceLigne;
use App\Models\ProduitPharmaceutique;
use App\Models\User;
use App\Models\Notification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Collection;
use Exception;
use App\Services\StockService;

class OrdonnanceService
{
    protected $notificationService;
    protected $stockService;

    public function __construct(
        NotificationService $notificationService,
        StockService $stockService
    ) {
        $this->notificationService = $notificationService;
        $this->stockService = $stockService;
    }

    /**
     * Créer une nouvelle ordonnance
     */
    public function creerOrdonnance(array $data, User $praticien): Ordonnance
    {
        DB::beginTransaction();

        try {
            // Créer l'ordonnance
            $ordonnanceData = [
                'consultation_id' => $data['consultation_id'] ?? null,
                'patient_id' => $data['patient_id'],
                'praticien_id' => $praticien->id,
                'structure_id' => $praticien->structure_id,
                'date_ordonnance' => now(),
                'diagnostic' => $data['diagnostic'],
                'observations' => $data['observations'] ?? null,
                'type_ordonnance' => $data['type_ordonnance'] ?? 'normale',
                'validite_jours' => $data['validite_jours'] ?? 15,
                'renouvelable' => $data['renouvelable'] ?? false,
                'nombre_renouvellements' => $data['nombre_renouvellements'] ?? 0,
                'statut' => 'active',
            ];

            $ordonnance = Ordonnance::create($ordonnanceData);

            // Ajouter les lignes de médicaments
            foreach ($data['lignes'] as $ligneData) {
                $this->ajouterLigneOrdonnance($ordonnance, $ligneData);
            }

            // Vérifications de sécurité
            $this->verifierSecuriteOrdonnance($ordonnance);

            // Notifier le patient
            $this->notificationService->notifier(
                $ordonnance->patient_id,
                'Nouvelle ordonnance',
                "Une nouvelle ordonnance a été créée pour vous par Dr. {$praticien->nom}",
                'ordonnance',
                $ordonnance->id
            );

            DB::commit();

            return $ordonnance->fresh('lignes');

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Erreur création ordonnance: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Ajouter une ligne à l'ordonnance
     */
    protected function ajouterLigneOrdonnance(Ordonnance $ordonnance, array $ligneData): OrdonnanceLigne
    {
        $produit = ProduitPharmaceutique::findOrFail($ligneData['produit_id']);

        // Vérifier le stock
        if ($produit->stock_disponible < $ligneData['quantite']) {
            Log::warning("Stock insuffisant pour {$produit->nom_commercial}");
        }

        return $ordonnance->lignes()->create([
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

    /**
     * Vérifier la sécurité de l'ordonnance
     */
    protected function verifierSecuriteOrdonnance(Ordonnance $ordonnance): array
    {
        $alertes = [];

        // Vérifier les interactions médicamenteuses
        $interactions = $this->verifierInteractions($ordonnance);
        if (!empty($interactions)) {
            $alertes['interactions'] = $interactions;

            // Créer une alerte pour le praticien
            Notification::create([
                'user_id' => $ordonnance->praticien_id,
                'type' => 'alerte_interaction',
                'titre' => 'Interactions médicamenteuses détectées',
                'message' => 'Des interactions ont été détectées dans l\'ordonnance #' . $ordonnance->numero_ordonnance,
                'data' => ['interactions' => $interactions],
                'priorite' => 'haute',
            ]);
        }

        // Vérifier les contre-indications
        $contrIndications = $this->verifierContrIndications($ordonnance);
        if (!empty($contrIndications)) {
            $alertes['contre_indications'] = $contrIndications;
        }

        // Vérifier les doses maximales
        $dosesExcessives = $this->verifierDosesMaximales($ordonnance);
        if (!empty($dosesExcessives)) {
            $alertes['doses_excessives'] = $dosesExcessives;
        }

        return $alertes;
    }

    /**
     * Vérifier les interactions médicamenteuses
     */
    public function verifierInteractions(Ordonnance $ordonnance): array
    {
        $interactions = [];
        $medicaments = $ordonnance->lignes->pluck('dci')->unique()->values();

        // Base de données simplifiée d'interactions
        $interactionsDB = [
            ['aspirine', 'warfarine', 'risque hémorragique augmenté'],
            ['metformine', 'alcool', 'risque d\'acidose lactique'],
            ['tramadol', 'ssri', 'syndrome sérotoninergique'],
            ['amiodarone', 'digoxine', 'risque de toxicité digitalique'],
            ['clarithromycine', 'statines', 'risque de rhabdomyolyse'],
        ];

        foreach ($interactionsDB as $interaction) {
            $med1 = $interaction[0];
            $med2 = $interaction[1];
            $risque = $interaction[2];

            $found1 = $medicaments->first(function ($dci) use ($med1) {
                return stripos($dci, $med1) !== false;
            });

            $found2 = $medicaments->first(function ($dci) use ($med2) {
                return stripos($dci, $med2) !== false;
            });

            if ($found1 && $found2) {
                $interactions[] = [
                    'medicament1' => $found1,
                    'medicament2' => $found2,
                    'risque' => $risque,
                    'severite' => 'majeure',
                ];
            }
        }

        return $interactions;
    }

    /**
     * Vérifier les contre-indications
     */
    public function verifierContrIndications(Ordonnance $ordonnance): array
    {
        $contrIndications = [];
        $patient = $ordonnance->patient;

        if (!$patient->dossierMedical) {
            return $contrIndications;
        }

        $dme = $patient->dossierMedical;

        // Vérifier les allergies
        if ($dme->allergies && is_array($dme->allergies)) {
            foreach ($ordonnance->lignes as $ligne) {
                foreach ($dme->allergies as $allergie) {
                    if (stripos($ligne->dci, $allergie) !== false ||
                        stripos($ligne->nom_commercial, $allergie) !== false) {
                        $contrIndications[] = [
                            'type' => 'allergie',
                            'medicament' => $ligne->nom_commercial,
                            'detail' => "Allergie connue : {$allergie}",
                            'severite' => 'critique',
                        ];
                    }
                }
            }
        }

        // Vérifier les conditions médicales
        if ($dme->antecedents && is_array($dme->antecedents)) {
            // Exemple : vérifier les contre-indications pour l'insuffisance rénale
            if (in_array('insuffisance_renale', $dme->antecedents)) {
                foreach ($ordonnance->lignes as $ligne) {
                    // Liste des médicaments contre-indiqués en cas d'insuffisance rénale
                    $medicamentsCI = ['metformine', 'ains', 'aminoside'];

                    foreach ($medicamentsCI as $med) {
                        if (stripos($ligne->dci, $med) !== false) {
                            $contrIndications[] = [
                                'type' => 'pathologie',
                                'medicament' => $ligne->nom_commercial,
                                'detail' => 'Contre-indiqué en cas d\'insuffisance rénale',
                                'severite' => 'majeure',
                            ];
                        }
                    }
                }
            }
        }

        return $contrIndications;
    }

    /**
     * Vérifier les doses maximales
     */
    public function verifierDosesMaximales(Ordonnance $ordonnance): array
    {
        $dosesExcessives = [];

        // Base de données des doses maximales journalières (mg)
        $dosesMax = [
            'paracetamol' => 4000,
            'ibuprofene' => 1200,
            'aspirine' => 3000,
            'tramadol' => 400,
            'codeine' => 240,
            'morphine' => 200,
        ];

        foreach ($ordonnance->lignes as $ligne) {
            foreach ($dosesMax as $medicament => $doseMax) {
                if (stripos($ligne->dci, $medicament) !== false) {
                    // Extraire la dose du dosage (ex: "500mg" -> 500)
                    preg_match('/(\d+)\s*mg/i', $ligne->dosage, $matches);

                    if (isset($matches[1])) {
                        $doseUnitaire = (int)$matches[1];

                        // Calculer la dose journalière basée sur la posologie
                        $dosesParJour = $this->calculerDosesParJour($ligne->posologie);
                        $doseJournaliere = $doseUnitaire * $dosesParJour;

                        if ($doseJournaliere > $doseMax) {
                            $dosesExcessives[] = [
                                'medicament' => $ligne->nom_commercial,
                                'dose_prescrite' => $doseJournaliere,
                                'dose_maximale' => $doseMax,
                                'depassement' => round(($doseJournaliere / $doseMax - 1) * 100, 1),
                            ];
                        }
                    }
                }
            }
        }

        return $dosesExcessives;
    }

    /**
     * Calculer le nombre de doses par jour depuis la posologie
     */
    protected function calculerDosesParJour(string $posologie): int
    {
        // Patterns pour détecter les fréquences
        $patterns = [
            '/(\d+)\s*fois\s*par\s*jour/i' => function($matches) { return (int)$matches[1]; },
            '/toutes\s*les\s*(\d+)\s*heures/i' => function($matches) { return 24 / (int)$matches[1]; },
            '/matin\s*et\s*soir/i' => function() { return 2; },
            '/matin.*midi.*soir/i' => function() { return 3; },
            '/(\d+)\s*comprim/i' => function($matches) { return (int)$matches[1]; },
        ];

        foreach ($patterns as $pattern => $calculator) {
            if (preg_match($pattern, $posologie, $matches)) {
                return $calculator($matches);
            }
        }

        // Par défaut, supposer 3 fois par jour
        return 3;
    }

    /**
     * Dispenser une ordonnance
     */
    public function dispenserOrdonnance(Ordonnance $ordonnance, array $lignesDispensees, int $pharmacieId): bool
    {
        DB::beginTransaction();

        try {
            $toutesDispensees = true;

            foreach ($lignesDispensees as $ligneData) {
                $ligne = OrdonnanceLigne::findOrFail($ligneData['id']);

                if ($ligne->ordonnance_id !== $ordonnance->id) {
                    throw new Exception('Ligne d\'ordonnance invalide');
                }

                // Marquer la ligne comme dispensée
                $ligne->marquerDispensee($ligneData['quantite_dispensee'], $pharmacieId);

                // Mettre à jour le stock
                $this->stockService->deduireStock(
                    $ligne->produit_id,
                    $ligneData['quantite_dispensee'],
                    'dispensation',
                    $ordonnance->numero_ordonnance
                );

                if (!$ligne->estCompletementDispensee()) {
                    $toutesDispensees = false;
                }
            }

            // Mettre à jour le statut de l'ordonnance
            if ($toutesDispensees) {
                $ordonnance->marquerDispensee($pharmacieId);
            } else {
                $ordonnance->marquerPartiellemmentDispensee(
                    array_column($lignesDispensees, 'id')
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
            return true;

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Erreur dispensation ordonnance: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Renouveler une ordonnance
     */
    public function renouvelerOrdonnance(Ordonnance $ordonnance, User $praticien = null): ?Ordonnance
    {
        if (!$ordonnance->peutEtreRenouvelee()) {
            throw new Exception('Cette ordonnance ne peut pas être renouvelée');
        }

        DB::beginTransaction();

        try {
            // Créer la nouvelle ordonnance
            $nouvelleOrdonnance = $ordonnance->replicate();
            $nouvelleOrdonnance->numero_ordonnance = Ordonnance::generateNumeroOrdonnance();
            $nouvelleOrdonnance->date_ordonnance = now();
            $nouvelleOrdonnance->statut = 'active';
            $nouvelleOrdonnance->renouvellements_effectues = 0;

            if ($praticien) {
                $nouvelleOrdonnance->praticien_id = $praticien->id;
            }

            // Ajouter les métadonnées
            $metadata = $nouvelleOrdonnance->metadata ?? [];
            $metadata['ordonnance_origine'] = $ordonnance->numero_ordonnance;
            $metadata['numero_renouvellement'] = $ordonnance->renouvellements_effectues + 1;
            $metadata['date_renouvellement'] = now()->toDateTimeString();
            $nouvelleOrdonnance->metadata = $metadata;

            $nouvelleOrdonnance->save();

            // Copier les lignes
            foreach ($ordonnance->lignes as $ligne) {
                $nouvelleLigne = $ligne->replicate();
                $nouvelleLigne->ordonnance_id = $nouvelleOrdonnance->id;
                $nouvelleLigne->dispensee = false;
                $nouvelleLigne->quantite_dispensee = 0;
                $nouvelleLigne->date_dispensation = null;
                $nouvelleLigne->pharmacie_id = null;
                $nouvelleLigne->save();
            }

            // Incrémenter le compteur de renouvellements
            $ordonnance->increment('renouvellements_effectues');

            // Notifier le patient
            $this->notificationService->notifier(
                $nouvelleOrdonnance->patient_id,
                'Ordonnance renouvelée',
                "Votre ordonnance a été renouvelée avec succès",
                'ordonnance',
                $nouvelleOrdonnance->id
            );

            DB::commit();
            return $nouvelleOrdonnance;

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Erreur renouvellement ordonnance: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Obtenir les statistiques des ordonnances
     */
    public function getStatistiques(array $filtres = []): array
    {
        $query = Ordonnance::query();

        // Appliquer les filtres
        if (isset($filtres['praticien_id'])) {
            $query->where('praticien_id', $filtres['praticien_id']);
        }

        if (isset($filtres['structure_id'])) {
            $query->where('structure_id', $filtres['structure_id']);
        }

        if (isset($filtres['date_debut'])) {
            $query->whereDate('date_ordonnance', '>=', $filtres['date_debut']);
        }

        if (isset($filtres['date_fin'])) {
            $query->whereDate('date_ordonnance', '<=', $filtres['date_fin']);
        }

        return [
            'total' => (clone $query)->count(),
            'actives' => (clone $query)->where('statut', 'active')->count(),
            'dispensees' => (clone $query)->where('statut', 'dispensee')->count(),
            'partiellement_dispensees' => (clone $query)->where('statut', 'partiellement_dispensee')->count(),
            'expirees' => (clone $query)->where('statut', 'expiree')->count(),
            'annulees' => (clone $query)->where('statut', 'annulee')->count(),
            'renouvelables' => (clone $query)->where('renouvelable', true)->count(),
            'montant_total' => (clone $query)->withSum('lignes', 'montant_total')->get()->sum('lignes_sum_montant_total'),
            'medicaments_prescrits' => OrdonnanceLigne::whereIn('ordonnance_id', (clone $query)->pluck('id'))->count(),
        ];
    }
}
