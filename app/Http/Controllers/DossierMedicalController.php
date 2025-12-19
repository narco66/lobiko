<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DossierMedical;
use App\Http\Requests\StoreDossierMedicalRequest;
use App\Http\Requests\UpdateDossierMedicalRequest;

class DossierMedicalController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(DossierMedical::class, 'dossier');
    }

    public function index()
    {
        $dossiers = DossierMedical::with('patient')->paginate(20);
        return view('dossier-medical.index', compact('dossiers'));
    }

    public function create()
    {
        return view('dossier-medical.create');
    }

    public function store(StoreDossierMedicalRequest $request)
    {
        $dossier = DossierMedical::create($request->validated());
        return redirect()->route('dossiers-medicaux.show', $dossier)->with('success', 'Dossier créé');
    }

    public function show(DossierMedical $dossier)
    {
        $dossier->load('patient');
        return view('dossier-medical.show', compact('dossier'));
    }

    public function edit(DossierMedical $dossier)
    {
        $dossier->load('patient');
        return view('dossier-medical.edit', compact('dossier'));
    }

    public function update(UpdateDossierMedicalRequest $request, DossierMedical $dossier)
    {
        $dossier->update($request->validated());
        return redirect()->route('dossiers-medicaux.show', $dossier)->with('success', 'Dossier mis à jour');
    }

    public function destroy(DossierMedical $dossier)
    {
        $dossier->delete();
        return redirect()->route('dossiers-medicaux.index')->with('success', 'Dossier supprimé');
    }
}
