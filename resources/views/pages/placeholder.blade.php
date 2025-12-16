@extends('layouts.app')

@section('title', $title ?? 'Page')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8 text-center">
            <p class="text-uppercase text-primary fw-semibold small mb-2">{{ $section ?? 'Information' }}</p>
            <h1 class="h3 fw-bold mb-3">{{ $title ?? 'Page' }}</h1>
            <p class="text-muted mb-4">{{ $message ?? 'Cette page est en cours de construction.' }}</p>
            <a href="{{ route('home') }}" class="btn btn-gradient rounded-pill">Retour à l’accueil</a>
        </div>
    </div>
</div>
@endsection
