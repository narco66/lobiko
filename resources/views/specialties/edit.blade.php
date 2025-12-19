@extends('layouts.app')
@section('title','Modifier la spécialité')
@section('content')
<div class="container py-4">
    <x-lobiko.page-header title="Modifier la spécialité" subtitle="{{ $specialty->libelle }}" />
    <x-lobiko.ui.flash />
    <form method="POST" action="{{ route('admin.specialties.update', $specialty) }}">
        @csrf @method('PUT')
        <div class="card mb-3">
            <div class="card-body">
                <x-lobiko.forms.input name="code" label="Code" :value="$specialty->code" required />
                <x-lobiko.forms.input name="libelle" label="Libellé" :value="$specialty->libelle" required />
                <x-lobiko.forms.textarea name="description" label="Description" :value="$specialty->description" />
                <x-lobiko.forms.select name="actif" label="Actif" :options="[1=>'Oui',0=>'Non']" :selected="$specialty->actif" />
            </div>
        </div>
        <x-lobiko.buttons.primary type="submit">Mettre à jour</x-lobiko.buttons.primary>
    </form>
</div>
@endsection
