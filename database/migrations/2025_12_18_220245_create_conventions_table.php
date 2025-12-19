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
        Schema::create('conventions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('assureur_partner_id')->constrained('partners');
            $table->foreignId('prestataire_partner_id')->constrained('partners');
            $table->string('code_convention')->unique();
            $table->string('libelle');
            $table->text('objet')->nullable();
            $table->date('date_debut')->nullable();
            $table->date('date_fin')->nullable();
            $table->enum('statut', ['DRAFT', 'EN_VALIDATION', 'ACTIVE', 'SUSPENDUE', 'EXPIREE', 'RESILIEE'])
                ->default('DRAFT');
            $table->text('conditions_generales')->nullable();
            $table->integer('delai_remboursement_jours')->default(30);
            $table->enum('mode_facturation', ['POST_PAY', 'TIERS_PAYANT', 'MIXTE'])->default('POST_PAY');
            $table->foreignUuid('created_by')->nullable()->constrained('users');
            $table->foreignUuid('validated_by')->nullable()->constrained('users');
            $table->softDeletes();
            $table->timestamps();

            $table->index(['assureur_partner_id', 'prestataire_partner_id']);
            $table->index('statut');
            $table->index('date_fin');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('conventions');
    }
};
