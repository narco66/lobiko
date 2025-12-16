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
        // Table du plan comptable
        Schema::create('plan_comptable', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('numero_compte', 10)->unique();
            $table->string('libelle');
            $table->text('description')->nullable();
            $table->enum('classe', ['1', '2', '3', '4', '5', '6', '7']); // Classes comptables
            $table->enum('type', ['actif', 'passif', 'charge', 'produit']);
            $table->string('compte_parent')->nullable();
            $table->boolean('auxiliaire')->default(false);
            $table->boolean('lettrable')->default(false);
            $table->boolean('pointable')->default(false);
            $table->boolean('actif')->default(true);
            $table->timestamps();

            // Index
            $table->index('numero_compte');
            $table->index('classe');
            $table->index('type');
            $table->index('actif');
        });

        // Table des journaux comptables
        Schema::create('journaux_comptables', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('code_journal', 10)->unique();
            $table->string('libelle');
            $table->enum('type', ['ventes', 'achats', 'banque', 'caisse', 'operations_diverses']);
            $table->string('compte_contrepartie')->nullable();
            $table->boolean('actif')->default(true);
            $table->timestamps();

            // Index
            $table->index('code_journal');
            $table->index('type');
        });

        // Table des écritures comptables
        Schema::create('ecritures_comptables', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('numero_ecriture')->unique();
            $table->uuid('journal_id');
            $table->date('date_ecriture');
            $table->string('numero_piece')->nullable();
            $table->string('libelle_ecriture');

            // Référence source
            $table->enum('type_source', ['facture', 'paiement', 'reversement', 'avoir', 'od'])->nullable();
            $table->uuid('source_id')->nullable();

            // Montants
            $table->decimal('total_debit', 12, 2);
            $table->decimal('total_credit', 12, 2);

            // Statut
            $table->enum('statut', ['brouillon', 'validee', 'cloturee'])->default('brouillon');
            $table->boolean('lettree')->default(false);
            $table->boolean('rapprochee')->default(false);

            // Validation
            $table->uuid('saisie_par');
            $table->uuid('validee_par')->nullable();
            $table->timestamp('validee_at')->nullable();

            // Période
            $table->string('exercice', 4);
            $table->string('periode', 7); // Format: 2025-01

            $table->timestamps();
            $table->softDeletes();

            // Index
            $table->index('numero_ecriture');
            $table->index(['journal_id', 'date_ecriture']);
            $table->index(['exercice', 'periode']);
            $table->index('statut');
            $table->index(['type_source', 'source_id']);

            // Foreign keys
            $table->foreign('journal_id')->references('id')->on('journaux_comptables')->restrictOnDelete();
            $table->foreign('saisie_par')->references('id')->on('users')->restrictOnDelete();
            $table->foreign('validee_par')->references('id')->on('users')->nullOnDelete();
        });

        // Table des lignes d'écritures
        Schema::create('lignes_ecritures', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('ecriture_id');
            $table->integer('numero_ligne');
            $table->string('numero_compte');
            $table->string('libelle_ligne');
            $table->decimal('debit', 12, 2)->default(0);
            $table->decimal('credit', 12, 2)->default(0);

            // Tiers
            $table->uuid('tiers_id')->nullable();
            $table->enum('type_tiers', ['patient', 'praticien', 'structure', 'assureur'])->nullable();

            // Lettrage et rapprochement
            $table->string('code_lettrage')->nullable();
            $table->date('date_lettrage')->nullable();
            $table->string('reference_rapprochement')->nullable();
            $table->date('date_rapprochement')->nullable();

            // Analytique
            $table->string('section_analytique')->nullable();
            $table->decimal('montant_analytique', 12, 2)->nullable();

            $table->timestamps();

            // Index
            $table->index(['ecriture_id', 'numero_ligne']);
            $table->index('numero_compte');
            $table->index('code_lettrage');
            $table->index(['tiers_id', 'type_tiers']);

            // Foreign keys
            $table->foreign('ecriture_id')->references('id')->on('ecritures_comptables')->cascadeOnDelete();
            $table->foreign('numero_compte')->references('numero_compte')->on('plan_comptable')->restrictOnDelete();
        });

        // Table des rapprochements bancaires
        Schema::create('rapprochements_bancaires', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('numero_rapprochement')->unique();
            $table->string('compte_bancaire');
            $table->date('date_debut');
            $table->date('date_fin');

            // Soldes
            $table->decimal('solde_debut_comptable', 12, 2);
            $table->decimal('solde_fin_comptable', 12, 2);
            $table->decimal('solde_debut_bancaire', 12, 2);
            $table->decimal('solde_fin_bancaire', 12, 2);
            $table->decimal('ecart', 12, 2);

            // Mouvements
            $table->integer('nombre_operations_comptables');
            $table->integer('nombre_operations_bancaires');
            $table->integer('nombre_rapprochees');
            $table->integer('nombre_en_suspens');

            // Détails
            $table->json('operations_rapprochees')->nullable();
            $table->json('operations_en_suspens')->nullable();
            $table->json('ecarts_identifies')->nullable();

            // Statut
            $table->enum('statut', ['en_cours', 'termine', 'valide'])->default('en_cours');
            $table->uuid('realise_par');
            $table->uuid('valide_par')->nullable();
            $table->timestamp('valide_at')->nullable();

            // Documents
            $table->string('releve_bancaire')->nullable();
            $table->string('rapport_pdf')->nullable();

            $table->timestamps();

            // Index
            $table->index('numero_rapprochement');
            $table->index(['compte_bancaire', 'date_fin']);
            $table->index('statut');

            // Foreign keys
            $table->foreign('realise_par')->references('id')->on('users')->restrictOnDelete();
            $table->foreign('valide_par')->references('id')->on('users')->nullOnDelete();
        });

        // Table des notifications
        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->string('titre');
            $table->text('message');
            $table->enum('type', [
                'rendez_vous',
                'consultation',
                'ordonnance',
                'paiement',
                'pec',
                'facture',
                'rappel',
                'alerte',
                'info',
                'promo'
            ]);
            $table->enum('priorite', ['basse', 'normale', 'haute', 'urgente'])->default('normale');

            // Canaux
            $table->boolean('sms')->default(false);
            $table->boolean('email')->default(false);
            $table->boolean('push')->default(false);
            $table->boolean('in_app')->default(true);

            // Référence
            $table->string('entite_type')->nullable();
            $table->uuid('entite_id')->nullable();
            $table->string('action_url')->nullable();

            // Envoi
            $table->timestamp('date_envoi_prevue')->nullable();
            $table->timestamp('sms_envoye_at')->nullable();
            $table->timestamp('email_envoye_at')->nullable();
            $table->timestamp('push_envoye_at')->nullable();

            // Statut
            $table->enum('statut', ['en_attente', 'envoye', 'lu', 'echoue', 'annule'])->default('en_attente');
            $table->timestamp('lu_at')->nullable();
            $table->integer('tentatives')->default(0);
            $table->text('erreur')->nullable();

            // Métadonnées
            $table->json('metadata')->nullable();
            $table->string('template')->nullable();
            $table->json('variables')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Index
            $table->index(['user_id', 'statut']);
            $table->index('type');
            $table->index('date_envoi_prevue');
            $table->index(['entite_type', 'entite_id']);

            // Foreign keys
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });

        // Table d'audit (logs)
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id')->nullable();
            $table->string('user_nom')->nullable();
            $table->string('user_role')->nullable();

            // Action
            $table->string('action'); // create, update, delete, view, login, logout, etc.
            $table->string('module'); // users, consultations, factures, paiements, etc.
            $table->string('entite_type');
            $table->uuid('entite_id')->nullable();
            $table->string('entite_nom')->nullable();

            // Détails
            $table->json('anciennes_valeurs')->nullable();
            $table->json('nouvelles_valeurs')->nullable();
            $table->json('changements')->nullable();
            $table->text('description')->nullable();

            // Contexte
            $table->string('ip_address');
            $table->string('user_agent')->nullable();
            $table->string('methode_http')->nullable();
            $table->string('url')->nullable();
            $table->json('parametres')->nullable();

            // Sécurité
            $table->enum('niveau', ['info', 'warning', 'error', 'critical'])->default('info');
            $table->boolean('sensible')->default(false); // Pour les données médicales/financières
            $table->string('hash_integrite')->nullable(); // Hash pour vérifier l'intégrité

            // Performance
            $table->integer('duree_ms')->nullable(); // Durée de l'opération en millisecondes
            $table->integer('queries_count')->nullable(); // Nombre de requêtes SQL

            // Résultat
            $table->boolean('succes')->default(true);
            $table->text('message_erreur')->nullable();
            $table->integer('code_erreur')->nullable();

            $table->timestamp('created_at')->useCurrent();

            // Index
            $table->index(['user_id', 'created_at']);
            $table->index(['module', 'action']);
            $table->index(['entite_type', 'entite_id']);
            $table->index('niveau');
            $table->index('created_at');
            $table->index('ip_address');

            // Foreign keys
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
        });

        // Table des litiges
        Schema::create('litiges', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('numero_litige')->unique();
            $table->uuid('declarant_id');
            $table->enum('type', ['medical', 'financier', 'service', 'technique', 'autre']);
            $table->enum('categorie', [
                'erreur_facturation',
                'probleme_paiement',
                'qualite_soins',
                'retard_rendez_vous',
                'erreur_prescription',
                'probleme_livraison',
                'contestation_pec',
                'autre'
            ]);

            // Entités concernées
            $table->string('entite_type');
            $table->uuid('entite_id');
            $table->uuid('praticien_concerne_id')->nullable();
            $table->uuid('structure_concernee_id')->nullable();

            // Description
            $table->string('objet');
            $table->text('description');
            $table->json('pieces_jointes')->nullable();
            $table->decimal('montant_conteste', 10, 2)->nullable();

            // Traitement
            $table->enum('statut', [
                'ouvert',
                'en_cours',
                'en_attente_info',
                'resolu',
                'rejete',
                'escalade'
            ])->default('ouvert');
            $table->enum('priorite', ['faible', 'normale', 'haute', 'critique'])->default('normale');
            $table->uuid('assigne_a')->nullable();
            $table->timestamp('assigne_at')->nullable();

            // Résolution
            $table->text('resolution')->nullable();
            $table->enum('decision', ['favorable', 'defavorable', 'partiel'])->nullable();
            $table->decimal('montant_rembourse', 10, 2)->nullable();
            $table->uuid('resolu_par')->nullable();
            $table->timestamp('resolu_at')->nullable();

            // Délais
            $table->timestamp('date_declaration');
            $table->timestamp('date_echeance')->nullable();
            $table->integer('delai_traitement')->nullable(); // En heures

            // Satisfaction
            $table->integer('note_satisfaction')->nullable();
            $table->text('commentaire_satisfaction')->nullable();

            // Historique
            $table->json('historique_statuts')->nullable();
            $table->json('correspondances')->nullable();

            $table->timestamps();

            // Index
            $table->index('numero_litige');
            $table->index(['declarant_id', 'statut']);
            $table->index(['entite_type', 'entite_id']);
            $table->index('statut');
            $table->index('priorite');
            $table->index('assigne_a');

            // Foreign keys
            $table->foreign('declarant_id')->references('id')->on('users')->restrictOnDelete();
            $table->foreign('praticien_concerne_id')->references('id')->on('users')->nullOnDelete();
            $table->foreign('structure_concernee_id')->references('id')->on('structures_medicales')->nullOnDelete();
            $table->foreign('assigne_a')->references('id')->on('users')->nullOnDelete();
            $table->foreign('resolu_par')->references('id')->on('users')->nullOnDelete();
        });

        // Table des évaluations
        Schema::create('evaluations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('evaluateur_id');
            $table->uuid('evalue_id'); // Praticien ou structure
            $table->enum('type_evalue', ['praticien', 'structure', 'service']);

            // Contexte
            $table->uuid('consultation_id')->nullable();
            $table->uuid('commande_id')->nullable();

            // Notes
            $table->integer('note_globale'); // 1-5
            $table->integer('note_professionnalisme')->nullable();
            $table->integer('note_ponctualite')->nullable();
            $table->integer('note_ecoute')->nullable();
            $table->integer('note_explications')->nullable();
            $table->integer('note_environnement')->nullable();
            $table->integer('note_rapport_qualite_prix')->nullable();

            // Commentaire
            $table->text('commentaire')->nullable();
            $table->boolean('recommande')->default(true);

            // Modération
            $table->boolean('visible')->default(true);
            $table->boolean('modere')->default(false);
            $table->uuid('modere_par')->nullable();
            $table->timestamp('modere_at')->nullable();
            $table->text('raison_moderation')->nullable();

            // Réponse
            $table->text('reponse')->nullable();
            $table->timestamp('reponse_at')->nullable();

            $table->timestamps();

            // Index
            $table->index(['evaluateur_id', 'created_at']);
            $table->index(['evalue_id', 'type_evalue']);
            $table->index('visible');
            $table->unique(['evaluateur_id', 'consultation_id']);

            // Foreign keys
            $table->foreign('evaluateur_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('consultation_id')->references('id')->on('consultations')->nullOnDelete();
            $table->foreign('commande_id')->references('id')->on('commandes_pharmacie')->nullOnDelete();
            $table->foreign('modere_par')->references('id')->on('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evaluations');
        Schema::dropIfExists('litiges');
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('rapprochements_bancaires');
        Schema::dropIfExists('lignes_ecritures');
        Schema::dropIfExists('ecritures_comptables');
        Schema::dropIfExists('journaux_comptables');
        Schema::dropIfExists('plan_comptable');
    }
};
