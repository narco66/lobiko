@extends('layouts.app')
@section('title', $doctor->full_name)
@section('content')
<div class="container py-4">
    <x-lobiko.page-header title="{{ $doctor->full_name }}" subtitle="Fiche médecin" />
    <x-lobiko.ui.flash />

    <div class="row">
        <div class="col-md-8">
            <div class="card mb-3">
                <div class="card-body">
                    <p><strong>Matricule :</strong> {{ $doctor->matricule }}</p>
                    <p><strong>Spécialités :</strong> {{ $doctor->specialties->pluck('libelle')->join(', ') ?: '—' }}</p>
                    <p><strong>Statut :</strong> <x-lobiko.ui.badge-status :status="$doctor->statut" /></p>
                    <p><strong>Structures :</strong> {{ $doctor->structures->pluck('nom_structure')->join(', ') ?: '—' }}</p>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-body">
                    <h6>Ajouter un créneau</h6>
                    <form method="POST" action="{{ route('admin.doctor-schedules.store') }}" class="row g-2">
                        @csrf
                        <input type="hidden" name="doctor_id" value="{{ $doctor->id }}">
                        <div class="col-md-3">
                            <x-lobiko.forms.select name="day_of_week" label="Jour (optionnel)" :options="[0=>'Dim',1=>'Lun',2=>'Mar',3=>'Mer',4=>'Jeu',5=>'Ven',6=>'Sam']" />
                        </div>
                        <div class="col-md-3">
                            <x-lobiko.forms.input name="date" label="Date (optionnel)" type="date" />
                        </div>
                        <div class="col-md-3">
                            <x-lobiko.forms.input name="start_time" label="Début" type="time" required />
                        </div>
                        <div class="col-md-3">
                            <x-lobiko.forms.input name="end_time" label="Fin" type="time" required />
                        </div>
                        <div class="col-12 d-flex gap-2">
                            <x-lobiko.buttons.primary type="submit">Ajouter</x-lobiko.buttons.primary>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <h6>Créneaux</h6>
                    <ul class="list-group">
                        @forelse($doctor->schedules as $schedule)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span>
                                    {{ $schedule->date ?? ['Dim','Lun','Mar','Mer','Jeu','Ven','Sam'][$schedule->day_of_week] ?? 'Date' }}
                                    : {{ $schedule->start_time }} - {{ $schedule->end_time }}
                                </span>
                                <form method="POST" action="{{ route('admin.doctor-schedules.destroy', $schedule) }}" onsubmit="return confirm('Supprimer ce créneau ?');">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                                </form>
                            </li>
                        @empty
                            <li class="list-group-item">Aucun créneau</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card mb-3">
                <div class="card-body">
                    <h6>Actions</h6>
                    <a href="{{ route('admin.doctors.edit', $doctor) }}" class="btn btn-primary w-100 mb-2">Modifier</a>
                    <form action="{{ route('admin.doctors.destroy', $doctor) }}" method="POST" onsubmit="return confirm('Supprimer ?');">
                        @csrf @method('DELETE')
                        <button class="btn btn-outline-danger w-100">Supprimer</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
