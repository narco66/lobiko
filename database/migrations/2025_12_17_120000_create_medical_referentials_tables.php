<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('specialties', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('code')->unique();
            $table->string('libelle');
            $table->text('description')->nullable();
            $table->boolean('actif')->default(true);
            $table->timestamps();
            $table->softDeletes();
            $table->index('actif');
        });

        Schema::create('medical_services', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('code')->unique();
            $table->string('libelle');
            $table->text('description')->nullable();
            $table->boolean('actif')->default(true);
            $table->timestamps();
            $table->softDeletes();
            $table->index('actif');
        });

        Schema::create('doctors', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id')->nullable();
            $table->string('matricule')->unique();
            $table->string('nom');
            $table->string('prenom');
            $table->string('telephone')->nullable();
            $table->string('email')->nullable();
            $table->uuid('specialty_id')->nullable();
            $table->enum('statut', ['actif', 'suspendu', 'en_validation'])->default('en_validation');
            $table->boolean('verified')->default(false);
            $table->timestamp('verified_at')->nullable();
            $table->uuid('verified_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
            $table->foreign('specialty_id')->references('id')->on('specialties')->nullOnDelete();
            $table->foreign('verified_by')->references('id')->on('users')->nullOnDelete();
            $table->index(['statut', 'verified']);
        });

        Schema::create('doctor_specialty', function (Blueprint $table) {
            $table->id();
            $table->uuid('doctor_id');
            $table->uuid('specialty_id');
            $table->timestamps();
            $table->unique(['doctor_id', 'specialty_id']);
            $table->foreign('doctor_id')->references('id')->on('doctors')->cascadeOnDelete();
            $table->foreign('specialty_id')->references('id')->on('specialties')->cascadeOnDelete();
        });

        Schema::create('doctor_structure', function (Blueprint $table) {
            $table->id();
            $table->uuid('doctor_id');
            $table->uuid('structure_id');
            $table->enum('role', ['praticien', 'assistant', 'secretaire', 'comptable', 'admin'])->default('praticien');
            $table->boolean('actif')->default(true);
            $table->date('date_debut')->nullable();
            $table->date('date_fin')->nullable();
            $table->decimal('pourcentage_honoraires', 5, 2)->default(70);
            $table->timestamps();

            $table->unique(['doctor_id', 'structure_id']);
            $table->foreign('doctor_id')->references('id')->on('doctors')->cascadeOnDelete();
            $table->foreign('structure_id')->references('id')->on('structures_medicales')->cascadeOnDelete();
            $table->index('actif');
        });

        Schema::create('doctor_schedules', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('doctor_id');
            $table->uuid('structure_id')->nullable();
            $table->tinyInteger('day_of_week')->nullable(); // 0=dimanche
            $table->date('date')->nullable();
            $table->time('start_time');
            $table->time('end_time');
            $table->boolean('is_recurring')->default(false);
            $table->date('starts_at')->nullable();
            $table->date('ends_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('doctor_id')->references('id')->on('doctors')->cascadeOnDelete();
            $table->foreign('structure_id')->references('id')->on('structures_medicales')->nullOnDelete();
            $table->index(['doctor_id', 'day_of_week', 'date']);
        });

        Schema::create('doctor_absences', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('doctor_id');
            $table->uuid('structure_id')->nullable();
            $table->dateTime('start_at');
            $table->dateTime('end_at');
            $table->string('motif')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('doctor_id')->references('id')->on('doctors')->cascadeOnDelete();
            $table->foreign('structure_id')->references('id')->on('structures_medicales')->nullOnDelete();
            $table->index(['doctor_id', 'start_at', 'end_at']);
        });

        Schema::create('structure_opening_hours', function (Blueprint $table) {
            $table->id();
            $table->uuid('structure_id');
            $table->tinyInteger('day_of_week');
            $table->time('open_time');
            $table->time('close_time');
            $table->boolean('ferme')->default(false);
            $table->timestamps();

            $table->unique(['structure_id', 'day_of_week']);
            $table->foreign('structure_id')->references('id')->on('structures_medicales')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('structure_opening_hours');
        Schema::dropIfExists('doctor_absences');
        Schema::dropIfExists('doctor_schedules');
        Schema::dropIfExists('doctor_structure');
        Schema::dropIfExists('doctor_specialty');
        Schema::dropIfExists('doctors');
        Schema::dropIfExists('medical_services');
        Schema::dropIfExists('specialties');
    }
};
