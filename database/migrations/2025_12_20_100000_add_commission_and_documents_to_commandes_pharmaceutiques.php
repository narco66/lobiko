<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('commandes_pharmaceutiques', function (Blueprint $table) {
            if (!Schema::hasColumn('commandes_pharmaceutiques', 'commission_taux')) {
                $table->decimal('commission_taux', 5, 2)->default(0)->after('frais_livraison');
            }
            if (!Schema::hasColumn('commandes_pharmaceutiques', 'commission_montant')) {
                $table->decimal('commission_montant', 10, 2)->default(0)->after('commission_taux');
            }
            if (!Schema::hasColumn('commandes_pharmaceutiques', 'montant_net_pharmacie')) {
                $table->decimal('montant_net_pharmacie', 10, 2)->nullable()->after('commission_montant');
            }
            if (!Schema::hasColumn('commandes_pharmaceutiques', 'statut_commission')) {
                $table->enum('statut_commission', ['non_calculee', 'en_attente', 'liberee'])
                    ->default('non_calculee')
                    ->after('montant_net_pharmacie');
            }
        });

        Schema::create('devis_pharmaceutiques', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('commande_pharmaceutique_id')->constrained('commandes_pharmaceutiques')->cascadeOnDelete();
            $table->foreignUuid('pharmacie_id')->constrained('pharmacies');
            $table->foreignUuid('cree_par')->nullable()->constrained('users');
            $table->decimal('montant_medicaments', 10, 2);
            $table->decimal('frais_livraison', 10, 2)->default(0);
            $table->decimal('commission_montant', 10, 2)->default(0);
            $table->decimal('total_general', 10, 2);
            $table->enum('statut', ['brouillon', 'envoye', 'accepte', 'refuse', 'expire'])->default('brouillon');
            $table->json('lignes')->nullable(); // détail des médicaments, substitutions, posologies
            $table->timestamp('envoye_at')->nullable();
            $table->timestamp('accepte_at')->nullable();
            $table->timestamps();
        });

        Schema::create('factures_pharmaceutiques', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('commande_pharmaceutique_id')->constrained('commandes_pharmaceutiques')->cascadeOnDelete();
            $table->string('numero_facture')->unique();
            $table->decimal('montant_medicaments', 10, 2);
            $table->decimal('commission_montant', 10, 2)->default(0);
            $table->decimal('total_ttc', 10, 2);
            $table->string('mode_paiement')->nullable();
            $table->enum('statut', ['generee', 'envoyee', 'annulee'])->default('generee');
            $table->string('chemin_pdf')->nullable();
            $table->timestamp('emise_le')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('factures_pharmaceutiques');
        Schema::dropIfExists('devis_pharmaceutiques');

        Schema::table('commandes_pharmaceutiques', function (Blueprint $table) {
            $table->dropColumn([
                'commission_taux',
                'commission_montant',
                'montant_net_pharmacie',
                'statut_commission',
            ]);
        });
    }
};
