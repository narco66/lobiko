<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('paiements', function (Blueprint $table) {
            if (!Schema::hasColumn('paiements', 'idempotence_key')) {
                $table->string('idempotence_key')->nullable()->after('statut');
            }

            $idempotenceIndex = DB::select("SHOW INDEX FROM paiements WHERE Key_name = 'paiements_idempotence_key_unique'");
            if (empty($idempotenceIndex)) {
                $table->unique('idempotence_key', 'paiements_idempotence_key_unique');
            }

            $referenceIndex = DB::select("SHOW INDEX FROM paiements WHERE Key_name = 'paiements_reference_transaction_unique'");
            if (empty($referenceIndex)) {
                $table->unique('reference_transaction', 'paiements_reference_transaction_unique');
            }

            $payeurIndex = DB::select("SHOW INDEX FROM paiements WHERE Key_name = 'paiements_payeur_id_index'");
            if (empty($payeurIndex)) {
                $table->index('payeur_id', 'paiements_payeur_id_index');
            }
        });
    }

    public function down(): void
    {
        Schema::table('paiements', function (Blueprint $table) {
            $table->dropUnique('paiements_idempotence_key_unique');
            $table->dropUnique('paiements_reference_transaction_unique');
            $table->dropIndex('paiements_payeur_id_index');
        });
    }
};
