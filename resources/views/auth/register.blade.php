<x-guest-layout>
    <div class="mb-4 text-center">
        <h1 class="auth-title">Rejoindre LOBIKO</h1>
        <p class="auth-subtitle">Créez votre compte pour accéder aux rendez-vous, téléconsultations et e-pharmacie.</p>
    </div>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <div class="row g-3">
            <div class="col-12">
                <label class="tc-label" for="name">Nom complet</label>
                <input id="name" type="text" name="name" value="{{ old('name') }}" class="form-control form-control-lg" required autofocus autocomplete="name">
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>
            <div class="col-md-6">
                <label class="tc-label" for="email">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" class="form-control form-control-lg" required autocomplete="username">
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>
            <div class="col-md-6">
                <label class="tc-label" for="phone">Téléphone</label>
                <input id="phone" type="text" name="phone" value="{{ old('phone') }}" class="form-control form-control-lg" autocomplete="tel">
                <x-input-error :messages="$errors->get('phone')" class="mt-2" />
            </div>
            <div class="col-md-6">
                <label class="tc-label" for="password">Mot de passe</label>
                <input id="password" type="password" name="password" class="form-control form-control-lg" required autocomplete="new-password">
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>
            <div class="col-md-6">
                <label class="tc-label" for="password_confirmation">Confirmer le mot de passe</label>
                <input id="password_confirmation" type="password" name="password_confirmation" class="form-control form-control-lg" required autocomplete="new-password">
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
            </div>
            <div class="col-12">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mt-1">
                    <a class="text-muted small text-decoration-none" href="{{ route('login') }}">
                        Déjà inscrit ? Connexion
                    </a>
                    <button type="submit" class="btn btn-primary btn-lg rounded-pill px-4">
                        Créer mon compte
                    </button>
                </div>
            </div>
        </div>
    </form>
</x-guest-layout>
