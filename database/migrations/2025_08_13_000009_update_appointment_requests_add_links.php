<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('appointment_requests', function (Blueprint $table) {
            $table->uuid('structure_id')->nullable()->after('mode');
            $table->uuid('practitioner_id')->nullable()->after('structure_id');
            $table->string('numero_rdv')->nullable()->after('practitioner_id');
            $table->dateTime('preferred_datetime')->nullable()->after('preferred_date');
            $table->foreign('structure_id')->references('id')->on('structures_medicales')->nullOnDelete();
            $table->foreign('practitioner_id')->references('id')->on('users')->nullOnDelete();
            $table->index('numero_rdv');
        });
    }

    public function down(): void
    {
        Schema::table('appointment_requests', function (Blueprint $table) {
            $table->dropForeign(['structure_id']);
            $table->dropForeign(['practitioner_id']);
            $table->dropColumn(['structure_id', 'practitioner_id', 'numero_rdv', 'preferred_datetime']);
        });
    }
};
