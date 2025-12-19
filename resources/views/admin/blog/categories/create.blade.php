@extends('layouts.app')
@section('title','Créer une catégorie')
@section('content')
<x-lobiko.page-header title="Créer une catégorie" />
<x-lobiko.ui.flash />

<form method="post" action="{{ route('admin.blog.categories.store') }}">
    @csrf
    @include('admin.blog.categories.partials.form', ['category'=>null, 'parents'=>$parents ?? collect() ])
    <div class="mt-3 d-flex gap-2">
        <button class="btn btn-primary">Enregistrer</button>
        <a href="{{ route('admin.blog.categories.index') }}" class="btn btn-outline-secondary">Annuler</a>
    </div>
</form>
@endsection
