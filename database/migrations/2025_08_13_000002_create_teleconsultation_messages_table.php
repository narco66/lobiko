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
        Schema::create('teleconsultation_messages', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('session_id');
            $table->uuid('sender_id')->nullable();
            $table->text('body');
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index('session_id');
            $table->foreign('session_id')->references('id')->on('teleconsultation_sessions')->cascadeOnDelete();
            $table->foreign('sender_id')->references('id')->on('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teleconsultation_messages');
    }
};
