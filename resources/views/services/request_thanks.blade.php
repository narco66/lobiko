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
                    <h1 class="h4 fw-bold mb-2">Votre demande {{ $module ?? '' }} a été envoyée</h1>
                    <p class="text-muted mb-4">
                        Nous revenons vers vous rapidement pour confirmer la prise en charge.
                    </p>
                    @if(!empty($data))
                        <div class="text-start bg-light rounded p-3 mb-4">
                            @if(isset($data['id']))
                                <div class="mb-2"><strong>Référence :</strong> {{ $data['id'] }}</div>
                            @endif
                            @foreach($data as $key => $value)
                                @if(!is_array($value) && $value !== null && $value !== '')
                                    <div class="mb-2"><strong>{{ ucfirst(str_replace('_', ' ', $key)) }} :</strong> {{ $value }}</div>
                                @endif
                            @endforeach
                        </div>
                    @endif
                    <div class="d-flex justify-content-center gap-3">
                        <a href="{{ url()->previous() }}" class="btn btn-outline-primary">Faire une autre demande</a>
                        <a href="{{ route('home') }}" class="btn btn-gradient">Retour à l'accueil</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
