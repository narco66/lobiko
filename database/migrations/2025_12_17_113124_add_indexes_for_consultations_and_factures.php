<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('consultations', function (Blueprint $table) {
            $table->index(['patient_id', 'professionnel_id'], 'consultations_patient_pro_idx');
            $table->index('structure_id', 'consultations_structure_idx');
        });

        Schema::table('factures', function (Blueprint $table) {
            $table->index(['patient_id', 'praticien_id'], 'factures_patient_praticien_idx');
        });
    }

    public function down(): void
    {
        Schema::table('consultations', function (Blueprint $table) {
            $table->dropIndex('consultations_patient_pro_idx');
            $table->dropIndex('consultations_structure_idx');
        });

        Schema::table('factures', function (Blueprint $table) {
            $table->dropIndex('factures_patient_praticien_idx');
        });
    }
};
