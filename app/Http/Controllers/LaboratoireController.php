<?php

namespace App\Http\Controllers;

use App\Models\Laboratoire;
use Illuminate\Http\Request;

class LaboratoireController extends Controller
{
    public function index()
    {
        $labs = Laboratoire::latest()->paginate(15);
        return view('laboratoires.index', compact('labs'));
    }

    public function create()
    {
        return view('laboratoires.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nom' => ['required', 'string', 'max:190'],
            'responsable' => ['nullable', 'string', 'max:190'],
            'telephone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email'],
            'adresse' => ['nullable', 'string', 'max:255'],
            'latitude' => ['nullable', 'numeric'],
            'longitude' => ['nullable', 'numeric'],
            'ville' => ['nullable', 'string', 'max:120'],
            'pays' => ['nullable', 'string', 'max:120'],
            'rayon_couverture_km' => ['nullable', 'numeric'],
            'statut' => ['required', 'in:actif,suspendu,maintenance'],
        ]);

        Laboratoire::create($data);
        return redirect()->route('laboratoires.index')->with('success', 'Laboratoire créé');
    }

    public function edit(Laboratoire $laboratoire)
    {
        return view('laboratoires.edit', compact('laboratoire'));
    }

    public function update(Request $request, Laboratoire $laboratoire)
    {
        $data = $request->validate([
            'nom' => ['required', 'string', 'max:190'],
            'responsable' => ['nullable', 'string', 'max:190'],
            'telephone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email'],
            'adresse' => ['nullable', 'string', 'max:255'],
            'latitude' => ['nullable', 'numeric'],
            'longitude' => ['nullable', 'numeric'],
            'ville' => ['nullable', 'string', 'max:120'],
            'pays' => ['nullable', 'string', 'max:120'],
            'rayon_couverture_km' => ['nullable', 'numeric'],
            'statut' => ['required', 'in:actif,suspendu,maintenance'],
        ]);

        $laboratoire->update($data);
        return redirect()->route('laboratoires.index')->with('success', 'Laboratoire mis à jour');
    }

    public function destroy(Laboratoire $laboratoire)
    {
        $laboratoire->delete();
        return redirect()->route('laboratoires.index')->with('success', 'Laboratoire supprimé');
    }
}
