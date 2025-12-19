@extends('layouts.app')

@section('title', 'Dossiers médicaux')

@section('content')
<div class="container py-4">
    <x-lobiko.page-header title="Dossiers médicaux" :breadcrumbs="[
        ['label' => 'Tableau de bord', 'href' => route('dashboard')],
        ['label' => 'Dossiers médicaux']
    ]" :action="['label' => 'Créer', 'href' => route('dossiers-medicaux.create'), 'icon' => 'fas fa-plus']" />

    <x-ui.filters>
        <div class="col-md-4">
            <label class="form-label">Patient</label>
            <input type="text" name="patient" value="{{ request('patient') }}" class="form-control" placeholder="Nom, prénom, email">
        </div>
        <div class="col-md-3">
            <label class="form-label">Numéro dossier</label>
            <input type="text" name="numero" value="{{ request('numero') }}" class="form-control">
        </div>
    </x-ui.filters>

    @if($dossiers->count())
        <x-lobiko.tables.datatable>
            <thead>
                <tr>
                    <th>Numéro</th>
                    <th>Patient</th>
                    <th>Consultations</th>
                    <th>Dernière consultation</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($dossiers as $dossier)
                    <tr>
                        <td>{{ $dossier->numero_dossier }}</td>
                        <td>{{ $dossier->patient?->nom }} {{ $dossier->patient?->prenom }}</td>
                        <td>{{ $dossier->nombre_consultations ?? 0 }}</td>
                        <td>{{ optional($dossier->derniere_consultation)->format('d/m/Y H:i') ?? 'N/A' }}</td>
                        <td class="text-end">
                            <x-lobiko.buttons.secondary :href="route('dossiers-medicaux.show', $dossier)" icon="fas fa-eye" class="btn-sm" title="Voir">Voir</x-lobiko.buttons.secondary>
                            @can('update', $dossier)
                                <x-lobiko.buttons.primary :href="route('dossiers-medicaux.edit', $dossier)" icon="fas fa-edit" class="btn-sm" title="Modifier">Modifier</x-lobiko.buttons.primary>
                            @endcan
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </x-lobiko.tables.datatable>

        <div class="mt-3">
            {{ $dossiers->withQueryString()->links() }}
        </div>
    @else
        <x-lobiko.ui.empty-state title="Aucun dossier" description="Créez le premier dossier médical." :action="['label' => 'Créer', 'href' => route('dossiers-medicaux.create'), 'icon' => 'fas fa-plus']" />
    @endif
</div>
@endsection
