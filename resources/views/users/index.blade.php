@extends('layouts.app')

@section('title', 'Utilisateurs')

@section('content')
<div class="container py-4">
    <x-lobiko.page-header title="Utilisateurs" :breadcrumbs="[['label' => 'Dashboard', 'href' => route('dashboard')], ['label' => 'Utilisateurs']]" :action="['label' => 'Créer', 'href' => route('users.create'), 'icon' => 'fas fa-plus']" />

    @if($users->count())
        <x-lobiko.tables.datatable>
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Email</th>
                    <th>Rôles</th>
                    <th>Statut</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                    <tr>
                        <td>{{ $user->nom }} {{ $user->prenom }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->roles->pluck('name')->join(', ') }}</td>
                        <td><x-lobiko.ui.badge-status :status="$user->statut_compte ?? 'actif'">{{ $user->statut_compte ?? 'actif' }}</x-lobiko.ui.badge-status></td>
                        <td class="text-end">
                            <x-lobiko.buttons.secondary :href="route('users.show', $user)" icon="fas fa-eye" class="btn-sm">Voir</x-lobiko.buttons.secondary>
                            @can('update', $user)
                                <x-lobiko.buttons.primary :href="route('users.edit', $user)" icon="fas fa-edit" class="btn-sm">Modifier</x-lobiko.buttons.primary>
                            @endcan
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </x-lobiko.tables.datatable>

        <div class="mt-3">
            {{ $users->links() }}
        </div>
    @else
        <x-lobiko.ui.empty-state title="Aucun utilisateur" description="Créez le premier utilisateur." :action="['label' => 'Créer', 'href' => route('users.create'), 'icon' => 'fas fa-plus']" />
    @endif
</div>
@endsection
