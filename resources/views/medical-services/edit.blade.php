@extends('layouts.app')
@section('title','Modifier le service')
@section('content')
<div class="container py-4">
    <x-lobiko.page-header title="Modifier le service" subtitle="{{ $service->libelle }}" />
    <x-lobiko.ui.flash />
    <form method="POST" action="{{ route('admin.services.update', $service) }}">
        @csrf @method('PUT')
        <div class="card mb-3">
            <div class="card-body">
                <x-lobiko.forms.input name="code" label="Code" :value="$service->code" required />
                <x-lobiko.forms.input name="libelle" label="Libellé" :value="$service->libelle" required />
                <x-lobiko.forms.textarea name="description" label="Description" :value="$service->description" />
                <x-lobiko.forms.select name="actif" label="Actif" :options="[1=>'Oui',0=>'Non']" :selected="$service->actif" />
            </div>
        </div>
        <x-lobiko.buttons.primary type="submit">Mettre à jour</x-lobiko.buttons.primary>
    </form>
</div>
@endsection
