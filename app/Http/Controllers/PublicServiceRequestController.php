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
        ]);

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
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

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
