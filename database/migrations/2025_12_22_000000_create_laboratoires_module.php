<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Laboratoires
        Schema::create('laboratoires', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('nom');
            $table->string('responsable')->nullable();
            $table->string('telephone')->nullable();
            $table->string('email')->nullable();
            $table->string('adresse')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->string('ville')->nullable();
            $table->string('pays')->nullable();
            $table->enum('statut', ['actif', 'suspendu', 'maintenance'])->default('actif');
            $table->decimal('rayon_couverture_km', 6, 2)->nullable();
            $table->timestamps();
        });

        // Examens / analyses
        Schema::create('examens', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('laboratoire_id');
            $table->string('nom');
            $table->string('code')->nullable();
            $table->text('description')->nullable();
            $table->decimal('tarif_base', 10, 2)->default(0);
            $table->json('tarifs_personnalises')->nullable(); // par assurance/structure
            $table->string('delai_resultat')->nullable(); // ex: 24h, 48h
            $table->enum('statut', ['actif', 'suspendu'])->default('actif');
            $table->timestamps();

            $table->foreign('laboratoire_id')->references('id')->on('laboratoires')->cascadeOnDelete();
        });

        // Equipes mobiles de prélèvement
        Schema::create('equipes_prelevement', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('laboratoire_id');
            $table->string('nom');
            $table->string('telephone')->nullable();
            $table->enum('statut', ['disponible', 'en_deplacement', 'indisponible'])->default('disponible');
            $table->timestamps();

            $table->foreign('laboratoire_id')->references('id')->on('laboratoires')->cascadeOnDelete();
        });

        // Rendez-vous labo (sur site ou domicile)
        Schema::create('rendez_vous_labo', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('laboratoire_id');
            $table->uuid('patient_id');
            $table->uuid('examen_id')->nullable();
            $table->enum('mode', ['sur_site', 'domicile']);
            $table->dateTime('date_rdv');
            $table->string('creneau')->nullable();
            $table->text('adresse')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->enum('statut', ['en_attente', 'confirme', 'en_cours', 'resultats_disponibles', 'termine', 'annule'])->default('en_attente');
            $table->timestamps();

            $table->foreign('laboratoire_id')->references('id')->on('laboratoires')->cascadeOnDelete();
            $table->foreign('patient_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('examen_id')->references('id')->on('examens')->nullOnDelete();
        });

        // Déplacements/prélèvements à domicile
        Schema::create('prelevements_domicile', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('rendez_vous_labo_id');
            $table->uuid('equipe_id')->nullable();
            $table->enum('statut', ['en_attente', 'en_route', 'prelevement_effectue', 'echantillon_livre', 'annule'])->default('en_attente');
            $table->dateTime('date_programmee')->nullable();
            $table->string('creneau')->nullable();
            $table->decimal('frais_deplacement', 10, 2)->nullable();
            $table->timestamps();

            $table->foreign('rendez_vous_labo_id')->references('id')->on('rendez_vous_labo')->cascadeOnDelete();
            $table->foreign('equipe_id')->references('id')->on('equipes_prelevement')->nullOnDelete();
        });

        // Facturation / paiements labo
        Schema::create('factures_labo', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('rendez_vous_labo_id');
            $table->decimal('montant_examen', 10, 2)->default(0);
            $table->decimal('frais_deplacement', 10, 2)->default(0);
            $table->decimal('commission_lobiko', 10, 2)->default(0);
            $table->decimal('montant_total', 10, 2)->default(0);
            $table->enum('statut_paiement', ['en_attente', 'paye', 'echoue', 'rembourse'])->default('en_attente');
            $table->string('mode_paiement')->nullable(); // carte, mobile_money, etc.
            $table->timestamps();

            $table->foreign('rendez_vous_labo_id')->references('id')->on('rendez_vous_labo')->cascadeOnDelete();
        });

        // Résultats
        Schema::create('resultats_labo', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('rendez_vous_labo_id');
            $table->string('fichier')->nullable();
            $table->text('commentaire')->nullable();
            $table->uuid('publie_par')->nullable(); // user du laboratoire
            $table->timestamp('publie_at')->nullable();
            $table->timestamps();

            $table->foreign('rendez_vous_labo_id')->references('id')->on('rendez_vous_labo')->cascadeOnDelete();
            $table->foreign('publie_par')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('resultats_labo');
        Schema::dropIfExists('factures_labo');
        Schema::dropIfExists('prelevements_domicile');
        Schema::dropIfExists('rendez_vous_labo');
        Schema::dropIfExists('equipes_prelevement');
        Schema::dropIfExists('examens');
        Schema::dropIfExists('laboratoires');
    }
};
