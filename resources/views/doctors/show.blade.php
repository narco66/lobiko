@extends('layouts.app')
@section('title', $doctor->full_name)
@section('content')
<div class="container py-4">
    <x-lobiko.page-header
        title="{{ $doctor->full_name }}"
        subtitle="Fiche médecin"
        :breadcrumbs="[
            ['label' => 'Dashboard', 'href' => route('dashboard')],
            ['label' => 'Médecins', 'href' => route('admin.doctors.index')],
            ['label' => $doctor->full_name]
        ]"
        :actions="[
            ['type' => 'secondary', 'url' => route('admin.doctors.edit', $doctor), 'label' => 'Modifier', 'icon' => 'pen']
        ]"
    />
    <x-lobiko.ui.flash />

    <div class="row g-3">
        <div class="col-lg-8">
            <div class="card shadow-sm mb-3">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0"><i class="fas fa-user-md me-2"></i>Profil</h6>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <p class="mb-1"><strong>Matricule :</strong> {{ $doctor->matricule }}</p>
                            <p class="mb-1"><strong>Nom :</strong> {{ $doctor->full_name }}</p>
                            <p class="mb-1"><strong>Statut :</strong> <x-lobiko.ui.badge-status :status="$doctor->statut" /></p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-1"><strong>Email :</strong> {{ $doctor->email ?? '-' }}</p>
                            <p class="mb-1"><strong>Téléphone :</strong> {{ $doctor->telephone ?? '-' }}</p>
                            <p class="mb-1"><strong>Utilisateur lié :</strong> {{ $doctor->user_id ?? '-' }}</p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <p class="mb-1"><strong>Spécialité principale :</strong> {{ $doctor->specialty?->libelle ?? '-' }}</p>
                            <p class="mb-0"><strong>Spécialités :</strong> {{ $doctor->specialties->pluck('libelle')->join(', ') ?: '-' }}</p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-0"><strong>Structures :</strong> {{ $doctor->structures->pluck('nom_structure')->join(', ') ?: '-' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm mb-3">
                <div class="card-header bg-success text-white">
                    <h6 class="mb-0"><i class="fas fa-calendar-plus me-2"></i>Ajouter un créneau</h6>
                </div>
                <div class="card-body">
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

            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="fas fa-clock me-2"></i>Créneaux</h6>
                </div>
                <div class="card-body">
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
        <div class="col-lg-4">
            <div class="card shadow-sm mb-3">
                <div class="card-header bg-warning">
                    <h6 class="mb-0 text-dark"><i class="fas fa-info-circle me-2"></i>Actions</h6>
                </div>
                <div class="card-body">
                    <a href="{{ route('admin.doctors.edit', $doctor) }}" class="btn btn-primary w-100 mb-2"><i class="fas fa-edit me-1"></i>Modifier</a>
                    <form action="{{ route('admin.doctors.destroy', $doctor) }}" method="POST" onsubmit="return confirm('Supprimer ?');">
                        @csrf @method('DELETE')
                        <button class="btn btn-outline-danger w-100"><i class="fas fa-trash me-1"></i>Supprimer</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
