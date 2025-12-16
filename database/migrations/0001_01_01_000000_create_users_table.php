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
         Schema::create('users', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('matricule')->unique()->nullable(); // Pour les professionnels
            $table->string('nom');
            $table->string('prenom');
            $table->date('date_naissance');
            $table->enum('sexe', ['M', 'F']);
            $table->string('telephone')->unique();
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');

            // Adresse et géolocalisation
            $table->string('adresse_rue')->nullable();
            $table->string('adresse_quartier')->nullable();
            $table->string('adresse_ville');
            $table->string('adresse_pays')->default('Gabon');
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();

            // Informations professionnelles
            $table->string('specialite')->nullable();
            $table->string('numero_ordre')->nullable(); // Numéro d'ordre professionnel
            $table->string('certification_document')->nullable(); // Document de certification
            $table->boolean('certification_verified')->default(false);
            $table->timestamp('certification_verified_at')->nullable();
            $table->uuid('certification_verified_by')->nullable();

            // Photo et documents
            $table->string('photo_profil')->nullable();
            $table->string('piece_identite')->nullable();
            $table->string('piece_identite_numero')->nullable();
            $table->enum('piece_identite_type', ['CNI', 'Passeport', 'Permis'])->nullable();

            // Statut et activation
            $table->enum('statut_compte', ['actif', 'suspendu', 'archivé', 'en_attente'])->default('en_attente');
            $table->boolean('two_factor_enabled')->default(false);
            $table->string('two_factor_secret')->nullable();
            $table->string('two_factor_recovery_codes', 1000)->nullable();

            // Paramètres utilisateur
            $table->string('langue_preferee')->default('fr');
            $table->boolean('notifications_sms')->default(true);
            $table->boolean('notifications_email')->default(true);
            $table->boolean('notifications_push')->default(true);

            // Métriques
            $table->integer('login_count')->default(0);
            $table->timestamp('last_login_at')->nullable();
            $table->string('last_login_ip')->nullable();
            $table->decimal('note_moyenne', 3, 2)->default(0); // Note moyenne des évaluations
            $table->integer('nombre_evaluations')->default(0);

            // Gestion des tokens
            $table->string('api_token')->nullable();
            $table->rememberToken();

            // Timestamps
            $table->timestamps();
            $table->softDeletes();

            // Index pour optimisation
            $table->index(['telephone', 'email']);
            $table->index(['adresse_ville', 'adresse_quartier']);
            $table->index(['latitude', 'longitude']);
            $table->index('statut_compte');
            $table->index('specialite');
            $table->index('certification_verified');

            // Foreign key
            $table->foreign('certification_verified_by')->references('id')->on('users')->nullOnDelete();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
