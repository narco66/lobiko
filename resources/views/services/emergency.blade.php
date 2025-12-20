@extends('layouts.app')

@section('title', 'Urgences | LOBIKO')

@section('content')
<div class="container py-5">
    <x-lobiko.page-header
        title="Parcours urgences"
        subtitle="SOS, g?olocalisation, orientation vers la structure la plus proche"
        :breadcrumbs="[
            ['label' => 'Accueil', 'href' => route('home')],
            ['label' => 'Urgences']
        ]"
    />

    <div class="row align-items-center g-4 mb-4">
        <div class="col-lg-7">
            <p class="lead text-muted mb-3">
                D?clarez une urgence, partagez votre position, et nous coordonnons l?orientation vers l?ambulance ou la structure adapt?e. Suivi en direct et notification de vos proches autoris?s.
            </p>
            <div class="d-flex flex-wrap gap-3">
                <a href="{{ route('services.emergency.request') }}" class="btn btn-gradient btn-lg rounded-pill px-4">
                    <i class="fas fa-ambulance me-2" aria-hidden="true"></i> Activer le mode urgence
                </a>
                <a href="{{ route('services.pharmacy') }}" class="btn btn-outline-primary btn-lg rounded-pill px-4">
                    Besoin de m?dicaments ?
                </a>
            </div>
            <div class="d-flex gap-4 mt-3 text-muted small">
                <div><i class="fas  fa-location-arrow me-1" aria-hidden="true"></i>G?olocalisation</div>
                <div><i class="fas fa-heart-pulse me-1" aria-hidden="true"></i>Infos vitales</div>
                <div><i class="fas fa-bell me-1" aria-hidden="true"></i>Alerte proches</div>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <h5 class="fw-bold mb-3">Fonctionnalit?s cl?s</h5>
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2"><i class="fas fa-check text-success me-2" aria-hidden="true"></i>Bouton SOS avec localisation et infos vitales minimales</li>
                        <li class="mb-2"><i class="fas fa-check text-success me-2" aria-hidden="true"></i>Matching structure/ambulance selon distance et capacit?</li>
                        <li class="mb-2"><i class="fas fa-check text-success me-2" aria-hidden="true"></i>Suivi du trajet, notifications aux proches autoris?s</li>
                        <li class="mb-2"><i class="fas fa-check text-success me-2" aria-hidden="true"></i>Dossier d'urgence partag? d?s l'arriv?e</li>
                        <li><i class="fas fa-check text-success me-2" aria-hidden="true"></i>Facturation/assurance align?es sur l'acte urgent</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-danger text-white me-3" style="width:48px;height:48px;">
                            <i class="fas fa-bolt" aria-hidden="true"></i>
                        </div>
                        <h5 class="mb-0">Priorisation</h5>
                    </div>
                    <p class="text-muted mb-0">Questionnaire bref pour orienter vers la bonne fili?re (trauma, obst?trique, cardio?) et prioriser.</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-success text-white me-3" style="width:48px;height:48px;">
                            <i class="fas fa-map-marked-alt" aria-hidden="true"></i>
                        </div>
                        <h5 class="mb-0">G?olocalisation</h5>
                    </div>
                    <p class="text-muted mb-0">Estimation du temps d'arriv?e, itin?raire partag?, mises ? jour en direct pour la famille si autoris?e.</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-warning text-white me-3" style="width:48px;height:48px;">
                            <i class="fas fa-file-medical-alt" aria-hidden="true"></i>
                        </div>
                        <h5 class="mb-0">Dossier d'urgence</h5>
                    </div>
                    <p class="text-muted mb-0">Ant?c?dents cl?s, allergies, traitements en cours, contacts utiles : pr?ts pour l'?quipe d'accueil.</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mt-4">
        <div class="col-lg-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <h5 class="fw-bold mb-3">Comment ?a marche</h5>
                    <ol class="list-group list-group-flush">
                        <li class="list-group-item px-0">Activez SOS avec votre position et les infos vitales.</li>
                        <li class="list-group-item px-0">Nous orientons vers l?ambulance ou la structure la plus adapt?e.</li>
                        <li class="list-group-item px-0">Suivez l?arriv?e et tenez vos contacts inform?s.</li>
                        <li class="list-group-item px-0">Dossier d'urgence partag? d?s la prise en charge.</li>
                    </ol>
                    <a href="{{ route('services.emergency.request') }}" class="btn btn-primary btn-sm mt-3">Lancer un SOS</a>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <h5 class="fw-bold mb-3">FAQ rapide</h5>
                    <div class="mb-3">
                        <div class="fw-semibold">Que se passe-t-il apr?s l?activation ?</div>
                        <div class="text-muted small">Nous contactons l?ambulance/structure la plus proche et partageons vos infos essentielles.</div>
                    </div>
                    <div class="mb-3">
                        <div class="fw-semibold">Et si la localisation est d?sactiv?e ?</div>
                        <div class="text-muted small">Vous pouvez renseigner manuellement votre adresse ou un point de rep?re.</div>
                    </div>
                    <div class="mb-3">
                        <div class="fw-semibold">Facturation et assurance</div>
                        <div class="text-muted small">Selon l?acte urgent et votre couverture. Les justificatifs sont disponibles dans votre espace.</div>
                    </div>
                    <a href="{{ route('help') }}" class="btn btn-outline-primary btn-sm">Plus de questions</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
