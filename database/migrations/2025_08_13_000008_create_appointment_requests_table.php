<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('appointment_requests', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('full_name');
            $table->string('phone');
            $table->string('email')->nullable();
            $table->string('speciality');
            $table->enum('mode', ['presentiel', 'teleconsultation', 'domicile']);
            $table->date('preferred_date');
            $table->text('notes')->nullable();
            $table->string('status')->default('pending');
            $table->timestamps();
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appointment_requests');
    }
};
