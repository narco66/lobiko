@extends('layouts.app')
@section('title','Créer un tag')
@section('content')
<x-lobiko.page-header title="Créer un tag" />
<x-lobiko.ui.flash />

<form method="post" action="{{ route('admin.blog.tags.store') }}">
    @csrf
    @include('admin.blog.tags.partials.form', ['tag'=>null])
    <div class="mt-3 d-flex gap-2">
        <button class="btn btn-primary">Enregistrer</button>
        <a href="{{ route('admin.blog.tags.index') }}" class="btn btn-outline-secondary">Annuler</a>
    </div>
</form>
@endsection
