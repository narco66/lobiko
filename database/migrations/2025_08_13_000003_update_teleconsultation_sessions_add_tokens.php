<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('teleconsultation_sessions', function (Blueprint $table) {
            $table->string('patient_token', 64)->nullable()->after('room_name');
            $table->string('practitioner_token', 64)->nullable()->after('patient_token');
            $table->timestamp('token_expires_at')->nullable()->after('practitioner_token');
        });
    }

    public function down(): void
    {
        Schema::table('teleconsultation_sessions', function (Blueprint $table) {
            $table->dropColumn(['patient_token', 'practitioner_token', 'token_expires_at']);
        });
    }
};
