@extends('layouts.app')

@section('title', 'Patient '.$patient->prenom.' '.$patient->nom)

@section('content')
<div class="container py-4">
    <x-lobiko.page-header
        title="Patient"
        subtitle="{{ $patient->prenom }} {{ $patient->nom }}"
        :breadcrumbs="[
            ['label' => 'Dashboard', 'href' => route('dashboard')],
            ['label' => 'Patients', 'href' => route('patients.index')],
            ['label' => $patient->prenom.' '.$patient->nom]
        ]"
        :actions="[
            ['type' => 'secondary', 'url' => route('patients.edit', $patient), 'label' => 'Modifier', 'icon' => 'pen']
        ]"
    />

    <x-lobiko.ui.flash />

    <div class="row g-3">
        <div class="col-lg-8">
            <div class="card shadow-sm mb-3">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0"><i class="fas fa-id-card me-2"></i>Identite et contact</h6>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <p class="mb-1"><strong>Nom complet :</strong> {{ $patient->prenom }} {{ $patient->nom }}</p>
                            <p class="mb-1"><strong>Date de naissance :</strong> {{ optional($patient->date_naissance)->format('d/m/Y') }}</p>
                            <p class="mb-0"><strong>Sexe :</strong> {{ $patient->sexe === 'M' ? 'Homme' : 'Femme' }}</p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-1"><strong>Email :</strong> {{ $patient->email }}</p>
                            <p class="mb-1"><strong>Telephone :</strong> {{ $patient->telephone }}</p>
                            <p class="mb-0"><strong>Statut :</strong> <x-lobiko.ui.badge-status :status="$patient->statut_compte ?? 'actif'" /></p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <p class="mb-1"><strong>Adresse :</strong>
                                {{ $patient->adresse_rue }}
                                {{ $patient->adresse_quartier ? ' - '.$patient->adresse_quartier : '' }},
                                {{ $patient->adresse_ville }},
                                {{ $patient->adresse_pays }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">
                    <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Infos complementaires</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p class="mb-1"><strong>Notifications email :</strong> {{ $patient->notifications_email ? 'Oui' : 'Non' }}</p>
                            <p class="mb-1"><strong>Notifications SMS :</strong> {{ $patient->notifications_sms ? 'Oui' : 'Non' }}</p>
                            <p class="mb-1"><strong>Notifications push :</strong> {{ $patient->notifications_push ? 'Oui' : 'Non' }}</p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-1"><strong>Matricule :</strong> {{ $patient->matricule ?? 'N/A' }}</p>
                            <p class="mb-1"><strong>Compte cree le :</strong> {{ optional($patient->created_at)->format('d/m/Y H:i') }}</p>
                            <p class="mb-1"><strong>Derniere connexion :</strong> {{ optional($patient->last_login_at)->format('d/m/Y H:i') ?? 'N/A' }}</p>
                        </div>
                    </div>
                    @if($patient->contratAssuranceActif)
                        <div class="alert alert-info mt-3 mb-0">
                            <i class="fas fa-shield-alt me-2"></i>
                            Assurance active : Contrat {{ $patient->contratAssuranceActif->numero_contrat ?? '' }} ({{ $patient->contratAssuranceActif->statut }})
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card shadow-sm mb-3">
                <div class="card-header bg-warning">
                    <h6 class="mb-0 text-dark"><i class="fas fa-chart-line me-2"></i>Statistiques patient</h6>
                </div>
                <div class="card-body">
                    <div class="mb-2"><strong>Consultations :</strong> {{ $stats['total_consultations'] ?? 0 }}</div>
                    <div class="mb-2"><strong>Ordonnances :</strong> {{ $stats['total_ordonnances'] ?? 0 }}</div>
                    <div class="mb-2"><strong>Depenses :</strong> {{ number_format($stats['total_depenses'] ?? 0, 0, ',', ' ') }} FCFA</div>
                    <div class="mb-0"><strong>Rendez-vous manques :</strong> {{ $stats['rendez_vous_manques'] ?? 0 }}</div>
                </div>
            </div>

            @if($patient->dossierMedical)
                <div class="card shadow-sm">
                    <div class="card-header bg-info text-white">
                        <h6 class="mb-0"><i class="fas fa-notes-medical me-2"></i>Dossier medical</h6>
                    </div>
                    <div class="card-body">
                        <p class="mb-1"><strong>No dossier :</strong> {{ $patient->dossierMedical->id }}</p>
                        <p class="mb-1"><strong>Allergies :</strong> {{ $patient->dossierMedical->allergies ? implode(', ', $patient->dossierMedical->allergies) : 'Aucune' }}</p>
                        <p class="mb-0"><strong>Antecedents :</strong> {{ $patient->dossierMedical->antecedents ?? 'Non renseigne' }}</p>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <div class="d-flex justify-content-end gap-2 mt-3">
        <x-lobiko.buttons.secondary href="{{ route('patients.index') }}">Retour</x-lobiko.buttons.secondary>
        @can('update', $patient)
            <x-lobiko.buttons.primary href="{{ route('patients.edit', $patient) }}" icon="fas fa-edit">Modifier</x-lobiko.buttons.primary>
        @endcan
    </div>
</div>
@endsection
