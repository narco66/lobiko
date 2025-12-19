@extends('layouts.app')
@section('title','Éditer un tag')
@section('content')
<x-lobiko.page-header title="Éditer le tag" />
<x-lobiko.ui.flash />

<form method="post" action="{{ route('admin.blog.tags.update', $tag) }}">
    @csrf @method('put')
    @include('admin.blog.tags.partials.form', ['tag'=>$tag])
    <div class="mt-3 d-flex gap-2">
        <button class="btn btn-primary">Mettre à jour</button>
        <a href="{{ route('admin.blog.tags.index') }}" class="btn btn-outline-secondary">Annuler</a>
    </div>
</form>
@endsection
