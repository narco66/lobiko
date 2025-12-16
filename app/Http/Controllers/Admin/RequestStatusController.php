<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PharmacyRequest;
use App\Models\InsuranceRequest;
use App\Models\EmergencyRequest;
use App\Models\AppointmentRequest;

class RequestStatusController extends Controller
{
    public function update(Request $request)
    {
        $data = $request->validate([
            'type' => ['required', 'in:pharmacy,insurance,emergency,appointment'],
            'id' => ['required', 'uuid'],
            'status' => ['required', 'in:pending,in_progress,done,rejected'],
        ]);

        $model = match ($data['type']) {
            'pharmacy' => PharmacyRequest::class,
            'insurance' => InsuranceRequest::class,
            'emergency' => EmergencyRequest::class,
            'appointment' => AppointmentRequest::class,
        };

        $record = $model::findOrFail($data['id']);
        $record->update(['status' => $data['status']]);

        return redirect()->back()->with('success', 'Statut mis à jour.');
    }
}
