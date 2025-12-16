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
        // Table des rendez-vous
        Schema::create('rendez_vous', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('numero_rdv')->unique();
            $table->uuid('patient_id');
            $table->uuid('professionnel_id');
            $table->uuid('structure_id')->nullable();

            // Date et heure
            $table->dateTime('date_heure');
            $table->integer('duree_prevue')->default(30); // En minutes
            $table->dateTime('date_heure_fin')->nullable();

            // Type et modalité
            $table->enum('type', ['consultation', 'controle', 'urgence', 'vaccination', 'examen']);
            $table->enum('modalite', ['presentiel', 'teleconsultation', 'domicile']);
            $table->string('specialite')->nullable();
            $table->uuid('acte_id')->nullable(); // Acte médical prévu

            // Motif et symptômes
            $table->text('motif');
            $table->text('symptomes')->nullable();
            $table->enum('urgence_niveau', ['faible', 'moyen', 'eleve', 'vital'])->default('faible');
            $table->json('antecedents_signales')->nullable();

            // Statut
            $table->enum('statut', [
                'en_attente',
                'confirme',
                'en_cours',
                'termine',
                'annule',
                'reporte',
                'no_show'
            ])->default('en_attente');

            // Confirmation
            $table->boolean('confirme_patient')->default(false);
            $table->timestamp('confirme_patient_at')->nullable();
            $table->boolean('confirme_praticien')->default(false);
            $table->timestamp('confirme_praticien_at')->nullable();

            // Rappels
            $table->boolean('rappel_envoye')->default(false);
            $table->timestamp('rappel_envoye_at')->nullable();
            $table->integer('nombre_rappels')->default(0);

            // Annulation/Report
            $table->string('raison_annulation')->nullable();
            $table->uuid('annule_par')->nullable();
            $table->timestamp('annule_at')->nullable();
            $table->uuid('reporte_de')->nullable(); // Si c'est un report
            $table->uuid('reporte_vers')->nullable(); // Nouveau RDV si reporté

            // Téléconsultation
            $table->string('lien_teleconsultation')->nullable();
            $table->string('room_id')->nullable();
            $table->string('session_id')->nullable();
            $table->timestamp('debut_appel')->nullable();
            $table->timestamp('fin_appel')->nullable();
            $table->integer('duree_appel')->nullable(); // En secondes

            // Notes et instructions
            $table->text('notes_patient')->nullable();
            $table->text('instructions_preparation')->nullable();
            $table->json('documents_requis')->nullable();

            // Tarification
            $table->decimal('montant_prevu', 10, 2)->nullable();
            $table->boolean('paiement_confirme')->default(false);

            $table->timestamps();
            $table->softDeletes();

            // Index
            $table->index('numero_rdv');
            $table->index(['patient_id', 'date_heure']);
            $table->index(['professionnel_id', 'date_heure']);
            $table->index(['structure_id', 'date_heure']);
            $table->index('statut');
            $table->index('modalite');
            $table->index(['date_heure', 'statut']);

            // Foreign keys
            $table->foreign('patient_id')->references('id')->on('users')->restrictOnDelete();
            $table->foreign('professionnel_id')->references('id')->on('users')->restrictOnDelete();
            $table->foreign('structure_id')->references('id')->on('structures_medicales')->nullOnDelete();
            $table->foreign('acte_id')->references('id')->on('actes_medicaux')->nullOnDelete();
            $table->foreign('annule_par')->references('id')->on('users')->nullOnDelete();
            $table->foreign('reporte_de')->references('id')->on('rendez_vous')->nullOnDelete();
            $table->foreign('reporte_vers')->references('id')->on('rendez_vous')->nullOnDelete();
        });

        // Table des consultations
        Schema::create('consultations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('numero_consultation')->unique();
            $table->uuid('rendez_vous_id')->nullable();
            $table->uuid('patient_id');
            $table->uuid('professionnel_id');
            $table->uuid('structure_id')->nullable();

            // Timing
            $table->dateTime('date_consultation');
            $table->time('heure_debut');
            $table->time('heure_fin')->nullable();
            $table->integer('duree_effective')->nullable(); // En minutes

            // Type et modalité
            $table->enum('type', ['generale', 'specialisee', 'urgence', 'controle', 'preventive']);
            $table->enum('modalite', ['presentiel', 'teleconsultation', 'domicile']);

            // Anamnèse
            $table->text('motif_consultation');
            $table->text('histoire_maladie')->nullable();
            $table->json('symptomes_declares')->nullable();
            $table->date('debut_symptomes')->nullable();

            // Examen clinique
            $table->json('signes_vitaux')->nullable(); // TA, FC, FR, T°, Poids, Taille, IMC
            $table->text('examen_general')->nullable();
            $table->text('examen_par_appareil')->nullable();
            $table->json('examens_complementaires_demandes')->nullable();

            // Diagnostic
            $table->text('diagnostic_principal');
            $table->text('diagnostics_secondaires')->nullable();
            $table->string('code_cim10')->nullable(); // Classification internationale des maladies
            $table->enum('certitude_diagnostic', ['confirme', 'probable', 'suspect'])->default('probable');

            // Prise en charge
            $table->text('conduite_a_tenir');
            $table->json('actes_realises')->nullable();
            $table->json('prescriptions')->nullable(); // Médicaments prescrits
            $table->json('examens_prescrits')->nullable();
            $table->boolean('ordonnance_delivree')->default(false);
            $table->boolean('arret_travail')->default(false);
            $table->integer('duree_arret_travail')->nullable(); // En jours
            $table->boolean('certificat_medical')->default(false);

            // Orientation et suivi
            $table->boolean('orientation_specialiste')->default(false);
            $table->string('specialiste_oriente')->nullable();
            $table->boolean('hospitalisation')->default(false);
            $table->string('structure_hospitalisation')->nullable();
            $table->date('prochain_rdv')->nullable();
            $table->text('recommandations')->nullable();

            // Pronostic
            $table->enum('pronostic', ['bon', 'reserve', 'sombre'])->nullable();
            $table->text('evolution_attendue')->nullable();

            // Documents
            $table->json('documents_joints')->nullable(); // Radios, analyses, etc.
            $table->string('compte_rendu_pdf')->nullable();

            // Validation
            $table->boolean('valide')->default(false);
            $table->timestamp('valide_at')->nullable();
            $table->uuid('valide_par')->nullable();

            // Notes privées
            $table->text('notes_privees')->nullable(); // Notes du praticien non visibles patient

            $table->timestamps();
            $table->softDeletes();

            // Index
            $table->index('numero_consultation');
            $table->index(['patient_id', 'date_consultation']);
            $table->index(['professionnel_id', 'date_consultation']);
            $table->index('structure_id');
            $table->index('type');
            $table->index('modalite');
            $table->index('code_cim10');

            // Foreign keys
            $table->foreign('rendez_vous_id')->references('id')->on('rendez_vous')->nullOnDelete();
            $table->foreign('patient_id')->references('id')->on('users')->restrictOnDelete();
            $table->foreign('professionnel_id')->references('id')->on('users')->restrictOnDelete();
            $table->foreign('structure_id')->references('id')->on('structures_medicales')->nullOnDelete();
            $table->foreign('valide_par')->references('id')->on('users')->nullOnDelete();
        });

        // Table du dossier médical électronique (DME)
        Schema::create('dossiers_medicaux', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('patient_id')->unique();
            $table->string('numero_dossier')->unique();

            // Informations médicales de base
            $table->string('groupe_sanguin')->nullable();
            $table->string('rhesus')->nullable();
            $table->json('allergies')->nullable();
            $table->json('antecedents_medicaux')->nullable();
            $table->json('antecedents_chirurgicaux')->nullable();
            $table->json('antecedents_familiaux')->nullable();
            $table->json('vaccinations')->nullable();

            // Habitudes de vie
            $table->enum('tabac', ['non', 'occasionnel', 'regulier', 'ancien'])->default('non');
            $table->integer('cigarettes_jour')->nullable();
            $table->enum('alcool', ['non', 'occasionnel', 'regulier', 'excessif'])->default('non');
            $table->enum('activite_physique', ['sedentaire', 'legere', 'moderee', 'intense'])->default('moderee');
            $table->text('regime_alimentaire')->nullable();

            // Traitements en cours
            $table->json('traitements_chroniques')->nullable();
            $table->json('medicaments_actuels')->nullable();
            $table->date('derniere_mise_jour_traitement')->nullable();

            // Femmes
            $table->date('date_dernieres_regles')->nullable();
            $table->boolean('enceinte')->default(false);
            $table->integer('nombre_grossesses')->nullable();
            $table->integer('nombre_enfants')->nullable();
            $table->string('contraception')->nullable();

            // Constantes habituelles
            $table->integer('tension_habituelle_sys')->nullable();
            $table->integer('tension_habituelle_dia')->nullable();
            $table->decimal('poids_habituel', 5, 2)->nullable();
            $table->integer('taille_cm')->nullable();
            $table->decimal('imc', 4, 2)->nullable();

            // Contact d'urgence
            $table->string('contact_urgence_nom')->nullable();
            $table->string('contact_urgence_telephone')->nullable();
            $table->string('contact_urgence_lien')->nullable();

            // Confidentialité
            $table->json('acces_autorises')->nullable(); // Liste des praticiens autorisés
            $table->boolean('partage_autorise')->default(true);
            $table->json('elements_caches')->nullable(); // Éléments masqués

            // Statut
            $table->boolean('actif')->default(true);
            $table->timestamp('derniere_consultation')->nullable();
            $table->integer('nombre_consultations')->default(0);

            $table->timestamps();

            // Index
            $table->index('patient_id');
            $table->index('numero_dossier');

            // Foreign keys
            $table->foreign('patient_id')->references('id')->on('users')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dossiers_medicaux');
        Schema::dropIfExists('consultations');
        Schema::dropIfExists('rendez_vous');
    }
};
