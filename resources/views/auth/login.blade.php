@extends('layouts.app')

@section('title', 'Connexion')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-6 col-md-8">
            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <h1 class="h4 fw-bold mb-3">Connexion</h1>
                    @if (session('status'))
                        <div class="alert alert-info">{{ session('status') }}</div>
                    @endif
                    <form method="POST" action="{{ route('login') }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label" for="email">Email</label>
                            <input id="email" class="form-control @error('email') is-invalid @enderror" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" />
                            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="password">Mot de passe</label>
                            <input id="password" class="form-control @error('password') is-invalid @enderror" type="password" name="password" required autocomplete="current-password" />
                            @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="mb-3 form-check">
                            <input id="remember_me" type="checkbox" class="form-check-input" name="remember">
                            <label class="form-check-label" for="remember_me">Se souvenir de moi</label>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            @if (Route::has('password.request'))
                                <a class="text-decoration-underline" href="{{ route('password.request') }}">
                                    Mot de passe oublié ?
                                </a>
                            @endif
                            <button type="submit" class="btn btn-gradient">
                                Se connecter
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
