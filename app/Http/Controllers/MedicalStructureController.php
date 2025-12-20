<?php

namespace App\Http\Controllers;

use App\Http\Requests\MedicalStructureStoreRequest;
use App\Http\Requests\MedicalStructureUpdateRequest;
use App\Models\MedicalStructure;
use Illuminate\Support\Facades\Gate;

class MedicalStructureController extends Controller
{
    public function index()
    {
        Gate::authorize('viewAny', MedicalStructure::class);
        $structures = MedicalStructure::orderBy('nom_structure')->paginate(15);
        return view('medical-structures.index', compact('structures'));
    }

    public function create()
    {
        Gate::authorize('create', MedicalStructure::class);

        // Récupérer tous les utilisateurs pour le select
        $users = \App\Models\User::orderBy('nom')->orderBy('prenom')
            ->get()
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'text' => $user->nom . ' ' . $user->prenom . ' (' . $user->email . ')',
                    'roles' => $user->getRoleNames()->implode(', ')
                ];
            });

        // Générer le prochain code structure
        $lastStructure = MedicalStructure::withTrashed()
            ->where('code_structure', 'like', 'STR%')
            ->orderBy('code_structure', 'desc')
            ->first();

        if ($lastStructure) {
            $lastNumber = (int) substr($lastStructure->code_structure, 3);
            $nextCode = 'STR' . str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
        } else {
            $nextCode = 'STR001';
        }

        return view('medical-structures.create', compact('users', 'nextCode'));
    }

    public function store(MedicalStructureStoreRequest $request)
    {
        $data = $request->validated();

        // Générer automatiquement le code si non fourni
        if (empty($data['code_structure'])) {
            $lastStructure = MedicalStructure::withTrashed()
                ->where('code_structure', 'like', 'STR%')
                ->orderBy('code_structure', 'desc')
                ->first();

            if ($lastStructure) {
                $lastNumber = (int) substr($lastStructure->code_structure, 3);
                $data['code_structure'] = 'STR' . str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
            } else {
                $data['code_structure'] = 'STR001';
            }
        }

        // Le champ horaires_ouverture est requis en base mais non present sur le formulaire pour l'instant
        $data['horaires_ouverture'] = $data['horaires_ouverture'] ?? [];

        MedicalStructure::create($data);

        return redirect()->route('admin.structures.index')->with('success', 'Structure créée avec succès.');
    }

    public function show(MedicalStructure $structure)
    {
        Gate::authorize('view', $structure);
        return view('medical-structures.show', compact('structure'));
    }

    public function edit(MedicalStructure $structure)
    {
        Gate::authorize('update', $structure);

        // Récupérer tous les utilisateurs pour le select
        $users = \App\Models\User::orderBy('nom')->orderBy('prenom')
            ->get()
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'text' => $user->nom . ' ' . $user->prenom . ' (' . $user->email . ')',
                    'roles' => $user->getRoleNames()->implode(', ')
                ];
            });

        return view('medical-structures.edit', compact('structure', 'users'));
    }

    public function update(MedicalStructureUpdateRequest $request, MedicalStructure $structure)
    {
        Gate::authorize('update', $structure);
        $structure->update($request->validated());

        return redirect()->route('admin.structures.index')->with('success', 'Structure mise à jour.');
    }

    public function destroy(MedicalStructure $structure)
    {
        Gate::authorize('delete', $structure);
        $structure->delete();

        return redirect()->route('admin.structures.index')->with('success', 'Structure désactivée.');
    }
}
