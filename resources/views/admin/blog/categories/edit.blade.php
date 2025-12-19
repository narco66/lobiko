@extends('layouts.app')
@section('title','Éditer une catégorie')
@section('content')
<x-lobiko.page-header title="Éditer la catégorie" />
<x-lobiko.ui.flash />

<form method="post" action="{{ route('admin.blog.categories.update', $category) }}">
    @csrf @method('put')
    @include('admin.blog.categories.partials.form', ['category'=>$category, 'parents'=>$parents ?? collect()])
    <div class="mt-3 d-flex gap-2">
        <button class="btn btn-primary">Mettre à jour</button>
        <a href="{{ route('admin.blog.categories.index') }}" class="btn btn-outline-secondary">Annuler</a>
    </div>
</form>
@endsection
