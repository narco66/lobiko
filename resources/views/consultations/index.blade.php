@extends('layouts.app')

@section('title', 'Consultations')

@section('content')
<div class="container py-4">
    <x-lobiko.page-header title="Consultations" :breadcrumbs="[
        ['label' => 'Tableau de bord', 'href' => route('dashboard')],
        ['label' => 'Consultations']
    ]" :action="['label' => 'Créer', 'href' => route('consultations.create'), 'icon' => 'fas fa-plus']" />

    <x-ui.filters>
        <div class="col-md-3">
            <label class="form-label">Patient</label>
            <input type="text" name="patient" value="{{ request('patient') }}" class="form-control" placeholder="Nom ou email">
        </div>
        <div class="col-md-3">
            <label class="form-label">Professionnel</label>
            <input type="text" name="pro" value="{{ request('pro') }}" class="form-control" placeholder="Nom ou email">
        </div>
        <div class="col-md-2">
            <label class="form-label">Date</label>
            <input type="date" name="date" value="{{ request('date') }}" class="form-control">
        </div>
        <div class="col-md-2">
            <label class="form-label">Modalité</label>
            <select name="modalite" class="form-select">
                <option value="">Toutes</option>
                <option value="presentiel" @selected(request('modalite')==='presentiel')>Présentiel</option>
                <option value="teleconsultation" @selected(request('modalite')==='teleconsultation')>Téléconsultation</option>
            </select>
        </div>
    </x-ui.filters>

    @if($consultations->count())
        <x-lobiko.tables.datatable>
            <thead>
                <tr>
                    <th>Numéro</th>
                    <th>Date</th>
                    <th>Patient</th>
                    <th>Professionnel</th>
                    <th>Modalité</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($consultations as $consultation)
                    <tr>
                        <td>{{ $consultation->numero_consultation }}</td>
                        <td>{{ optional($consultation->date_consultation)->format('d/m/Y') }}</td>
                        <td>{{ $consultation->patient->nom ?? '' }} {{ $consultation->patient->prenom ?? '' }}</td>
                        <td>{{ $consultation->professionnel->nom ?? '' }} {{ $consultation->professionnel->prenom ?? '' }}</td>
                        <td><x-lobiko.ui.badge-status :status="$consultation->modalite ?? 'info'">{{ $consultation->modalite }}</x-lobiko.ui.badge-status></td>
                        <td class="text-end">
                            <x-lobiko.buttons.secondary :href="route('consultations.show', $consultation)" icon="fas fa-eye" class="btn-sm">Voir</x-lobiko.buttons.secondary>
                            @can('update', $consultation)
                                <x-lobiko.buttons.primary :href="route('consultations.edit', $consultation)" icon="fas fa-edit" class="btn-sm">Modifier</x-lobiko.buttons.primary>
                            @endcan
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </x-lobiko.tables.datatable>

        <div class="mt-3">
            {{ $consultations->withQueryString()->links() }}
        </div>
    @else
        <x-lobiko.ui.empty-state title="Aucune consultation" description="Créez votre première consultation." :action="['label' => 'Créer', 'href' => route('consultations.create'), 'icon' => 'fas fa-plus']" />
    @endif
</div>
@endsection
