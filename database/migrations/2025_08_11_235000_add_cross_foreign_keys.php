<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {


        // remboursements_assurance.facture_id -> factures.id
        if (Schema::hasTable('remboursements_assurance') && Schema::hasTable('factures')) {
            Schema::table('remboursements_assurance', function (Blueprint $table) {
                $table->foreign('facture_id', 'remboursements_assurance_facture_id_foreign')
                      ->references('id')->on('factures')
                      ->restrictOnDelete();
            });
        }
    }

    public function down(): void
    {
        Schema::table('devis', function (Blueprint $table) {
            $table->dropForeign('devis_contrat_assurance_id_foreign');
        });
        Schema::table('remboursements_assurance', function (Blueprint $table) {
            $table->dropForeign('remboursements_assurance_facture_id_foreign');
        });
    }
};
