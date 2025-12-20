<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('insurance_requests', function (Blueprint $table) {
            $columns = [
                'beneficiary' => fn () => $table->string('beneficiary')->nullable(),
                'contract_number' => fn () => $table->string('contract_number')->nullable(),
                'contract_valid_until' => fn () => $table->date('contract_valid_until')->nullable(),
                'plafond_remaining' => fn () => $table->decimal('plafond_remaining', 12, 2)->nullable(),
                'exclusions' => fn () => $table->text('exclusions')->nullable(),
                'waiting_period_days' => fn () => $table->unsignedInteger('waiting_period_days')->nullable(),
                'tiers_payant' => fn () => $table->boolean('tiers_payant')->default(false),
                'status' => fn () => $table->string('status')->default('pending'), // pending, in_review, approved, rejected
                'preauthorization_ref' => fn () => $table->string('preauthorization_ref')->nullable(),
                'simulated_total' => fn () => $table->decimal('simulated_total', 12, 2)->nullable(),
                'coverage_rate' => fn () => $table->unsignedTinyInteger('coverage_rate')->nullable(),
                'covered_amount' => fn () => $table->decimal('covered_amount', 12, 2)->nullable(),
                'patient_due' => fn () => $table->decimal('patient_due', 12, 2)->nullable(),
                'attachments' => fn () => $table->json('attachments')->nullable(),
            ];

            foreach ($columns as $name => $callback) {
                if (!Schema::hasColumn('insurance_requests', $name)) {
                    $callback();
                }
            }
        });
    }

    public function down(): void
    {
        Schema::table('insurance_requests', function (Blueprint $table) {
            $columns = [
                'beneficiary',
                'contract_number',
                'contract_valid_until',
                'plafond_remaining',
                'exclusions',
                'waiting_period_days',
                'tiers_payant',
                'status',
                'preauthorization_ref',
                'simulated_total',
                'coverage_rate',
                'covered_amount',
                'patient_due',
                'attachments',
            ];

            foreach ($columns as $name) {
                if (Schema::hasColumn('insurance_requests', $name)) {
                    $table->dropColumn($name);
                }
            }
        });
    }
};
