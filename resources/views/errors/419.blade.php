@extends('layouts.app')

@section('title', 'Session expirée')

@section('content')
    @include('errors.partials.card', [
        'code' => 419,
        'title' => 'Session expirée',
        'message' => "Votre session a expiré. Rafraîchissez la page ou reconnectez-vous pour continuer."
    ])
@endsection
