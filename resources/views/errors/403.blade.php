@extends('layouts.app')

@section('title', 'Accès refusé')

@section('content')
    @include('errors.partials.card', [
        'code' => 403,
        'title' => 'Accès refusé',
        'message' => "Vous n'êtes pas autorisé à accéder à cette page ou à réaliser cette action."
    ])
@endsection
