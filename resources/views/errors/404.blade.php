@extends('layouts.app')

@section('title', 'Page introuvable')

@section('content')
    @include('errors.partials.card', [
        'code' => 404,
        'title' => 'Page introuvable',
        'message' => "Le contenu recherché est introuvable ou n'existe plus."
    ])
@endsection
