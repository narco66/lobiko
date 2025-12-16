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
        // Table des devis
        Schema::create('devis', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('numero_devis')->unique();
            $table->uuid('patient_id');
            $table->uuid('praticien_id');
            $table->uuid('structure_id')->nullable();

            // Référence
            $table->uuid('consultation_id')->nullable();
            $table->uuid('rendez_vous_id')->nullable();

            // Montants
            $table->decimal('montant_ht', 10, 2);
            $table->decimal('montant_tva', 10, 2)->default(0);
            $table->decimal('montant_ttc', 10, 2);
            $table->decimal('montant_remise', 10, 2)->default(0);
            $table->decimal('montant_majoration', 10, 2)->default(0);
            $table->decimal('montant_final', 10, 2);

            // Assurance
            $table->uuid('contrat_assurance_id')->nullable();
            $table->decimal('montant_assurance', 10, 2)->default(0);
            $table->decimal('reste_a_charge', 10, 2);
            $table->boolean('simulation_pec')->default(false);
            $table->json('detail_couverture')->nullable();

            // Validité
            $table->date('date_emission');
            $table->date('date_validite');
            $table->integer('duree_validite')->default(30); // En jours

            // Statut
            $table->enum('statut', [
                'brouillon',
                'emis',
                'envoye',
                'accepte',
                'refuse',
                'expire',
                'converti'
            ])->default('brouillon');

            // Acceptation
            $table->boolean('accepte_patient')->default(false);
            $table->timestamp('accepte_patient_at')->nullable();
            $table->string('signature_patient')->nullable();
            $table->text('motif_refus')->nullable();

            // Conversion
            $table->boolean('converti_facture')->default(false);
            $table->uuid('facture_id')->nullable();
            $table->timestamp('converti_at')->nullable();

            // Documents et notes
            $table->string('devis_pdf')->nullable();
            $table->text('notes_internes')->nullable();
            $table->text('conditions_particulieres')->nullable();
            $table->text('mentions_legales')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Index
            $table->index('numero_devis');
            $table->index(['patient_id', 'date_emission']);
            $table->index(['praticien_id', 'date_emission']);
            $table->index('statut');
            $table->index('date_validite');

            // Foreign keys
            $table->foreign('patient_id')->references('id')->on('users')->restrictOnDelete();
            $table->foreign('praticien_id')->references('id')->on('users')->restrictOnDelete();
            $table->foreign('structure_id')->references('id')->on('structures_medicales')->nullOnDelete();
            $table->foreign('consultation_id')->references('id')->on('consultations')->nullOnDelete();
            $table->foreign('rendez_vous_id')->references('id')->on('rendez_vous')->nullOnDelete();
            $table->foreign('contrat_assurance_id')->references('id')->on('contrats_assurance')->nullOnDelete();
        });

        // Table des lignes de devis
        Schema::create('devis_lignes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('devis_id');
            $table->integer('ordre')->default(1);

            // Type et référence
            $table->enum('type', ['acte', 'produit', 'forfait', 'frais']);
            $table->uuid('element_id')->nullable();
            $table->string('code');
            $table->string('libelle');
            $table->text('description')->nullable();

            // Quantités et montants
            $table->integer('quantite');
            $table->decimal('prix_unitaire', 10, 2);
            $table->decimal('montant_ht', 10, 2);
            $table->decimal('taux_tva', 5, 2)->default(0);
            $table->decimal('montant_tva', 10, 2)->default(0);
            $table->decimal('montant_ttc', 10, 2);

            // Remises et majorations
            $table->decimal('taux_remise', 5, 2)->default(0);
            $table->decimal('montant_remise', 10, 2)->default(0);
            $table->decimal('taux_majoration', 5, 2)->default(0);
            $table->decimal('montant_majoration', 10, 2)->default(0);
            $table->decimal('montant_final', 10, 2);

            // Assurance
            $table->boolean('remboursable')->default(true);
            $table->decimal('taux_couverture', 5, 2)->default(0);
            $table->decimal('montant_couvert', 10, 2)->default(0);
            $table->decimal('reste_a_charge', 10, 2);

            $table->timestamps();

            // Index
            $table->index(['devis_id', 'ordre']);
            $table->index('element_id');

            // Foreign keys
            $table->foreign('devis_id')->references('id')->on('devis')->cascadeOnDelete();
        });

        // Table des factures
        Schema::create('factures', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('numero_facture')->unique();
            $table->uuid('patient_id');
            $table->uuid('praticien_id');
            $table->uuid('structure_id')->nullable();

            // Références
            $table->uuid('devis_id')->nullable();
            $table->uuid('consultation_id')->nullable();
            $table->uuid('ordonnance_id')->nullable();
            $table->uuid('commande_pharmacie_id')->nullable();

            // Type
            $table->enum('type', ['consultation', 'pharmacie', 'hospitalisation', 'analyse', 'imagerie', 'autre']);
            $table->enum('nature', ['normale', 'avoir', 'rectificative']);
            $table->uuid('facture_origine_id')->nullable(); // Si avoir ou rectificative

            // Montants
            $table->decimal('montant_ht', 10, 2);
            $table->decimal('montant_tva', 10, 2)->default(0);
            $table->decimal('montant_ttc', 10, 2);
            $table->decimal('montant_remise', 10, 2)->default(0);
            $table->decimal('montant_majoration', 10, 2)->default(0);
            $table->decimal('montant_final', 10, 2);

            // Multi-payeurs
            $table->decimal('part_patient', 10, 2)->default(0);
            $table->decimal('part_assurance', 10, 2)->default(0);
            $table->decimal('part_subvention', 10, 2)->default(0);
            $table->json('repartition_payeurs')->nullable();

            // Prise en charge
            $table->uuid('pec_id')->nullable();
            $table->boolean('tiers_payant')->default(false);
            $table->decimal('montant_pec', 10, 2)->default(0);
            $table->decimal('reste_a_charge', 10, 2);

            // Dates
            $table->date('date_facture');
            $table->date('date_echeance');
            $table->integer('delai_paiement')->default(30); // En jours

            // Paiement
            $table->enum('statut_paiement', [
                'en_attente',
                'partiel',
                'paye',
                'impaye',
                'annule',
                'rembourse'
            ])->default('en_attente');
            $table->decimal('montant_paye', 10, 2)->default(0);
            $table->decimal('montant_restant', 10, 2);
            $table->date('date_dernier_paiement')->nullable();
            $table->integer('nombre_paiements')->default(0);

            // Relances
            $table->integer('nombre_relances')->default(0);
            $table->date('derniere_relance')->nullable();
            $table->date('prochaine_relance')->nullable();

            // Documents
            $table->string('facture_pdf')->nullable();
            $table->boolean('originale_remise')->default(false);
            $table->timestamp('originale_remise_at')->nullable();

            // Comptabilité
            $table->boolean('comptabilisee')->default(false);
            $table->timestamp('comptabilisee_at')->nullable();
            $table->string('numero_piece_comptable')->nullable();
            $table->string('journal_comptable')->nullable();

            // Notes
            $table->text('notes_internes')->nullable();
            $table->text('mentions_legales')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Index
            $table->index('numero_facture');
            $table->index(['patient_id', 'date_facture']);
            $table->index(['praticien_id', 'date_facture']);
            $table->index(['structure_id', 'date_facture']);
            $table->index('statut_paiement');
            $table->index('date_echeance');
            $table->index('pec_id');

            // Foreign keys
            $table->foreign('patient_id')->references('id')->on('users')->restrictOnDelete();
            $table->foreign('praticien_id')->references('id')->on('users')->restrictOnDelete();
            $table->foreign('structure_id')->references('id')->on('structures_medicales')->nullOnDelete();
            $table->foreign('devis_id')->references('id')->on('devis')->nullOnDelete();
            $table->foreign('consultation_id')->references('id')->on('consultations')->nullOnDelete();
            $table->foreign('ordonnance_id')->references('id')->on('ordonnances')->nullOnDelete();
            $table->foreign('commande_pharmacie_id')->references('id')->on('commandes_pharmacie')->nullOnDelete();
            $table->foreign('pec_id')->references('id')->on('prises_en_charge')->nullOnDelete();
            $table->foreign('facture_origine_id')->references('id')->on('factures')->nullOnDelete();
        });

        // Table des lignes de facture
        Schema::create('facture_lignes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('facture_id');
            $table->integer('ordre')->default(1);

            // Type et référence
            $table->enum('type', ['acte', 'produit', 'forfait', 'frais']);
            $table->uuid('element_id')->nullable();
            $table->string('code');
            $table->string('libelle');
            $table->text('description')->nullable();

            // Quantités et montants
            $table->integer('quantite');
            $table->decimal('prix_unitaire', 10, 2);
            $table->decimal('montant_ht', 10, 2);
            $table->decimal('taux_tva', 5, 2)->default(0);
            $table->decimal('montant_tva', 10, 2)->default(0);
            $table->decimal('montant_ttc', 10, 2);

            // Remises et majorations
            $table->decimal('taux_remise', 5, 2)->default(0);
            $table->decimal('montant_remise', 10, 2)->default(0);
            $table->decimal('taux_majoration', 5, 2)->default(0);
            $table->decimal('montant_majoration', 10, 2)->default(0);
            $table->decimal('montant_final', 10, 2);

            // Répartition payeurs
            $table->decimal('part_patient', 10, 2)->default(0);
            $table->decimal('part_assurance', 10, 2)->default(0);
            $table->decimal('part_subvention', 10, 2)->default(0);

            $table->timestamps();

            // Index
            $table->index(['facture_id', 'ordre']);
            $table->index('element_id');

            // Foreign keys
            $table->foreign('facture_id')->references('id')->on('factures')->cascadeOnDelete();
        });

        // Table des paiements
        Schema::create('paiements', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('numero_paiement')->unique();
            $table->uuid('facture_id');
            $table->uuid('payeur_id');
            $table->uuid('commande_id')->nullable(); // Commandes pharmacie
            $table->uuid('reference_id')->nullable(); // Autre entité (commande pharmaceutique, etc.)
            $table->string('type_reference')->nullable();
            $table->enum('type_payeur', ['patient', 'assurance', 'subvention']);

            // Mode de paiement
            $table->enum('mode_paiement', [
                'especes',
                'carte_bancaire',
                'virement',
                'cheque',
                'mobile_money_airtel',
                'mobile_money_mtn',
                'mobile_money_orange',
                'mobile_money_moov',
                'paypal',
                'voucher'
            ]);

            // Montants
            $table->decimal('montant', 10, 2);
            $table->string('devise', 3)->default('XAF');
            $table->decimal('taux_change', 10, 4)->default(1);
            $table->decimal('montant_devise_locale', 10, 2);
            $table->decimal('frais_transaction', 10, 2)->default(0);
            $table->decimal('montant_net', 10, 2);

            // Référence et statut
            $table->string('reference_transaction')->unique();
            $table->string('reference_passerelle')->nullable();
            $table->enum('statut', [
                'initie',
                'en_cours',
                'confirme',
                'echoue',
                'annule',
                'rembourse',
                'timeout'
            ])->default('initie');

            // Idempotence
            $table->string('idempotence_key')->unique();
            $table->integer('tentatives')->default(1);
            $table->timestamp('derniere_tentative')->nullable();

            // Passerelle de paiement
            $table->string('passerelle')->nullable();
            $table->json('reponse_passerelle')->nullable();
            $table->string('code_autorisation')->nullable();
            $table->string('code_erreur')->nullable();
            $table->text('message_erreur')->nullable();

            // Dates
            $table->timestamp('date_initiation');
            $table->timestamp('date_confirmation')->nullable();
            $table->timestamp('date_annulation')->nullable();

            // Remboursement
            $table->boolean('remboursable')->default(true);
            $table->decimal('montant_rembourse', 10, 2)->default(0);
            $table->timestamp('date_remboursement')->nullable();
            $table->string('reference_remboursement')->nullable();

            // Validation
            $table->boolean('valide')->default(false);
            $table->uuid('valide_par')->nullable();
            $table->timestamp('valide_at')->nullable();

            // Agent (pour paiement espèces)
            $table->uuid('agent_id')->nullable();
            $table->string('code_agent')->nullable();
            $table->string('lieu_paiement')->nullable();

            // Documents
            $table->string('recu_pdf')->nullable();
            $table->string('preuve_paiement')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Index
            $table->index('numero_paiement');
            $table->index('reference_transaction');
            $table->index('idempotence_key');
            $table->index(['facture_id', 'statut']);
            $table->index(['commande_id', 'statut']);
            $table->index(['reference_id', 'type_reference']);
            $table->index(['payeur_id', 'date_initiation']);
            $table->index('statut');
            $table->index('mode_paiement');

            // Foreign keys
            $table->foreign('facture_id')->references('id')->on('factures')->restrictOnDelete();
            $table->foreign('payeur_id')->references('id')->on('users')->restrictOnDelete();
            $table->foreign('valide_par')->references('id')->on('users')->nullOnDelete();
            $table->foreign('agent_id')->references('id')->on('users')->nullOnDelete();
        });

        // Table des reversements aux praticiens
        Schema::create('reversements', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('numero_reversement')->unique();
            $table->uuid('beneficiaire_id');
            $table->enum('type_beneficiaire', ['praticien', 'structure']);

            // Période
            $table->date('periode_debut');
            $table->date('periode_fin');
            $table->string('mois_annee'); // Format: 2025-01

            // Montants
            $table->decimal('montant_brut', 10, 2);
            $table->decimal('commission_plateforme', 10, 2);
            $table->decimal('taux_commission', 5, 2);
            $table->decimal('retenues_fiscales', 10, 2)->default(0);
            $table->decimal('autres_retenues', 10, 2)->default(0);
            $table->decimal('montant_net', 10, 2);

            // Détails
            $table->integer('nombre_consultations')->default(0);
            $table->integer('nombre_actes')->default(0);
            $table->json('detail_consultations')->nullable();
            $table->json('detail_retenues')->nullable();

            // Paiement
            $table->enum('mode_paiement', ['virement', 'cheque', 'mobile_money']);
            $table->string('compte_beneficiaire')->nullable();
            $table->string('reference_paiement')->nullable();
            $table->enum('statut', [
                'calcule',
                'valide',
                'en_paiement',
                'paye',
                'rejete',
                'annule'
            ])->default('calcule');

            // Dates
            $table->date('date_calcul');
            $table->date('date_validation')->nullable();
            $table->date('date_paiement_prevu');
            $table->date('date_paiement_effectif')->nullable();

            // Validation
            $table->uuid('valide_par')->nullable();
            $table->timestamp('valide_at')->nullable();
            $table->uuid('paye_par')->nullable();
            $table->timestamp('paye_at')->nullable();

            // Documents
            $table->string('bordereau_pdf')->nullable();
            $table->string('preuve_paiement')->nullable();

            // Notes
            $table->text('notes')->nullable();
            $table->text('motif_rejet')->nullable();

            $table->timestamps();

            // Index
            $table->index('numero_reversement');
            $table->index(['beneficiaire_id', 'mois_annee']);
            $table->index(['periode_debut', 'periode_fin']);
            $table->index('statut');

            // Foreign keys
            $table->foreign('beneficiaire_id')->references('id')->on('users')->restrictOnDelete();
            $table->foreign('valide_par')->references('id')->on('users')->nullOnDelete();
            $table->foreign('paye_par')->references('id')->on('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reversements');
        Schema::dropIfExists('paiements');
        Schema::dropIfExists('facture_lignes');
        Schema::dropIfExists('factures');
        Schema::dropIfExists('devis_lignes');
        Schema::dropIfExists('devis');
    }
};
