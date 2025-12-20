<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDossierMedicalRequest;
use App\Http\Requests\UpdateDossierMedicalRequest;
use App\Models\DossierMedical;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class DossierMedicalController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(DossierMedical::class, 'dossier');
    }

    public function index(Request $request)
    {
        Gate::authorize('viewAny', DossierMedical::class);

        $search = $request->get('search');
        $numero = $request->get('numero');

        $dossiers = DossierMedical::with('patient:id,name,email')
            ->when($numero, fn ($q) => $q->where('numero_dossier', 'like', "%{$numero}%"))
            ->when($search, function ($q) use ($search) {
                $q->whereHas('patient', function ($p) use ($search) {
                    $p->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->orderBy('numero_dossier')
            ->paginate(15)
            ->withQueryString();

        return view('dossier-medical.index', compact('dossiers'));
    }

    public function create()
    {
        $patients = $this->patientsForSelect();
        return view('dossier-medical.create', compact('patients'));
    }

    public function store(StoreDossierMedicalRequest $request)
    {
        $dossier = DossierMedical::create($this->prepare($request->validated()));

        return redirect()
            ->route('dossiers-medicaux.show', $dossier)
            ->with('success', 'Dossier créé');
    }

    public function show(DossierMedical $dossier)
    {
        $dossier->load('patient');
        return view('dossier-medical.show', compact('dossier'));
    }

    public function edit(DossierMedical $dossier)
    {
        $dossier->load('patient');
        $patients = $this->patientsForSelect($dossier);
        return view('dossier-medical.edit', compact('dossier', 'patients'));
    }

    public function update(UpdateDossierMedicalRequest $request, DossierMedical $dossier)
    {
        $dossier->update($this->prepare($request->validated()));

        return redirect()
            ->route('dossiers-medicaux.show', $dossier)
            ->with('success', 'Dossier mis à jour');
    }

    public function destroy(DossierMedical $dossier)
    {
        $dossier->delete();
        return redirect()->route('dossiers-medicaux.index')->with('success', 'Dossier supprimé');
    }

    private function patientsForSelect(DossierMedical $dossier = null)
    {
        $patients = User::select('id', 'name')
            ->orderBy('name')
            ->limit(200)
            ->get();

        if ($dossier && $dossier->patient && !$patients->contains('id', $dossier->patient_id)) {
            $patients->push($dossier->patient);
        }

        return $patients;
    }

    private function prepare(array $data): array
    {
        $data['partage_autorise'] = (bool) ($data['partage_autorise'] ?? true);
        $data['actif'] = (bool) ($data['actif'] ?? true);
        $data['enceinte'] = (bool) ($data['enceinte'] ?? false);
        return $data;
    }
}
