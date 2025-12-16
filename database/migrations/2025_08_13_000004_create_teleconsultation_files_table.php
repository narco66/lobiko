<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('teleconsultation_files', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('session_id');
            $table->uuid('uploader_id')->nullable();
            $table->string('original_name');
            $table->string('path');
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('size')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index('session_id');
            $table->foreign('session_id')->references('id')->on('teleconsultation_sessions')->cascadeOnDelete();
            $table->foreign('uploader_id')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('teleconsultation_files');
    }
};
