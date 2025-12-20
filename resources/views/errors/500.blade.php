@extends('layouts.app')

@section('title', 'Erreur interne')

@section('content')
    @include('errors.partials.card', [
        'code' => 500,
        'title' => 'Un souci technique est survenu',
        'message' => "Nos équipes ont été alertées. Réessayez dans quelques instants ou contactez le support si le problème persiste."
    ])
@endsection
