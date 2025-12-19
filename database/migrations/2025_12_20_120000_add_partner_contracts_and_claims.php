<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Etendre partners (référentiel unique)
        Schema::table('partners', function (Blueprint $table) {
            if (!Schema::hasColumn('partners', 'partner_type')) {
                $table->enum('partner_type', ['ASSUREUR', 'PHARMACIE', 'STRUCTURE_MEDICALE', 'AUTRE'])
                    ->default('AUTRE')
                    ->after('type');
            }
            if (!Schema::hasColumn('partners', 'statut')) {
                $table->enum('statut', ['actif', 'suspendu', 'en_attente'])
                    ->default('actif')
                    ->after('partner_type');
            }
            if (!Schema::hasColumn('partners', 'commission_mode')) {
                $table->enum('commission_mode', ['percent', 'fixed', 'none'])
                    ->default('none')
                    ->after('statut');
            }
            if (!Schema::hasColumn('partners', 'commission_value')) {
                $table->decimal('commission_value', 8, 2)->default(0)->after('commission_mode');
            }
            if (!Schema::hasColumn('partners', 'contact_email')) {
                $table->string('contact_email')->nullable()->after('website');
            }
            if (!Schema::hasColumn('partners', 'contact_phone')) {
                $table->string('contact_phone')->nullable()->after('contact_email');
            }
            if (!Schema::hasColumn('partners', 'adresse_ville')) {
                $table->string('adresse_ville')->nullable()->after('contact_phone');
            }
            if (!Schema::hasColumn('partners', 'adresse_pays')) {
                $table->string('adresse_pays')->nullable()->after('adresse_ville');
            }
            if (!Schema::hasColumn('partners', 'numero_legal')) {
                $table->string('numero_legal')->nullable()->after('description');
            }
        });

        Schema::create('convention_rules', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('convention_id')->constrained('conventions')->cascadeOnDelete();
            $table->foreignId('plan_id')->nullable()->constrained('insurance_plans');
            $table->enum('categorie', ['CONSULTATION', 'TELECONSULTATION', 'MEDICAMENT', 'ANALYSE', 'HOSPITALISATION', 'AUTRE'])
                ->default('AUTRE');
            $table->decimal('taux_prise_en_charge', 5, 2)->default(0);
            $table->decimal('plafond_par_acte', 10, 2)->nullable();
            $table->decimal('plafond_mensuel', 10, 2)->nullable();
            $table->decimal('plafond_annuel', 10, 2)->nullable();
            $table->decimal('ticket_moderateur', 10, 2)->nullable();
            $table->decimal('franchise', 10, 2)->nullable();
            $table->boolean('prior_authorization_required')->default(false);
            $table->integer('delai_carence_jours')->nullable();
            $table->json('exclusions')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['convention_id', 'categorie']);
        });

        Schema::create('claims', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('convention_id')->constrained('conventions')->cascadeOnDelete();
            $table->foreignId('prestataire_partner_id')->constrained('partners');
            $table->foreignUuid('insured_id')->nullable()->constrained('users');
            $table->string('reference')->unique();
            $table->date('periode_soins_debut')->nullable();
            $table->date('periode_soins_fin')->nullable();
            $table->decimal('montant_total', 10, 2);
            $table->decimal('montant_couvert', 10, 2);
            $table->decimal('reste_a_charge', 10, 2);
            $table->enum('statut', ['SOUMISE', 'EN_VERIF', 'APPROUVEE', 'REJETEE', 'PAYEE'])->default('SOUMISE');
            $table->date('date_echeance_remboursement')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->string('payment_reference')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('claim_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('claim_id')->constrained('claims')->cascadeOnDelete();
            $table->foreignUuid('convention_rule_id')->nullable()->constrained('convention_rules')->nullOnDelete();
            $table->enum('categorie', ['CONSULTATION', 'TELECONSULTATION', 'MEDICAMENT', 'ANALYSE', 'HOSPITALISATION', 'AUTRE'])
                ->default('AUTRE');
            $table->string('description')->nullable();
            $table->decimal('montant', 10, 2);
            $table->decimal('taux_applique', 5, 2)->default(0);
            $table->decimal('montant_couvert', 10, 2);
            $table->decimal('reste_a_charge', 10, 2);
            $table->json('justificatifs')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('claim_items');
        Schema::dropIfExists('claims');
        Schema::dropIfExists('convention_rules');
        Schema::dropIfExists('insurance_plans');
        Schema::dropIfExists('conventions');

        Schema::table('partners', function (Blueprint $table) {
            $table->dropColumn([
                'partner_type',
                'statut',
                'commission_mode',
                'commission_value',
                'contact_email',
                'contact_phone',
                'adresse_ville',
                'adresse_pays',
                'numero_legal',
            ]);
        });
    }
};
