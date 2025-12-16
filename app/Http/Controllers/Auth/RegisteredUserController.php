<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'phone' => ['nullable', 'string', 'max:50'],
        ]);

        // Adapter au modèle User qui attend nom/prenom
        $fullName = $request->name;
        $parts = preg_split('/\s+/', trim($fullName));
        $prenom = array_shift($parts) ?: $fullName;
        $nom = implode(' ', $parts);
        $nom = $nom !== '' ? $nom : $prenom;

        $user = User::create([
            'nom' => $nom,
            'prenom' => $prenom,
            'date_naissance' => now()->subYears(30)->toDateString(),
            'sexe' => 'M',
            'telephone' => $request->phone ?? '0000000000',
            'adresse_ville' => 'Libreville',
            'adresse_pays' => 'Gabon',
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
