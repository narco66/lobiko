@extends('layouts.app')
@section('title','Nouveau service')
@section('content')
<div class="container py-4">
    <x-lobiko.page-header title="Nouveau service" />
    <x-lobiko.ui.flash />
    <form method="POST" action="{{ route('admin.services.store') }}">
        @csrf
        <div class="card mb-3">
            <div class="card-body">
                <x-lobiko.forms.input name="code" label="Code" required />
                <x-lobiko.forms.input name="libelle" label="Libellé" required />
                <x-lobiko.forms.textarea name="description" label="Description" />
                <x-lobiko.forms.select name="actif" label="Actif" :options="[1=>'Oui',0=>'Non']" />
            </div>
        </div>
        <x-lobiko.buttons.primary type="submit">Enregistrer</x-lobiko.buttons.primary>
    </form>
</div>
@endsection
