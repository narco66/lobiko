<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmergencyRequest;
use App\Models\InsuranceRequest;
use App\Models\PharmacyRequest;
use Illuminate\Http\Request;

class ServiceRequestAdminController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->only('type', 'status', 'from', 'to', 'q');
        $perPage = 20;

        $pharmacy = $this->applyFilters(PharmacyRequest::query(), $filters)
            ->latest()
            ->paginate($perPage, ['*'], 'pharmacy_page');
        $insurance = $this->applyFilters(InsuranceRequest::query(), $filters)
            ->latest()
            ->paginate($perPage, ['*'], 'insurance_page');
        $emergency = $this->applyFilters(EmergencyRequest::query(), $filters)
            ->latest()
            ->paginate($perPage, ['*'], 'emergency_page');

        return view('admin.requests.index', compact('pharmacy', 'insurance', 'emergency'));
    }

    private function applyFilters($query, array $filters)
    {
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        if (!empty($filters['from'])) {
            $query->whereDate('created_at', '>=', $filters['from']);
        }
        if (!empty($filters['to'])) {
            $query->whereDate('created_at', '<=', $filters['to']);
        }
        if (!empty($filters['q'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('full_name', 'like', '%'.$filters['q'].'%')
                  ->orWhere('phone', 'like', '%'.$filters['q'].'%')
                  ->orWhere('email', 'like', '%'.$filters['q'].'%');
            });
        }
        return $query;
    }

    public function export()
    {
        $type = request('type', 'pharmacy');
        $filename = "requests_{$type}_" . now()->format('Ymd_His') . ".csv";

        $data = match ($type) {
            'pharmacy' => PharmacyRequest::latest()->limit(1000)->get(),
            'insurance' => InsuranceRequest::latest()->limit(1000)->get(),
            'emergency' => EmergencyRequest::latest()->limit(1000)->get(),
            default => collect(),
        };

        $headers = ['Content-Type' => 'text/csv'];
        return response()->streamDownload(function () use ($data) {
            $out = fopen('php://output', 'w');
            if ($data->isEmpty()) {
                fclose($out);
                return;
            }
            fputcsv($out, array_keys($data->first()->getAttributes()));
            foreach ($data as $row) {
                fputcsv($out, $row->getAttributes());
            }
            fclose($out);
        }, $filename, $headers);
    }
}
