@extends('layouts.app')

@section('title', 'Carrières')

@section('content')
<div class="container py-5">
    <div class="row align-items-center g-4">
        <div class="col-lg-6">
            <p class="text-uppercase text-primary fw-semibold small mb-2">Nous recrutons</p>
            <h1 class="h3 fw-bold mb-3">Rejoignez l'équipe LOBIKO</h1>
            <p class="text-muted mb-4">
                LOBIKO accompagne les professionnels de santé avec des outils modernes.
                Nous cherchons des personnes curieuses, pragmatiques et motivées pour construire la santé de demain.
            </p>
            <div class="d-flex gap-2">
                <a class="btn btn-primary" href="mailto:jobs@lobiko.com">Envoyer une candidature</a>
                <a class="btn btn-outline-secondary" href="{{ route('home') }}">Retour à l'accueil</a>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <h2 class="h5 fw-bold mb-3">Postes ouverts</h2>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-start">
                            <div>
                                <div class="fw-semibold">Développeur Laravel</div>
                                <div class="text-muted small">Remote • CDI</div>
                            </div>
                            <span class="badge bg-primary rounded-pill">Tech</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-start">
                            <div>
                                <div class="fw-semibold">Product Designer</div>
                                <div class="text-muted small">Remote • CDI</div>
                            </div>
                            <span class="badge bg-success rounded-pill">Produit</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-start">
                            <div>
                                <div class="fw-semibold">Customer Success Santé</div>
                                <div class="text-muted small">Remote • CDI</div>
                            </div>
                            <span class="badge bg-info rounded-pill">Support</span>
                        </li>
                    </ul>
                    <p class="text-muted small mt-3 mb-0">
                        Aucune de ces offres ne correspond ? Écrivez-nous : nous lisons toutes les candidatures spontanées.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
