<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('commandes_pharmaceutiques', function (Blueprint $table) {
            $table->enum('statut_paiement', [
                'en_attente',
                'partiel',
                'paye',
                'rembourse',
                'echoue',
            ])->default('en_attente')->after('statut');
        });
    }

    public function down(): void
    {
        Schema::table('commandes_pharmaceutiques', function (Blueprint $table) {
            $table->dropColumn('statut_paiement');
        });
    }
};
