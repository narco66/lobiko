@extends('layouts.app')

@section('title', 'Modifier un partenaire')

@section('content')
<div class="container py-4">
    <x-lobiko.page-header title="Modifier un partenaire" :breadcrumbs="[['label' => 'Partenaires', 'href' => route('partners')], ['label' => 'Modifier']]" />

    <form method="POST" action="{{ route('partners.update', $partner) }}">
        @csrf
        @method('PUT')
        <div class="row g-3">
            <div class="col-md-6">
                <x-ui.panel title="Informations">
                    <x-lobiko.forms.input name="name" label="Nom" :value="old('name', $partner->name)" required />
                    <x-lobiko.forms.select name="partner_type" label="Type" :options="['ASSUREUR' => 'Assureur', 'PHARMACIE' => 'Pharmacie', 'STRUCTURE_MEDICALE' => 'Structure médicale', 'AUTRE' => 'Autre']" :value="old('partner_type', $partner->partner_type)" required />
                    <x-lobiko.forms.select name="statut" label="Statut" :options="['actif' => 'Actif', 'suspendu' => 'Suspendu', 'en_attente' => 'En attente']" :value="old('statut', $partner->statut)" required />
                    <x-lobiko.forms.select name="type" label="Catégorie (legacy)" :options="['insurance' => 'Assurance', 'medical' => 'Médical', 'payment' => 'Paiement', 'logistics' => 'Logistique', 'technology' => 'Technologie', 'other' => 'Autre']" :value="old('type', $partner->type ?? 'other')" />
                    <x-lobiko.forms.input name="numero_legal" label="Numéro légal" :value="old('numero_legal', $partner->numero_legal)" />
                    <x-lobiko.forms.input name="contact_email" label="Email contact" :value="old('contact_email', $partner->contact_email)" />
                    <x-lobiko.forms.input name="contact_phone" label="Téléphone contact" :value="old('contact_phone', $partner->contact_phone)" />
                </x-ui.panel>
            </div>
            <div class="col-md-6">
                <x-ui.panel title="Commission & localisation">
                    <x-lobiko.forms.select name="commission_mode" label="Mode de commission" :options="['percent' => '%', 'fixed' => 'Montant fixe', 'none' => 'Aucune']" :value="old('commission_mode', $partner->commission_mode)" />
                    <x-lobiko.forms.input name="commission_value" type="number" step="0.01" label="Valeur" :value="old('commission_value', $partner->commission_value)" />
                    <x-lobiko.forms.input name="adresse_ville" label="Ville" :value="old('adresse_ville', $partner->adresse_ville)" />
                    <x-lobiko.forms.input name="adresse_pays" label="Pays" :value="old('adresse_pays', $partner->adresse_pays ?? 'Gabon')" />
                </x-ui.panel>
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2 mt-3">
            <x-lobiko.buttons.secondary href="{{ route('partners') }}">Annuler</x-lobiko.buttons.secondary>
            <x-lobiko.buttons.primary type="submit" icon="fas fa-save">Mettre à jour</x-lobiko.buttons.primary>
        </div>
    </form>
</div>
@endsection
