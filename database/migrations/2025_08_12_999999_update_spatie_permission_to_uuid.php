<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Adapter Spatie Permission aux UUID du modèle User
        if (Schema::hasTable('model_has_roles')) {
            Schema::table('model_has_roles', function (Blueprint $table) {
                $table->dropPrimary();
                $table->uuid('model_id')->change();
                $table->primary(['role_id', 'model_id', 'model_type']);
            });
        }

        if (Schema::hasTable('model_has_permissions')) {
            Schema::table('model_has_permissions', function (Blueprint $table) {
                $table->dropPrimary();
                $table->uuid('model_id')->change();
                $table->primary(['permission_id', 'model_id', 'model_type']);
            });
        }
    }

    public function down(): void
    {
        // Revenir à des clés entières si nécessaire
        if (Schema::hasTable('model_has_roles')) {
            Schema::table('model_has_roles', function (Blueprint $table) {
                $table->dropPrimary();
                $table->unsignedBigInteger('model_id')->change();
                $table->primary(['role_id', 'model_id', 'model_type']);
            });
        }

        if (Schema::hasTable('model_has_permissions')) {
            Schema::table('model_has_permissions', function (Blueprint $table) {
                $table->dropPrimary();
                $table->unsignedBigInteger('model_id')->change();
                $table->primary(['permission_id', 'model_id', 'model_type']);
            });
        }
    }
};
