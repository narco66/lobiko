@extends('layouts.app')
@section('title','Créer un article')
@section('content')
<x-lobiko.page-header title="Créer un article" />
<x-lobiko.ui.flash />

<form method="post" action="{{ route('admin.blog.posts.store') }}">
    @csrf
    @include('admin.blog.posts.partials.form', ['post' => null])
    <div class="mt-3 d-flex gap-2">
        <button class="btn btn-primary">Enregistrer</button>
        <a href="{{ route('admin.blog.posts.index') }}" class="btn btn-outline-secondary">Annuler</a>
    </div>
</form>
@endsection
