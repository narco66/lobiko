@extends('layouts.app')
@section('title','Téléconsultation')
@section('content')
<div class="container py-4">
    <x-lobiko.page-header title="Téléconsultation" subtitle="Accès rapide aux salles" />
    <x-lobiko.ui.flash />
    <div class="card">
        <div class="card-body">
            <p>Sélectionnez une consultation en cours ou récente depuis le module consultations.</p>
            <a href="{{ route('consultations.index', ['type' => 'teleconsultation']) }}" class="btn btn-primary">
                <i class="fas fa-video me-2"></i> Aller aux consultations
            </a>
        </div>
    </div>
</div>
@endsection
