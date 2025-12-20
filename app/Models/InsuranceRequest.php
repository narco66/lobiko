<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InsuranceRequest extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'full_name',
        'phone',
        'email',
        'policy_number',
        'insurer',
        'request_type',
        'notes',
        'status',
        'beneficiary',
        'contract_number',
        'contract_valid_until',
        'plafond_remaining',
        'exclusions',
        'waiting_period_days',
        'tiers_payant',
        'preauthorization_ref',
        'simulated_total',
        'coverage_rate',
        'covered_amount',
        'patient_due',
        'attachments',
    ];
}
