<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('paiements', function (Blueprint $table) {
            $table->boolean('reversement_genere')->default(false)->after('statut_cantonnement');
            $table->uuid('reversement_pharmacie_id')->nullable()->after('reversement_genere');
            $table->uuid('reversement_livreur_id')->nullable()->after('reversement_pharmacie_id');
            $table->timestamp('payout_tagged_at')->nullable()->after('reversement_livreur_id');
        });
    }

    public function down(): void
    {
        Schema::table('paiements', function (Blueprint $table) {
            $table->dropColumn([
                'reversement_genere',
                'reversement_pharmacie_id',
                'reversement_livreur_id',
                'payout_tagged_at',
            ]);
        });
    }
};
