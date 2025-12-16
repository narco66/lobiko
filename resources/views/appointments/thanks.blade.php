@extends('layouts.app')

@section('title', 'Demande envoyée')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-7">
            <div class="card shadow-sm border-0">
                <div class="card-body text-center p-5">
                    <div class="mb-3">
                        <span class="btn btn-success btn-lg rounded-circle" style="width:72px;height:72px;">
                            <i class="fas fa-check"></i>
                        </span>
                    </div>
                    <h1 class="h4 fw-bold mb-2">Votre demande a été envoyée</h1>
                    <p class="text-muted mb-4">
                        Nous vous contacterons pour confirmer le rendez-vous et vous proposer un créneau disponible.
                    </p>
                    @if($data)
                    @if($data)
                        <div class="text-start bg-light rounded p-3 mb-4">
                            @if(isset($data['id']))
                                <div class="mb-2"><strong>Référence :</strong> {{ $data['id'] }}</div>
                            @endif
                            @if(!empty($data['numero_rdv']))
                                <div class="mb-2"><strong>Numéro RDV :</strong> {{ $data['numero_rdv'] }}</div>
                            @endif
                            <div class="mb-2"><strong>Nom :</strong> {{ $data['full_name'] ?? '' }}</div>
                            <div class="mb-2"><strong>Téléphone :</strong> {{ $data['phone'] ?? '' }}</div>
                            @if(!empty($data['email']))
                                <div class="mb-2"><strong>Email :</strong> {{ $data['email'] }}</div>
                            @endif
                            <div class="mb-2"><strong>Spécialité :</strong> {{ isset($data['speciality']) ? ucfirst($data['speciality']) : '' }}</div>
                            <div class="mb-2"><strong>Mode :</strong> {{ isset($data['mode']) ? ucfirst($data['mode']) : '' }}</div>
                            @if(!empty($data['preferred_date']))
                                <div class="mb-2"><strong>Date souhaitée :</strong> {{ \Illuminate\Support\Carbon::parse($data['preferred_date'])->format('d/m/Y') }}</div>
                            @endif
                            @if(!empty($data['notes']))
                                <div><strong>Notes :</strong> {{ $data['notes'] }}</div>
                            @endif
                        </div>
                    @endif
                    @endif
                    <div class="d-flex justify-content-center gap-3">
                        <a href="{{ route('appointments.create') }}" class="btn btn-outline-primary">Planifier un autre rendez-vous</a>
                        <a href="{{ route('home') }}" class="btn btn-gradient">Retour à l'accueil</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
