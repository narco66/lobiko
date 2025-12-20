<?php

namespace App\Http\Controllers;

use App\Mail\ServiceRequestNotification;
use Illuminate\Http\Request;
use App\Models\PharmacyRequest;
use App\Models\InsuranceRequest;
use App\Models\EmergencyRequest;
use Illuminate\Support\Facades\Mail;
use App\Services\SmsNotifier;

class PublicServiceRequestController extends Controller
{
    public function pharmacy()
    {
        return view('services.pharmacy_request');
    }

    public function storePharmacy(Request $request)
    {
        $data = $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:50'],
            'email' => ['nullable', 'email'],
            'prescription_code' => ['nullable', 'string', 'max:100'],
            'delivery_mode' => ['required', 'in:retrait,livraison'],
            'address' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'attachments.*' => ['file', 'max:4096'],
        ]);

        $attachments = [];
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $attachments[] = $file->store('pharmacy_attachments');
            }
        }
        if (!empty($attachments)) {
            $data['attachments'] = $attachments;
        }

        $record = PharmacyRequest::create($data);

        $this->notifyAdmin('Pharmacie', $record->toArray());

        return view('services.request_thanks', [
            'module' => 'Pharmacie',
            'data' => $record->toArray(),
        ]);
    }

    public function insurance()
    {
        return view('services.insurance_request');
    }

    public function storeInsurance(Request $request)
    {
        $data = $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:50'],
            'email' => ['nullable', 'email'],
            'policy_number' => ['required', 'string', 'max:100'],
            'insurer' => ['nullable', 'string', 'max:150'],
            'request_type' => ['required', 'in:preautorisation,remboursement,information'],
            'beneficiary' => ['nullable', 'string', 'max:255'],
            'contract_number' => ['nullable', 'string', 'max:150'],
            'contract_valid_until' => ['nullable', 'date'],
            'plafond_remaining' => ['nullable', 'numeric', 'min:0'],
            'exclusions' => ['nullable', 'string', 'max:1000'],
            'waiting_period_days' => ['nullable', 'integer', 'min:0', 'max:365'],
            'tiers_payant' => ['nullable', 'boolean'],
            'preauthorization_ref' => ['nullable', 'string', 'max:150'],
            'simulated_total' => ['nullable', 'numeric', 'min:0'],
            'coverage_rate' => ['nullable', 'integer', 'min:0', 'max:100'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'attachments.*' => ['file', 'max:4096'],
        ]);

        // Simulation RAC
        $total = $data['simulated_total'] ?? null;
        $rate = $data['coverage_rate'] ?? null;
        if ($total !== null && $rate !== null) {
            $covered = round(($total * $rate) / 100, 2);
            $due = max(0, round($total - $covered, 2));
            $data['covered_amount'] = $covered;
            $data['patient_due'] = $due;
        }

        // Tiers payant flag
        $data['tiers_payant'] = (bool) ($data['tiers_payant'] ?? false);

        // Gestion des pièces jointes
        $attachments = [];
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $attachments[] = $file->store('insurance_attachments');
            }
        }
        if (!empty($attachments)) {
            $data['attachments'] = $attachments;
        }

        $record = InsuranceRequest::create($data);

        $this->notifyAdmin('Assurance', $record->toArray());

        return view('services.request_thanks', [
            'module' => 'Assurance',
            'data' => $record->toArray(),
        ]);
    }

    public function emergency()
    {
        return view('services.emergency_request');
    }

    public function storeEmergency(Request $request)
    {
        $data = $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:50'],
            'city' => ['nullable', 'string', 'max:100'],
            'location' => ['nullable', 'string', 'max:255'],
            'emergency_type' => ['required', 'in:medicale,traumatique,obstetricale,autre'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $record = EmergencyRequest::create($data);

        $this->notifyAdmin('Urgence', $record->toArray());

        return view('services.request_thanks', [
            'module' => 'Urgence',
            'data' => $record->toArray(),
        ]);
    }

    protected function notifyAdmin(string $module, array $payload): void
    {
        try {
            $to = config('mail.from.address') ?? env('MAIL_FROM_ADDRESS');
            if ($to) {
                Mail::to($to)->send(new ServiceRequestNotification($module, $payload));
            }
            $sms = app(SmsNotifier::class);
            $smsRecipient = env('ADMIN_SMS_TO');
            $sms->send($smsRecipient, "[{$module}] Demande de {$payload['full_name']} ({$payload['phone']})");
        } catch (\Throwable $e) {
            // On ignore en silence pour ne pas casser le parcours utilisateur.
        }
    }
}
