@extends('layouts.app')

@section('title', 'Prendre rendez-vous')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="mb-4">
                <p class="text-uppercase text-primary fw-semibold small mb-1">Rendez-vous</p>
                <h1 class="h3 fw-bold mb-2">Planifier un rendez-vous</h1>
                <p class="text-muted mb-0">Choisissez votre spécialité, le mode de consultation et la date souhaitée.</p>
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    @php
                        $structureSelected = old('structure_id', $selectedStructureId ?? '');
                        $practitionerSelected = old('practitioner_id', $selectedPractitionerId ?? '');
                        $specialitySelected = old('speciality', request('speciality'));
                        $defaultSpecialities = ['generaliste' => 'Médecine générale', 'cardiologie' => 'Cardiologie', 'dermatologie' => 'Dermatologie', 'pediatrie' => 'Pédiatrie'];
                    @endphp
                    <form method="POST" action="{{ route('appointments.store') }}" class="row g-3">
                        @csrf
                        <div class="col-md-6">
                            <label class="form-label">Nom complet</label>
                            <input type="text" name="full_name" class="form-control @error('full_name') is-invalid @enderror" value="{{ old('full_name') }}" required>
                            @error('full_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Téléphone</label>
                            <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone') }}" required>
                            @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email (optionnel)</label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}">
                            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Spécialité</label>
                            <select name="speciality" class="form-select @error('speciality') is-invalid @enderror" required>
                                <option value="">Sélectionnez</option>
                                @foreach($defaultSpecialities as $key => $label)
                                    <option value="{{ $key }}" @selected($specialitySelected === $key)>{{ $label }}</option>
                                @endforeach
                                @if($specialitySelected && ! array_key_exists($specialitySelected, $defaultSpecialities))
                                    <option value="{{ $specialitySelected }}" selected>{{ $specialitySelected }}</option>
                                @endif
                            </select>
                            @error('speciality') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Structure</label>
                            <select name="structure_id" class="form-select @error('structure_id') is-invalid @enderror">
                                <option value="">(Optionnel)</option>
                                @foreach($structures as $s)
                                    <option value="{{ $s->id }}" @selected($structureSelected===$s->id)>{{ $s->nom_structure }} ({{ $s->adresse_ville }})</option>
                                @endforeach
                            </select>
                            @error('structure_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Praticien</label>
                            <select name="practitioner_id" class="form-select @error('practitioner_id') is-invalid @enderror">
                                <option value="">(Optionnel)</option>
                                @foreach($practitioners as $p)
                                    <option value="{{ $p->id }}" @selected($practitionerSelected===$p->id)>{{ $p->prenom }} {{ $p->nom }}</option>
                                @endforeach
                            </select>
                            @error('practitioner_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Mode</label>
                            <select name="mode" class="form-select @error('mode') is-invalid @enderror" required>
                                <option value="">Sélectionnez</option>
                                <option value="presentiel" @selected(old('mode')==='presentiel')>Présentiel</option>
                                <option value="teleconsultation" @selected(old('mode')==='teleconsultation')>Téléconsultation</option>
                                <option value="domicile" @selected(old('mode')==='domicile')>Visite à domicile</option>
                            </select>
                            @error('mode') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Date souhaitée</label>
                            <input type="date" name="preferred_date" class="form-control @error('preferred_date') is-invalid @enderror" value="{{ old('preferred_date') }}" required>
                            @error('preferred_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Créneau souhaité (optionnel)</label>
                            <input type="datetime-local" name="preferred_datetime" class="form-control @error('preferred_datetime') is-invalid @enderror" value="{{ old('preferred_datetime') }}">
                            @error('preferred_datetime') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label">Notes</label>
                            <textarea name="notes" class="form-control @error('notes') is-invalid @enderror" rows="3" placeholder="Précisez vos symptômes ou préférences...">{{ old('notes') }}</textarea>
                            @error('notes') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-12 d-flex justify-content-end">
                            <a href="{{ route('appointments.index') }}" class="btn btn-outline-secondary me-2">Annuler</a>
                            <button type="submit" class="btn btn-gradient">Envoyer la demande</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
