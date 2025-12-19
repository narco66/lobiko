@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="container py-4">
    <x-ui.page-header title="Dashboard" :breadcrumbs="[['label' => 'Dashboard']]" />

    @if(session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    <div class="row g-3 mb-3">
        @foreach($viewModel['kpis'] as $kpi)
            <div class="col-md-3 col-sm-6">
                <x-ui.kpi-card :label="$kpi['label']" :value="$kpi['value']" :icon="$kpi['icon'] ?? null" :variant="$kpi['variant'] ?? 'primary'" />
            </div>
        @endforeach
    </div>

    @if(!empty($viewModel['alerts']))
        <div class="mb-3">
            @foreach($viewModel['alerts'] as $alert)
                <div class="alert alert-{{ $alert['type'] ?? 'info' }} mb-2">
                    {{ $alert['label'] ?? '' }}
                </div>
            @endforeach
        </div>
    @endif

    <div class="row g-3">
        @if($viewModel['role'] === 'admin')
            <div class="col-lg-8">
                <x-ui.panel title="Dernières activités">
                    @if(!empty($viewModel['tables']['activity']))
                        <x-ui.datatable>
                            <thead>
                                <tr>
                                    <th>Événement</th>
                                    <th>Causer</th>
                                    <th>Sujet</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($viewModel['tables']['activity'] as $row)
                                    <tr>
                                        <td>{{ $row->description }}</td>
                                        <td>{{ class_basename($row->causer_type) }}</td>
                                        <td>{{ class_basename($row->subject_type) }}</td>
                                        <td>{{ \Illuminate\Support\Carbon::parse($row->created_at)->format('d/m/Y H:i') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </x-ui.datatable>
                    @else
                        <x-ui.empty-state title="Aucune activité" />
                    @endif
                </x-ui.panel>
            </div>
            <div class="col-lg-4">
                <x-ui.panel title="Actions rapides">
                    <div class="d-grid gap-2">
                        <x-lobiko.buttons.primary href="{{ route('dossiers-medicaux.index') }}" icon="fas fa-folder-open">Dossiers médicaux</x-lobiko.buttons.primary>
                        @can('viewAny', \App\Models\User::class)
                            <x-lobiko.buttons.secondary href="{{ route('users.index') }}" icon="fas fa-users">Utilisateurs</x-lobiko.buttons.secondary>
                        @endcan
                        @if(Route::has('teleconsultation.index'))
                            <x-lobiko.buttons.secondary href="{{ route('teleconsultation.index') }}" icon="fas fa-video">Téléconsultation</x-lobiko.buttons.secondary>
                        @else
                            @php $firstConsultationId = $viewModel['tables']['next_consultations'][0]->id ?? null; @endphp
                            @if($firstConsultationId && Route::has('teleconsultation.room'))
                                <x-lobiko.buttons.secondary href="{{ route('teleconsultation.room', ['consultation' => $firstConsultationId]) }}" icon="fas fa-video">Téléconsultation</x-lobiko.buttons.secondary>
                            @else
                                <x-lobiko.buttons.secondary href="#" icon="fas fa-video" disabled>Téléconsultation</x-lobiko.buttons.secondary>
                            @endif
                        @endif
                        @can('viewAny', \App\Models\Paiement::class)
                            <x-lobiko.buttons.secondary href="{{ route('admin.payments.index') }}" icon="fas fa-credit-card">Paiements</x-lobiko.buttons.secondary>
                        @else
                            <x-lobiko.buttons.secondary href="#" icon="fas fa-credit-card" disabled>Paiements</x-lobiko.buttons.secondary>
                        @endcan
                    </div>
                </x-ui.panel>
            </div>
        @elseif($viewModel['role'] === 'pro')
            <div class="col-lg-6">
                <x-ui.panel title="Consultations à venir">
                    @if(!empty($viewModel['tables']['next_consultations']))
                        <x-ui.datatable>
                            <thead>
                                <tr>
                                    <th>Numéro</th>
                                    <th>Date</th>
                                    <th>Motif</th>
                                    <th>Statut</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($viewModel['tables']['next_consultations'] as $c)
                                    <tr>
                                        <td>{{ $c->numero_consultation }}</td>
                                        <td>{{ \Illuminate\Support\Carbon::parse($c->date_consultation)->format('d/m/Y') }}</td>
                                        <td>{{ $c->motif_consultation }}</td>
                                        <td><x-ui.badge-status :status="$c->statut ?? 'en_attente'" /></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </x-ui.datatable>
                    @else
                        <x-ui.empty-state title="Aucune consultation" />
                    @endif
                </x-ui.panel>
            </div>
            <div class="col-lg-6">
                <x-ui.panel title="Ordonnances récentes">
                    @if(!empty($viewModel['tables']['recent_ordonnances']))
                        <x-ui.datatable>
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Consultation</th>
                                    <th>Statut</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($viewModel['tables']['recent_ordonnances'] as $o)
                                    <tr>
                                        <td>{{ $o->id }}</td>
                                        <td>{{ $o->consultation_id }}</td>
                                        <td><x-ui.badge-status :status="$o->statut ?? 'validee'" /></td>
                                        <td>{{ \Illuminate\Support\Carbon::parse($o->created_at)->format('d/m/Y') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </x-ui.datatable>
                    @else
                        <x-ui.empty-state title="Aucune ordonnance" />
                    @endif
                </x-ui.panel>
            </div>
        @else
            <div class="col-lg-6">
                <x-ui.panel title="Mes consultations">
                    @if(!empty($viewModel['tables']['next_consultations']))
                        <x-ui.datatable>
                            <thead>
                                <tr>
                                    <th>Numéro</th>
                                    <th>Date</th>
                                    <th>Statut</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($viewModel['tables']['next_consultations'] as $c)
                                    <tr>
                                        <td>{{ $c->numero_consultation }}</td>
                                        <td>{{ \Illuminate\Support\Carbon::parse($c->date_consultation)->format('d/m/Y') }}</td>
                                        <td><x-ui.badge-status :status="$c->statut ?? 'en_attente'" /></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </x-ui.datatable>
                    @else
                        <x-ui.empty-state title="Aucune consultation" />
                    @endif
                </x-ui.panel>
            </div>
            <div class="col-lg-6">
                <x-ui.panel title="Mes ordonnances">
                    @if(!empty($viewModel['tables']['recent_ordonnances']))
                        <x-ui.datatable>
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Statut</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($viewModel['tables']['recent_ordonnances'] as $o)
                                    <tr>
                                        <td>{{ $o->id }}</td>
                                        <td><x-ui.badge-status :status="$o->statut ?? 'validee'" /></td>
                                        <td>{{ \Illuminate\Support\Carbon::parse($o->created_at)->format('d/m/Y') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </x-ui.datatable>
                    @else
                        <x-ui.empty-state title="Aucune ordonnance" />
                    @endif
                </x-ui.panel>
            </div>
        @endif
    </div>
</div>
@endsection
