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
        Schema::create('structures_medicales', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('code_structure')->unique();
            $table->string('nom_structure');
            $table->enum('type_structure', [
                'cabinet',
                'clinique',
                'hopital',
                'pharmacie',
                'laboratoire',
                'centre_imagerie',
                'centre_specialise'
            ]);

            // Informations légales
            $table->string('numero_agrement')->nullable();
            $table->string('numero_fiscal')->nullable();
            $table->string('registre_commerce')->nullable();

            // Adresse et géolocalisation
            $table->string('adresse_rue');
            $table->string('adresse_quartier');
            $table->string('adresse_ville');
            $table->string('adresse_pays')->default('Gabon');
            $table->decimal('latitude', 10, 8);
            $table->decimal('longitude', 11, 8);

            // Contact
            $table->string('telephone_principal');
            $table->string('telephone_secondaire')->nullable();
            $table->string('email');
            $table->string('site_web')->nullable();

            // Horaires
            $table->json('horaires_ouverture'); // JSON avec les horaires par jour
            $table->boolean('urgences_24h')->default(false);
            $table->boolean('garde_weekend')->default(false);

            // Responsable
            $table->uuid('responsable_id');

            // Services et équipements
            $table->json('services_disponibles')->nullable(); // Liste des services offerts
            $table->json('equipements')->nullable(); // Liste des équipements disponibles
            $table->integer('nombre_lits')->nullable();
            $table->integer('nombre_salles')->nullable();
            $table->boolean('parking_disponible')->default(false);
            $table->boolean('accessible_handicapes')->default(false);

            // Assurances partenaires
            $table->json('assurances_acceptees')->nullable(); // Liste des assurances acceptées
            $table->boolean('tiers_payant')->default(false);

            // Tarification
            $table->enum('categorie_tarif', ['public', 'prive', 'conventionné'])->default('prive');
            $table->decimal('taux_majoration', 5, 2)->default(0); // Majoration sur tarifs de base

            // Statut et validation
            $table->enum('statut', ['actif', 'suspendu', 'fermé', 'en_validation'])->default('en_validation');
            $table->boolean('verified')->default(false);
            $table->timestamp('verified_at')->nullable();
            $table->uuid('verified_by')->nullable();

            // Documents
            $table->string('logo')->nullable();
            $table->string('photo_facade')->nullable();
            $table->json('galerie_photos')->nullable();
            $table->string('document_agrement')->nullable();

            // Métriques
            $table->decimal('note_moyenne', 3, 2)->default(0);
            $table->integer('nombre_evaluations')->default(0);
            $table->integer('nombre_consultations')->default(0);

            // Financier
            $table->string('compte_bancaire')->nullable();
            $table->string('code_banque')->nullable();
            $table->string('iban')->nullable();
            $table->decimal('commission_plateforme', 5, 2)->default(5); // Pourcentage de commission

            $table->timestamps();
            $table->softDeletes();

            // Index
            $table->index('type_structure');
            $table->index(['adresse_ville', 'adresse_quartier']);
            $table->index(['latitude', 'longitude']);
            $table->index('statut');
            $table->index('verified');

            // Foreign keys
            $table->foreign('responsable_id')->references('id')->on('users')->restrictOnDelete();
            $table->foreign('verified_by')->references('id')->on('users')->nullOnDelete();
        });

        // Table de liaison utilisateurs-structures (pour multi-praticiens)
        Schema::create('user_structure', function (Blueprint $table) {
            $table->id();
            $table->uuid('user_id');
            $table->uuid('structure_id');
            $table->enum('role', ['praticien', 'assistant', 'secretaire', 'comptable', 'admin']);
            $table->boolean('actif')->default(true);
            $table->date('date_debut');
            $table->date('date_fin')->nullable();
            $table->decimal('pourcentage_honoraires', 5, 2)->default(70); // Part du praticien
            $table->timestamps();

            $table->unique(['user_id', 'structure_id']);
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('structure_id')->references('id')->on('structures_medicales')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_structure');
        Schema::dropIfExists('structures_medicales');
    }
};
