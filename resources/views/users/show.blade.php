@extends('layouts.app')

@section('title', 'Utilisateur '.$user->nom)

@section('content')
<div class="container py-4">
    <x-lobiko.page-header title="Utilisateur" :breadcrumbs="[['label' => 'Utilisateurs', 'href' => route('users.index')], ['label' => $user->nom.' '.$user->prenom]]" />

    <div class="row g-3">
        <div class="col-lg-6">
            <x-ui.panel title="Identité">
                <p class="mb-1"><strong>Nom :</strong> {{ $user->nom }} {{ $user->prenom }}</p>
                <p class="mb-1"><strong>Email :</strong> {{ $user->email }}</p>
                <p class="mb-1"><strong>Téléphone :</strong> {{ $user->telephone }}</p>
                <p class="mb-0"><strong>Rôles :</strong> {{ $user->roles->pluck('name')->join(', ') }}</p>
            </x-ui.panel>
        </div>
        <div class="col-lg-6">
            <x-ui.panel title="Compte">
                <p class="mb-1"><strong>Statut :</strong> <x-lobiko.ui.badge-status :status="$user->statut_compte ?? 'actif'">{{ $user->statut_compte ?? 'actif' }}</x-lobiko.ui.badge-status></p>
                <p class="mb-1"><strong>Date de naissance :</strong> {{ optional($user->date_naissance)->format('d/m/Y') }}</p>
                <p class="mb-0"><strong>Créé le :</strong> {{ optional($user->created_at)->format('d/m/Y H:i') }}</p>
            </x-ui.panel>
        </div>
    </div>

    <div class="d-flex justify-content-end gap-2 mt-3">
        <x-lobiko.buttons.secondary href="{{ route('users.index') }}">Retour</x-lobiko.buttons.secondary>
        @can('update', $user)
            <x-lobiko.buttons.primary href="{{ route('users.edit', $user) }}" icon="fas fa-edit">Modifier</x-lobiko.buttons.primary>
        @endcan
    </div>
</div>
@endsection
