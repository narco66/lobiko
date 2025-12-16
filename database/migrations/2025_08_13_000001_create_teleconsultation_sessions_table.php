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
        Schema::create('teleconsultation_sessions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('consultation_id')->unique();
            $table->enum('status', ['pending', 'live', 'ended', 'canceled'])->default('pending');
            $table->string('provider')->default('internal');
            $table->string('room_name');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('ended_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->foreign('consultation_id')->references('id')->on('consultations')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teleconsultation_sessions');
    }
};
