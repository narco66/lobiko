@extends('layouts.app')
@section('title', 'Demande envoyée')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-7 text-center">
            <div class="mb-3">
                <i class="fas fa-check-circle text-success fa-3x"></i>
            </div>
            <h1 class="h4 fw-bold mb-2">Votre demande a été prise en compte</h1>
            <p class="text-muted mb-4">Nous revenons vers vous pour confirmer le rendez-vous.</p>
            <a href="{{ route('appointments.index') }}" class="btn btn-primary"><i class="fas fa-calendar-alt me-2"></i>Voir mes rendez-vous</a>
        </div>
    </div>
</div>
@endsection
