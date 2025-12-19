@extends('layouts.app')
@section('title','Éditer un article')
@section('content')
<x-lobiko.page-header title="Éditer l’article" />
<x-lobiko.ui.flash />

<form method="post" action="{{ route('admin.blog.posts.update', ['article' => $post]) }}">
    @csrf @method('put')
    @include('admin.blog.posts.partials.form', ['post' => $post])
    <div class="mt-3 d-flex gap-2">
        <button class="btn btn-primary">Mettre à jour</button>
        <a href="{{ route('admin.blog.posts.index') }}" class="btn btn-outline-secondary">Annuler</a>
    </div>
</form>
@endsection
