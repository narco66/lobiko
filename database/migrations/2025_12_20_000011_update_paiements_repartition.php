<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('paiements', function (Blueprint $table) {
            $table->decimal('montant_pharmacie', 12, 2)->default(0)->after('montant_net');
            $table->decimal('montant_livreur', 12, 2)->default(0)->after('montant_pharmacie');
            $table->decimal('commission_plateforme', 12, 2)->default(0)->after('montant_livreur');
            $table->enum('statut_cantonnement', ['en_attente', 'bloque', 'libere'])->default('en_attente')->after('commission_plateforme');
            $table->timestamp('date_cantonnement')->nullable()->after('statut_cantonnement');
            $table->timestamp('date_liberation')->nullable()->after('date_cantonnement');
        });
    }

    public function down(): void
    {
        Schema::table('paiements', function (Blueprint $table) {
            $table->dropColumn([
                'montant_pharmacie',
                'montant_livreur',
                'commission_plateforme',
                'statut_cantonnement',
                'date_cantonnement',
                'date_liberation',
            ]);
        });
    }
};
