<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Table des compagnies d'assurance
        Schema::create('compagnies_assurance', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('code_assureur')->unique();
            $table->string('nom_assureur');
            $table->string('nom_commercial')->nullable();
            $table->enum('type', ['prive', 'public', 'mutuelle', 'internationale']);

            // Informations légales
            $table->string('numero_agrement');
            $table->string('numero_fiscal')->nullable();
            $table->string('registre_commerce')->nullable();

            // Contact
            $table->string('adresse');
            $table->string('ville');
            $table->string('pays')->default('Gabon');
            $table->string('telephone');
            $table->string('email');
            $table->string('site_web')->nullable();

            // Contact médical
            $table->string('email_medical')->nullable();
            $table->string('telephone_medical')->nullable();
            $table->string('fax')->nullable();

            // API et intégration
            $table->boolean('api_active')->default(false);
            $table->string('api_url')->nullable();
            $table->string('api_key')->nullable();
            $table->string('api_secret')->nullable();
            $table->enum('api_version', ['v1', 'v2', 'v3'])->nullable();
            $table->json('api_endpoints')->nullable();

            // Paramètres
            $table->boolean('tiers_payant')->default(true);
            $table->boolean('pec_temps_reel')->default(false);
            $table->integer('delai_remboursement')->default(30); // En jours
            $table->decimal('taux_commission', 5, 2)->default(0);
            $table->json('documents_requis')->nullable(); // Documents pour PEC

            // Plafonds généraux
            $table->decimal('plafond_annuel_global', 12, 2)->nullable();
            $table->decimal('plafond_consultation', 10, 2)->nullable();
            $table->decimal('plafond_pharmacie', 10, 2)->nullable();
            $table->decimal('plafond_hospitalisation', 12, 2)->nullable();

            // Statut
            $table->boolean('actif')->default(true);
            $table->boolean('partenaire')->default(false);
            $table->date('date_partenariat')->nullable();
            $table->date('fin_partenariat')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Index
            $table->index('code_assureur');
            $table->index('type');
            $table->index('actif');
            $table->index('partenaire');
        });

        // Table des contrats d'assurance
        Schema::create('contrats_assurance', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('numero_contrat')->unique();
            $table->uuid('patient_id');
            $table->uuid('assureur_id');

            // Type de contrat
            $table->enum('type_contrat', ['individuel', 'famille', 'entreprise', 'collectif']);
            $table->string('formule'); // Base, Medium, Premium, etc.
            $table->string('numero_police')->nullable();
            $table->string('numero_adherent');

            // Souscripteur (si différent du patient)
            $table->string('souscripteur_nom')->nullable();
            $table->string('souscripteur_prenom')->nullable();
            $table->string('souscripteur_entreprise')->nullable();
            $table->string('lien_souscripteur')->nullable(); // conjoint, enfant, employé

            // Validité
            $table->date('date_debut');
            $table->date('date_fin');
            $table->date('date_renouvellement')->nullable();
            $table->boolean('auto_renouvellement')->default(true);
            $table->enum('statut', ['actif', 'suspendu', 'expire', 'resilie'])->default('actif');

            // Couverture générale
            $table->decimal('taux_couverture_consultation', 5, 2)->default(70);
            $table->decimal('taux_couverture_pharmacie', 5, 2)->default(65);
            $table->decimal('taux_couverture_hospitalisation', 5, 2)->default(80);
            $table->decimal('taux_couverture_analyse', 5, 2)->default(70);
            $table->decimal('taux_couverture_imagerie', 5, 2)->default(70);
            $table->decimal('taux_couverture_dentaire', 5, 2)->default(50);
            $table->decimal('taux_couverture_optique', 5, 2)->default(60);

            // Plafonds
            $table->decimal('plafond_annuel', 12, 2);
            $table->decimal('plafond_consomme', 12, 2)->default(0);
            $table->decimal('plafond_restant', 12, 2);
            $table->json('plafonds_par_categorie')->nullable();

            // Franchises et délais
            $table->decimal('franchise_annuelle', 10, 2)->default(0);
            $table->decimal('franchise_consommee', 10, 2)->default(0);
            $table->integer('delai_carence')->default(0); // En jours
            $table->date('fin_carence')->nullable();

            // Exclusions et restrictions
            $table->json('exclusions')->nullable(); // Pathologies/actes exclus
            $table->json('restrictions')->nullable();
            $table->boolean('maternite_couverte')->default(true);
            $table->boolean('dentaire_couvert')->default(true);
            $table->boolean('optique_couvert')->default(true);
            $table->boolean('prevention_couverte')->default(true);

            // Bénéficiaires
            $table->json('beneficiaires')->nullable(); // Liste des ayants droit
            $table->integer('nombre_beneficiaires')->default(1);

            // Documents
            $table->string('carte_assure')->nullable();
            $table->string('attestation')->nullable();
            $table->date('date_emission_carte')->nullable();
            $table->date('validite_carte')->nullable();

            // Cotisations
            $table->decimal('cotisation_mensuelle', 10, 2)->nullable();
            $table->decimal('cotisation_annuelle', 10, 2)->nullable();
            $table->boolean('cotisation_a_jour')->default(true);
            $table->date('derniere_cotisation')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Index
            $table->index('numero_contrat');
            $table->index(['patient_id', 'statut']);
            $table->index(['assureur_id', 'statut']);
            $table->index('date_fin');
            $table->index('statut');

            // Foreign keys
            $table->foreign('patient_id')->references('id')->on('users')->restrictOnDelete();
            $table->foreign('assureur_id')->references('id')->on('compagnies_assurance')->restrictOnDelete();
        });

        // Table des prises en charge (PEC)
        Schema::create('prises_en_charge', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('numero_pec')->unique();
            $table->uuid('contrat_id');
            $table->uuid('patient_id');
            $table->uuid('assureur_id');
            $table->uuid('prestataire_id'); // Médecin ou structure
            $table->uuid('facture_id')->nullable();

            // Type et nature
            $table->enum('type', ['consultation', 'pharmacie', 'hospitalisation', 'analyse', 'imagerie', 'autre']);
            $table->enum('nature', ['initiale', 'complementaire', 'prolongation']);
            $table->boolean('urgence')->default(false);
            $table->uuid('pec_initiale_id')->nullable(); // Si prolongation/complément

            // Montants
            $table->decimal('montant_total', 10, 2);
            $table->decimal('montant_couvert', 10, 2);
            $table->decimal('montant_franchise', 10, 2)->default(0);
            $table->decimal('montant_exclusion', 10, 2)->default(0);
            $table->decimal('reste_a_charge', 10, 2);
            $table->decimal('taux_couverture_applique', 5, 2);

            // Détails médicaux
            $table->string('diagnostic')->nullable();
            $table->string('code_cim10')->nullable();
            $table->json('actes_prevus')->nullable();
            $table->json('medicaments_prevus')->nullable();
            $table->text('justification_medicale')->nullable();

            // Validation
            $table->enum('statut', [
                'en_attente',
                'validee',
                'rejetee',
                'partielle',
                'expiree',
                'annulee',
                'utilisee'
            ])->default('en_attente');
            $table->string('numero_autorisation')->nullable();
            $table->uuid('validee_par')->nullable();
            $table->timestamp('validee_at')->nullable();
            $table->text('motif_rejet')->nullable();
            $table->json('elements_rejetes')->nullable();

            // Délais et validité
            $table->dateTime('date_demande');
            $table->dateTime('date_reponse')->nullable();
            $table->integer('delai_traitement')->nullable(); // En heures
            $table->date('valide_du');
            $table->date('valide_au');
            $table->boolean('prolongeable')->default(false);

            // Utilisation
            $table->boolean('utilisee')->default(false);
            $table->timestamp('utilisee_at')->nullable();
            $table->decimal('montant_utilise', 10, 2)->default(0);
            $table->decimal('montant_restant', 10, 2)->nullable();

            // Documents
            $table->json('documents_joints')->nullable();
            $table->string('demande_pdf')->nullable();
            $table->string('accord_pdf')->nullable();
            $table->boolean('original_requis')->default(false);

            // Tiers payant
            $table->boolean('tiers_payant')->default(true);
            $table->enum('mode_reglement', ['virement', 'cheque', 'especes'])->nullable();
            $table->string('reference_reglement')->nullable();
            $table->date('date_reglement')->nullable();

            // Communication
            $table->string('canal_demande')->default('web'); // web, app, email, courrier
            $table->string('canal_reponse')->nullable();
            $table->boolean('notification_envoyee')->default(false);

            // Notes
            $table->text('notes_assureur')->nullable();
            $table->text('notes_prestataire')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Index
            $table->index('numero_pec');
            $table->index(['patient_id', 'date_demande']);
            $table->index(['assureur_id', 'statut']);
            $table->index(['prestataire_id', 'date_demande']);
            $table->index('facture_id');
            $table->index('statut');
            $table->index(['valide_du', 'valide_au']);

            // Foreign keys
            $table->foreign('contrat_id')->references('id')->on('contrats_assurance')->restrictOnDelete();
            $table->foreign('patient_id')->references('id')->on('users')->restrictOnDelete();
            $table->foreign('assureur_id')->references('id')->on('compagnies_assurance')->restrictOnDelete();
            $table->foreign('prestataire_id')->references('id')->on('users')->restrictOnDelete();
            $table->foreign('pec_initiale_id')->references('id')->on('prises_en_charge')->nullOnDelete();
            $table->foreign('validee_par')->references('id')->on('users')->nullOnDelete();
        });

        // Table de suivi des remboursements
        Schema::create('remboursements_assurance', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('numero_remboursement')->unique();
            $table->uuid('pec_id');
            $table->uuid('facture_id');
            $table->uuid('assureur_id');
            $table->uuid('beneficiaire_id'); // Patient ou prestataire

            // Type
            $table->enum('type', ['patient', 'prestataire', 'mixte']);
            $table->enum('mode', ['virement', 'cheque', 'mobile_money']);

            // Montants
            $table->decimal('montant_facture', 10, 2);
            $table->decimal('montant_remboursable', 10, 2);
            $table->decimal('montant_rembourse', 10, 2);
            $table->decimal('montant_rejete', 10, 2)->default(0);

            // Statut
            $table->enum('statut', [
                'en_attente',
                'en_traitement',
                'approuve',
                'paye',
                'rejete',
                'partiel'
            ])->default('en_attente');

            // Dates
            $table->date('date_demande');
            $table->date('date_traitement')->nullable();
            $table->date('date_paiement')->nullable();
            $table->integer('delai_traitement')->nullable(); // En jours

            // Paiement
            $table->string('reference_paiement')->nullable();
            $table->string('compte_beneficiaire')->nullable();
            $table->string('preuve_paiement')->nullable();

            // Rejet
            $table->text('motif_rejet')->nullable();
            $table->json('lignes_rejetees')->nullable();

            // Documents
            $table->json('documents')->nullable();
            $table->string('bordereau_pdf')->nullable();

            $table->timestamps();

            // Index
            $table->index('numero_remboursement');
            $table->index(['assureur_id', 'statut']);
            $table->index(['beneficiaire_id', 'date_demande']);
            $table->index('statut');

            // Foreign keys
            $table->foreign('pec_id')->references('id')->on('prises_en_charge')->restrictOnDelete();
         //   $table->foreign('facture_id')->references('id')->on('factures')->restrictOnDelete();
            $table->foreign('assureur_id')->references('id')->on('compagnies_assurance')->restrictOnDelete();
            $table->foreign('beneficiaire_id')->references('id')->on('users')->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('remboursements_assurance');
        Schema::dropIfExists('prises_en_charge');
        Schema::dropIfExists('contrats_assurance');
        Schema::dropIfExists('compagnies_assurance');
    }
};
