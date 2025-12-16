<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('insurance_requests', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('full_name');
            $table->string('phone');
            $table->string('email')->nullable();
            $table->string('policy_number');
            $table->string('insurer')->nullable();
            $table->enum('request_type', ['preautorisation', 'remboursement', 'information']);
            $table->text('notes')->nullable();
            $table->string('status')->default('pending');
            $table->timestamps();
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('insurance_requests');
    }
};
