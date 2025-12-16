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
        // Table des actes médicaux
        Schema::create('actes_medicaux', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('code_acte')->unique();
            $table->string('libelle');
            $table->text('description')->nullable();
            $table->string('categorie'); // consultation, imagerie, analyse, intervention, etc.
            $table->string('specialite')->nullable();
            $table->decimal('tarif_base', 10, 2);
            $table->integer('duree_prevue')->nullable(); // En minutes
            $table->boolean('urgence_possible')->default(false);
            $table->boolean('teleconsultation_possible')->default(false);
            $table->boolean('domicile_possible')->default(false);

            // Prérequis et restrictions
            $table->json('prerequis')->nullable(); // Examens préalables nécessaires
            $table->json('contre_indications')->nullable();
            $table->integer('age_minimum')->nullable();
            $table->integer('age_maximum')->nullable();
            $table->enum('sexe_requis', ['M', 'F', 'Tous'])->default('Tous');

            // Matériel nécessaire
            $table->json('equipements_requis')->nullable();
            $table->json('consommables')->nullable();

            // Tarification
            $table->decimal('tarif_urgence', 10, 2)->nullable();
            $table->decimal('tarif_weekend', 10, 2)->nullable();
            $table->decimal('tarif_nuit', 10, 2)->nullable();
            $table->decimal('tarif_domicile', 10, 2)->nullable();

            // Remboursement assurance
            $table->boolean('remboursable')->default(true);
            $table->decimal('taux_remboursement_base', 5, 2)->default(70);
            $table->string('code_securite_sociale')->nullable();

            // Statut
            $table->boolean('actif')->default(true);
            $table->date('date_debut_validite')->nullable();
            $table->date('date_fin_validite')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Index
            $table->index('code_acte');
            $table->index('categorie');
            $table->index('specialite');
            $table->index('actif');
        });

        // Table des produits pharmaceutiques
        Schema::create('produits_pharmaceutiques', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('code_produit')->unique();
            $table->string('dci'); // Dénomination Commune Internationale
            $table->string('nom_commercial');
            $table->string('laboratoire');
            $table->string('forme'); // comprimé, sirop, injectable, etc.
            $table->string('dosage');
            $table->string('conditionnement'); // boîte de X
            $table->string('voie_administration'); // orale, injectable, topique, etc.

            // Classification
            $table->string('classe_therapeutique');
            $table->string('famille');
            $table->boolean('generique')->default(false);
            $table->string('princeps')->nullable(); // Si générique, référence au princeps

            // Prix et stock
            $table->decimal('prix_unitaire', 10, 2);
            $table->decimal('prix_boite', 10, 2);
            $table->integer('stock_minimum')->default(10);
            $table->integer('stock_alerte')->default(20);

            // Prescription
            $table->boolean('prescription_obligatoire')->default(true);
            $table->boolean('stupefiant')->default(false);
            $table->boolean('liste_i')->default(false);
            $table->boolean('liste_ii')->default(false);
            $table->integer('duree_traitement_max')->nullable(); // En jours

            // Conservation
            $table->string('conditions_conservation')->nullable();
            $table->integer('temperature_min')->nullable();
            $table->integer('temperature_max')->nullable();
            $table->date('date_peremption')->nullable();
            $table->string('numero_lot')->nullable();

            // Remboursement
            $table->boolean('remboursable')->default(true);
            $table->decimal('taux_remboursement', 5, 2)->default(65);
            $table->string('code_cip')->nullable(); // Code identifiant de présentation
            $table->string('code_ucd')->nullable(); // Unité commune de dispensation

            // Contre-indications et interactions
            $table->json('contre_indications')->nullable();
            $table->json('interactions_medicamenteuses')->nullable();
            $table->json('effets_secondaires')->nullable();
            $table->json('precautions_emploi')->nullable();

            // Images et documents
            $table->string('image_produit')->nullable();
            $table->string('notice_pdf')->nullable();
            $table->string('rcp_pdf')->nullable(); // Résumé caractéristiques produit

            // Statut
            $table->boolean('disponible')->default(true);
            $table->boolean('rupture_stock')->default(false);
            $table->date('date_rupture')->nullable();
            $table->date('date_retour_prevue')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Index
            $table->index('code_produit');
            $table->index('dci');
            $table->index('nom_commercial');
            $table->index('classe_therapeutique');
            $table->index('disponible');
            $table->index('prescription_obligatoire');
        });

        // Table des grilles tarifaires
        Schema::create('grilles_tarifaires', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('nom_grille');
            $table->enum('type_client', ['public', 'prive', 'assure', 'indigent']);
            $table->enum('zone', ['urbain', 'rural', 'periurbain']);
            $table->uuid('structure_id')->nullable(); // Null = grille générale

            // Applicable à
            $table->enum('applicable_a', ['acte', 'produit', 'tous']);
            $table->uuid('element_id')->nullable(); // ID de l'acte ou produit spécifique

            // Tarification
            $table->decimal('coefficient_multiplicateur', 5, 2)->default(1);
            $table->decimal('majoration_fixe', 10, 2)->default(0);
            $table->decimal('taux_remise', 5, 2)->default(0);
            $table->decimal('tva_applicable', 5, 2)->default(0);

            // Conditions
            $table->integer('quantite_min')->nullable();
            $table->integer('quantite_max')->nullable();
            $table->decimal('montant_min', 10, 2)->nullable();
            $table->decimal('montant_max', 10, 2)->nullable();

            // Validité
            $table->date('date_debut');
            $table->date('date_fin')->nullable();
            $table->boolean('actif')->default(true);
            $table->integer('priorite')->default(0); // Pour gérer les conflits

            $table->timestamps();

            // Index
            $table->index(['type_client', 'zone']);
            $table->index('structure_id');
            $table->index('element_id');
            $table->index(['date_debut', 'date_fin']);
            $table->index('actif');

            // Foreign keys
            $table->foreign('structure_id')->references('id')->on('structures_medicales')->cascadeOnDelete();
        });

        // Table des forfaits et packs
        Schema::create('forfaits', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('code_forfait')->unique();
            $table->string('nom_forfait');
            $table->text('description');
            $table->string('categorie'); // suivi_grossesse, diabete, hypertension, etc.
            $table->decimal('prix_forfait', 10, 2);
            $table->integer('duree_validite')->nullable(); // En jours
            $table->integer('nombre_seances')->nullable();

            // Composition
            $table->json('actes_inclus')->nullable(); // Liste des actes inclus
            $table->json('produits_inclus')->nullable(); // Liste des produits inclus
            $table->json('examens_inclus')->nullable(); // Liste des examens inclus

            // Conditions
            $table->integer('age_minimum')->nullable();
            $table->integer('age_maximum')->nullable();
            $table->enum('sexe_requis', ['M', 'F', 'Tous'])->default('Tous');
            $table->json('pathologies_cibles')->nullable();

            // Remboursement
            $table->boolean('remboursable')->default(true);
            $table->decimal('taux_remboursement', 5, 2)->default(80);

            $table->boolean('actif')->default(true);
            $table->timestamps();
            $table->softDeletes();

            // Index
            $table->index('code_forfait');
            $table->index('categorie');
            $table->index('actif');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('forfaits');
        Schema::dropIfExists('grilles_tarifaires');
        Schema::dropIfExists('produits_pharmaceutiques');
        Schema::dropIfExists('actes_medicaux');
    }
};
