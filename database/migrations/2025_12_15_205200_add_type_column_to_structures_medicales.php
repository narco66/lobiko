<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('structures_medicales', function (Blueprint $table) {
            // Alias pour compatibilité legacy (certaines requêtes utilisent `type` au lieu de `type_structure`)
            $table->enum('type', [
                'cabinet',
                'clinique',
                'hopital',
                'pharmacie',
                'laboratoire',
                'centre_imagerie',
                'centre_specialise'
            ])->nullable()->after('type_structure');
        });

        // Copier la valeur existante
        DB::table('structures_medicales')->update([
            'type' => DB::raw('type_structure')
        ]);
    }

    public function down(): void
    {
        Schema::table('structures_medicales', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
};
