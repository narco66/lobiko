<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pharmacie;
use App\Models\StructureMedicale;
use App\Models\StockMedicament;
use App\Models\ProduitPharmaceutique;
use App\Models\FournisseurPharmaceutique;
use App\Models\CommandePharmaceutique;
use App\Models\User;
use Faker\Factory as Faker;

class PharmacieSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('fr_FR');

        // Créer des structures médicales de type pharmacie si elles n'existent pas
        $structuresPharmacies = [];
        for ($i = 1; $i <= 10; $i++) {
            $structure = StructureMedicale::create([
                'nom' => "Pharmacie " . $faker->company,
                'type_structure' => 'pharmacie',
                'adresse' => $faker->address,
                'ville' => $faker->city,
                'pays' => 'Gabon',
                'telephone' => $faker->phoneNumber,
                'email' => $faker->unique()->safeEmail,
                'latitude' => $faker->latitude(-4, 2),
                'longitude' => $faker->longitude(8, 15),
                'statut' => 'active',
            ]);
            $structuresPharmacies[] = $structure;
        }

        // Créer des pharmacies
        $pharmacies = [];
        foreach ($structuresPharmacies as $index => $structure) {
            $pharmacie = Pharmacie::create([
                'structure_medicale_id' => $structure->id,
                'numero_licence' => 'PHG-' . str_pad($index + 1, 4, '0', STR_PAD_LEFT),
                'nom_pharmacie' => $structure->nom,
                'nom_responsable' => $faker->name,
                'telephone_pharmacie' => $structure->telephone,
                'email_pharmacie' => $structure->email,
                'adresse_complete' => $structure->adresse . ', ' . $structure->ville,
                'latitude' => $structure->latitude,
                'longitude' => $structure->longitude,
                'horaires_ouverture' => $this->genererHoraires(),
                'service_garde' => $faker->boolean(30),
                'livraison_disponible' => $faker->boolean(70),
                'rayon_livraison_km' => $faker->randomFloat(1, 5, 20),
                'frais_livraison_base' => $faker->randomFloat(2, 1000, 3000),
                'frais_livraison_par_km' => $faker->randomFloat(2, 200, 500),
                'paiement_mobile_money' => true,
                'paiement_carte' => $faker->boolean(60),
                'paiement_especes' => true,
                'statut' => 'active',
            ]);
            $pharmacies[] = $pharmacie;
        }

        // Créer des produits pharmaceutiques s'ils n'existent pas
        $produits = $this->creerProduits();

        // Créer des stocks pour chaque pharmacie
        foreach ($pharmacies as $pharmacie) {
            // Sélectionner aléatoirement 50-150 produits pour cette pharmacie
            $produitsStock = $faker->randomElements($produits, $faker->numberBetween(50, 150));

            foreach ($produitsStock as $produit) {
                $quantiteDisponible = $faker->numberBetween(0, 500);
                $dateExpiration = $faker->dateTimeBetween('-30 days', '+2 years');

                // Déterminer le statut en fonction de la quantité et de la date d'expiration
                $statut = 'disponible';
                if ($quantiteDisponible == 0) {
                    $statut = 'rupture';
                } elseif ($quantiteDisponible <= 10) {
                    $statut = 'faible';
                } elseif ($dateExpiration < now()) {
                    $statut = 'expire';
                }

                StockMedicament::create([
                    'pharmacie_id' => $pharmacie->id,
                    'produit_pharmaceutique_id' => $produit->id,
                    'quantite_disponible' => $quantiteDisponible,
                    'quantite_minimum' => $faker->numberBetween(5, 20),
                    'quantite_maximum' => $faker->numberBetween(100, 1000),
                    'prix_vente' => $produit->prix_reference * $faker->randomFloat(2, 0.9, 1.3),
                    'prix_achat' => $produit->prix_reference * $faker->randomFloat(2, 0.6, 0.8),
                    'numero_lot' => 'LOT-' . $faker->bothify('??##??##'),
                    'date_expiration' => $dateExpiration,
                    'emplacement_rayon' => $faker->randomElement(['A', 'B', 'C', 'D']) . $faker->numberBetween(1, 20),
                    'prescription_requise' => $produit->prescription_requise,
                    'disponible_vente' => $statut !== 'expire',
                    'statut_stock' => $statut,
                ]);
            }

            // Créer quelques alertes de stock
            $this->creerAlertesStock($pharmacie);
        }

        // Créer des fournisseurs
        $fournisseurs = $this->creerFournisseurs();

        // Associer des fournisseurs aux pharmacies
        foreach ($pharmacies as $pharmacie) {
            $fournisseursSelectionnes = $faker->randomElements($fournisseurs, $faker->numberBetween(2, 5));
            foreach ($fournisseursSelectionnes as $fournisseur) {
                $pharmacie->fournisseurs()->attach($fournisseur->id, [
                    'numero_compte_client' => 'CLI-' . $faker->bothify('####'),
                    'statut' => 'actif',
                    'credit_maximum' => $faker->randomFloat(2, 50000, 500000),
                    'credit_utilise' => $faker->randomFloat(2, 0, 100000),
                ]);
            }
        }

        // Créer des commandes de test
        $this->creerCommandes($pharmacies, $produits);

        $this->command->info('Pharmacies seeded successfully!');
    }

    /**
     * Générer les horaires d'ouverture
     */
    private function genererHoraires()
    {
        $jours = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
        $horaires = [];

        foreach ($jours as $jour) {
            if ($jour === 'sunday') {
                // Dimanche : 50% de chance d'être fermé
                $ouvert = rand(0, 1) === 1;
                $horaires[$jour] = [
                    'ouvert' => $ouvert,
                    'ouverture' => $ouvert ? '09:00' : null,
                    'fermeture' => $ouvert ? '13:00' : null,
                ];
            } else {
                // Jours de semaine
                $horaires[$jour] = [
                    'ouvert' => true,
                    'ouverture' => '08:00',
                    'fermeture' => $jour === 'saturday' ? '18:00' : '20:00',
                ];
            }
        }

        return $horaires;
    }

    /**
     * Créer des produits pharmaceutiques
     */
    private function creerProduits()
    {
        $produitsData = [
            // Antalgiques et antipyrétiques
            ['nom' => 'Paracétamol 500mg', 'dci' => 'Paracétamol', 'forme' => 'comprimé', 'prix' => 500, 'prescription' => false],
            ['nom' => 'Paracétamol 1g', 'dci' => 'Paracétamol', 'forme' => 'comprimé', 'prix' => 800, 'prescription' => false],
            ['nom' => 'Ibuprofène 400mg', 'dci' => 'Ibuprofène', 'forme' => 'comprimé', 'prix' => 1000, 'prescription' => false],
            ['nom' => 'Aspirine 500mg', 'dci' => 'Acide acétylsalicylique', 'forme' => 'comprimé', 'prix' => 600, 'prescription' => false],

            // Antibiotiques
            ['nom' => 'Amoxicilline 500mg', 'dci' => 'Amoxicilline', 'forme' => 'gélule', 'prix' => 2000, 'prescription' => true],
            ['nom' => 'Amoxicilline 1g', 'dci' => 'Amoxicilline', 'forme' => 'comprimé', 'prix' => 3000, 'prescription' => true],
            ['nom' => 'Azithromycine 500mg', 'dci' => 'Azithromycine', 'forme' => 'comprimé', 'prix' => 4000, 'prescription' => true],
            ['nom' => 'Ciprofloxacine 500mg', 'dci' => 'Ciprofloxacine', 'forme' => 'comprimé', 'prix' => 3500, 'prescription' => true],

            // Antipaludéens
            ['nom' => 'Artéméther-Luméfantrine', 'dci' => 'Artéméther-Luméfantrine', 'forme' => 'comprimé', 'prix' => 5000, 'prescription' => true],
            ['nom' => 'Quinine 300mg', 'dci' => 'Quinine', 'forme' => 'comprimé', 'prix' => 2500, 'prescription' => true],
            ['nom' => 'Artésunate 50mg', 'dci' => 'Artésunate', 'forme' => 'comprimé', 'prix' => 3000, 'prescription' => true],

            // Antihypertenseurs
            ['nom' => 'Amlodipine 5mg', 'dci' => 'Amlodipine', 'forme' => 'comprimé', 'prix' => 1500, 'prescription' => true],
            ['nom' => 'Captopril 25mg', 'dci' => 'Captopril', 'forme' => 'comprimé', 'prix' => 1200, 'prescription' => true],
            ['nom' => 'Losartan 50mg', 'dci' => 'Losartan', 'forme' => 'comprimé', 'prix' => 2000, 'prescription' => true],

            // Antidiabétiques
            ['nom' => 'Metformine 500mg', 'dci' => 'Metformine', 'forme' => 'comprimé', 'prix' => 1000, 'prescription' => true],
            ['nom' => 'Glibenclamide 5mg', 'dci' => 'Glibenclamide', 'forme' => 'comprimé', 'prix' => 800, 'prescription' => true],

            // Vitamines et suppléments
            ['nom' => 'Vitamine C 500mg', 'dci' => 'Acide ascorbique', 'forme' => 'comprimé', 'prix' => 500, 'prescription' => false],
            ['nom' => 'Fer + Acide folique', 'dci' => 'Fer-Acide folique', 'forme' => 'comprimé', 'prix' => 700, 'prescription' => false],
            ['nom' => 'Multivitamines', 'dci' => 'Complexe vitaminique', 'forme' => 'comprimé', 'prix' => 1500, 'prescription' => false],

            // Antihistaminiques
            ['nom' => 'Loratadine 10mg', 'dci' => 'Loratadine', 'forme' => 'comprimé', 'prix' => 1000, 'prescription' => false],
            ['nom' => 'Cétirizine 10mg', 'dci' => 'Cétirizine', 'forme' => 'comprimé', 'prix' => 900, 'prescription' => false],

            // Antiacides
            ['nom' => 'Oméprazole 20mg', 'dci' => 'Oméprazole', 'forme' => 'gélule', 'prix' => 1500, 'prescription' => false],
            ['nom' => 'Ranitidine 150mg', 'dci' => 'Ranitidine', 'forme' => 'comprimé', 'prix' => 1000, 'prescription' => false],
        ];

        $produits = [];
        foreach ($produitsData as $data) {
            $produit = ProduitPharmaceutique::firstOrCreate(
                ['dci' => $data['dci'], 'forme_galenique' => $data['forme']],
                [
                    'nom_commercial' => $data['nom'],
                    'dosage' => $data['nom'],
                    'conditionnement' => 'Boîte de 10',
                    'categorie' => 'Médicament',
                    'prix_reference' => $data['prix'],
                    'prescription_requise' => $data['prescription'],
                    'remboursable' => true,
                    'taux_remboursement' => $data['prescription'] ? 70 : 30,
                    'statut' => 'actif',
                ]
            );
            $produits[] = $produit;
        }

        return $produits;
    }

    /**
     * Créer des fournisseurs
     */
    private function creerFournisseurs()
    {
        $faker = Faker::create('fr_FR');
        $fournisseurs = [];

        $nomsFournisseurs = [
            'Pharma Gabon Distribution',
            'Centrale Pharmaceutique du Gabon',
            'MedSupply Africa',
            'Laboratoires Unis du Gabon',
            'Distribution Médicale Centrale',
        ];

        foreach ($nomsFournisseurs as $index => $nom) {
            $fournisseur = FournisseurPharmaceutique::create([
                'nom_fournisseur' => $nom,
                'numero_licence' => 'FOURG-' . str_pad($index + 1, 4, '0', STR_PAD_LEFT),
                'telephone' => $faker->phoneNumber,
                'email' => $faker->unique()->companyEmail,
                'adresse' => $faker->address,
                'personne_contact' => $faker->name,
                'telephone_contact' => $faker->phoneNumber,
                'categories_produits' => ['Médicaments', 'Matériel médical', 'Consommables'],
                'delai_livraison_jours' => $faker->numberBetween(1, 5),
                'montant_minimum_commande' => $faker->randomFloat(2, 10000, 50000),
                'statut' => 'actif',
            ]);
            $fournisseurs[] = $fournisseur;
        }

        return $fournisseurs;
    }

    /**
     * Créer des alertes de stock
     */
    private function creerAlertesStock($pharmacie)
    {
        $faker = Faker::create('fr_FR');

        // Récupérer quelques stocks problématiques
        $stocksProblematiques = $pharmacie->stocks()
            ->whereIn('statut_stock', ['faible', 'rupture'])
            ->orWhere('date_expiration', '<', now()->addDays(30))
            ->take(5)
            ->get();

        foreach ($stocksProblematiques as $stock) {
            $typeAlerte = 'stock_faible';
            $message = "Stock faible pour {$stock->produitPharmaceutique->nom_commercial}";

            if ($stock->statut_stock === 'rupture') {
                $typeAlerte = 'rupture_stock';
                $message = "Rupture de stock pour {$stock->produitPharmaceutique->nom_commercial}";
            } elseif ($stock->date_expiration && $stock->date_expiration < now()->addDays(30)) {
                $typeAlerte = 'expiration_proche';
                $message = "Expiration proche pour {$stock->produitPharmaceutique->nom_commercial} - Lot: {$stock->numero_lot}";
            }

            $pharmacie->alertes()->create([
                'stock_medicament_id' => $stock->id,
                'type_alerte' => $typeAlerte,
                'message' => $message,
                'vue' => $faker->boolean(30),
                'traitee' => false,
            ]);
        }
    }

    /**
     * Créer des commandes de test
     */
    private function creerCommandes($pharmacies, $produits)
    {
        $faker = Faker::create('fr_FR');

        // Récupérer quelques patients
        $patients = User::role('patient')->take(10)->get();

        if ($patients->isEmpty()) {
            return;
        }

        foreach ($pharmacies as $pharmacie) {
            // Créer 5-10 commandes par pharmacie
            $nombreCommandes = $faker->numberBetween(5, 10);

            for ($i = 0; $i < $nombreCommandes; $i++) {
                $patient = $faker->randomElement($patients);
                $modeRetrait = $faker->randomElement(['sur_place', 'livraison']);
                $statut = $faker->randomElement(['en_attente', 'confirmee', 'en_preparation', 'prete', 'livree']);

                $commande = CommandePharmaceutique::create([
                    'numero_commande' => 'CMD-' . now()->format('Ymd') . '-' . strtoupper($faker->bothify('??????')),
                    'patient_id' => $patient->id,
                    'pharmacie_id' => $pharmacie->id,
                    'montant_total' => 0, // Sera calculé après
                    'montant_assurance' => 0,
                    'montant_patient' => 0,
                    'mode_retrait' => $modeRetrait,
                    'adresse_livraison' => $modeRetrait === 'livraison' ? $faker->address : null,
                    'latitude_livraison' => $modeRetrait === 'livraison' ? $faker->latitude(-4, 2) : null,
                    'longitude_livraison' => $modeRetrait === 'livraison' ? $faker->longitude(8, 15) : null,
                    'frais_livraison' => $modeRetrait === 'livraison' ? $faker->randomFloat(2, 1000, 5000) : 0,
                    'statut' => $statut,
                    'date_commande' => $faker->dateTimeBetween('-7 days', 'now'),
                    'code_retrait' => strtoupper($faker->bothify('########')),
                    'urgent' => $faker->boolean(20),
                ]);

                // Ajouter des lignes de commande
                $nombreLignes = $faker->numberBetween(1, 5);
                $montantTotal = 0;
                $montantAssurance = 0;

                for ($j = 0; $j < $nombreLignes; $j++) {
                    $stock = $pharmacie->stocks()->disponible()->inRandomOrder()->first();

                    if ($stock) {
                        $quantite = $faker->numberBetween(1, min(3, $stock->quantite_disponible));
                        $montantLigne = $stock->prix_vente * $quantite;
                        $tauxRemboursement = $stock->prescription_requise ? 70 : 30;
                        $montantRemboursement = ($montantLigne * $tauxRemboursement) / 100;

                        $commande->lignes()->create([
                            'produit_pharmaceutique_id' => $stock->produit_pharmaceutique_id,
                            'stock_medicament_id' => $stock->id,
                            'quantite_commandee' => $quantite,
                            'quantite_livree' => $statut === 'livree' ? $quantite : 0,
                            'prix_unitaire' => $stock->prix_vente,
                            'montant_ligne' => $montantLigne,
                            'taux_remboursement' => $tauxRemboursement,
                            'montant_remboursement' => $montantRemboursement,
                        ]);

                        $montantTotal += $montantLigne;
                        $montantAssurance += $montantRemboursement;
                    }
                }

                // Ajouter les frais de livraison au total
                if ($modeRetrait === 'livraison') {
                    $montantTotal += $commande->frais_livraison;
                }

                // Mettre à jour les montants
                $commande->update([
                    'montant_total' => $montantTotal,
                    'montant_assurance' => $montantAssurance,
                    'montant_patient' => $montantTotal - $montantAssurance,
                ]);

                // Créer une livraison si nécessaire
                if ($modeRetrait === 'livraison' && in_array($statut, ['en_livraison', 'livree'])) {
                    $commande->livraison()->create([
                        'numero_livraison' => 'LIV-' . now()->format('Ymd') . '-' . strtoupper($faker->bothify('??????')),
                        'statut' => $statut === 'livree' ? 'livree' : 'en_cours',
                        'date_livraison' => $statut === 'livree' ? $faker->dateTimeBetween('-2 days', 'now') : null,
                    ]);
                }
            }
        }
    }
}
