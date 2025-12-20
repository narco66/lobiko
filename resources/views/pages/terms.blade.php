@extends('layouts.app')
@section('title', 'Conditions Générales d’utilisation')

@section('content')
<div class="container py-4">
    <x-lobiko.page-header
        title="Conditions Générales d’utilisation"
        subtitle="Engagements, responsabilités et cadre contractuel de LOBIKO"
        :breadcrumbs="[
            ['label' => 'Accueil', 'href' => route('home')],
            ['label' => 'Conditions']
        ]"
    />

    <div class="row g-3">
        <div class="col-lg-8">
            <div class="card shadow-sm mb-3">
                <div class="card-body">
                    <h5 class="fw-bold mb-2">1. Objet du service</h5>
                    <p class="text-muted mb-0">LOBIKO fournit une plateforme de santé numérique permettant la gestion des rendez-vous, dossiers médicaux, téléconsultations, ordonnances, commandes pharmacie, assurances, devis et factures, accessible aux patients, professionnels et structures autorisés.</p>
                </div>
            </div>

            <div class="card shadow-sm mb-3">
                <div class="card-body">
                    <h5 class="fw-bold mb-2">2. Inscription et compte</h5>
                    <ul class="text-muted mb-0">
                        <li>Vous êtes responsable de la confidentialité de vos identifiants et de toute activité associée.</li>
                        <li>Les informations fournies doivent être exactes et tenues à jour.</li>
                        <li>LOBIKO peut suspendre un compte en cas d’usage frauduleux ou non conforme.</li>
                    </ul>
                </div>
            </div>

            <div class="card shadow-sm mb-3">
                <div class="card-body">
                    <h5 class="fw-bold mb-2">3. Usage autorisé</h5>
                    <ul class="text-muted mb-0">
                        <li>Utilisation conforme aux lois applicables et aux règles professionnelles de santé.</li>
                        <li>Interdiction de diffusion de contenus illicites, faux ou portant atteinte à des tiers.</li>
                        <li>Respect des droits d’auteur sur les documents partagés.</li>
                    </ul>
                </div>
            </div>

            <div class="card shadow-sm mb-3">
                <div class="card-body">
                    <h5 class="fw-bold mb-2">4. Données personnelles et sécurité</h5>
                    <p class="text-muted mb-0">Les traitements sont décrits dans la Politique de confidentialité. Communications chiffrées (TLS), contrôle d’accès par rôles, journalisation des actions sensibles et hébergement sécurisé. Vous devez protéger vos accès et signaler tout incident.</p>
                </div>
            </div>

            <div class="card shadow-sm mb-3">
                <div class="card-body">
                    <h5 class="fw-bold mb-2">5. Contenu médical et limites</h5>
                    <p class="text-muted mb-0">Les informations mises à disposition (dossier, ordonnances, actes) sont fournies par les professionnels habilités. LOBIKO n’exerce pas la médecine et ne remplace pas l’avis clinique. En cas d’urgence, contactez les services d’urgence locaux.</p>
                </div>
            </div>

            <div class="card shadow-sm mb-3">
                <div class="card-body">
                    <h5 class="fw-bold mb-2">6. Responsabilités</h5>
                    <ul class="text-muted mb-0">
                        <li>LOBIKO met en œuvre des moyens pour assurer disponibilité et sécurité, sans garantie d’absence totale d’interruption.</li>
                        <li>Le professionnel reste responsable des décisions médicales et prescriptions.</li>
                        <li>L’utilisateur est responsable de l’exactitude des données saisies et des documents transmis.</li>
                    </ul>
                </div>
            </div>

            <div class="card shadow-sm mb-3">
                <div class="card-body">
                    <h5 class="fw-bold mb-2">7. Paiements et facturation</h5>
                    <p class="text-muted mb-0">Les paiements et factures sont traités via les modules dédiés. Les tarifs, remises ou prises en charge sont affichés avant validation. Les remboursements suivent la politique indiquée dans chaque flux (assurance, pharmacie, consultations).</p>
                </div>
            </div>

            <div class="card shadow-sm mb-3">
                <div class="card-body">
                    <h5 class="fw-bold mb-2">8. Durée, modification et résiliation</h5>
                    <p class="text-muted mb-0">Les présentes conditions s’appliquent pendant l’utilisation de la plateforme. LOBIKO peut les mettre à jour ; la poursuite d’usage vaut acceptation. Chaque partie peut résilier l’accès en cas de manquement ou sur demande utilisateur, sous réserve des obligations légales de conservation.</p>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="fw-bold mb-2">9. Contact et réclamations</h5>
                    <p class="text-muted mb-2">Pour toute question ou réclamation relative aux conditions d’utilisation :</p>
                    <ul class="text-muted mb-0">
                        <li>Email : legal@lobiko.example</li>
                        <li>Support : via la page Contact</li>
                        <li>Adresse : siège LOBIKO, Libreville, Gabon</li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm mb-3">
                <div class="card-header bg-light">
                    <h6 class="mb-0">En résumé</h6>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0 small text-muted">
                        <li class="mb-2"><strong>Objet :</strong> plateforme de santé numérique.</li>
                        <li class="mb-2"><strong>Comptes :</strong> exactitude des infos, sécurité des accès.</li>
                        <li class="mb-2"><strong>Données :</strong> encadrées par la Politique de confidentialité.</li>
                        <li class="mb-2"><strong>Responsabilité :</strong> décisions médicales par les praticiens.</li>
                        <li><strong>Contact :</strong> legal@lobiko.example</li>
                    </ul>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">Liens utiles</h6>
                    <span class="badge bg-primary-subtle text-primary">Légal</span>
                </div>
                <div class="list-group list-group-flush">
                    <a class="list-group-item list-group-item-action d-flex justify-content-between align-items-center" href="{{ route('privacy') }}">
                        Politique de confidentialité <i class="fas fa-arrow-right text-muted" aria-hidden="true"></i>
                    </a>
                    <a class="list-group-item list-group-item-action d-flex justify-content-between align-items-center" href="{{ route('help') }}">
                        Centre d’aide <i class="fas fa-arrow-right text-muted" aria-hidden="true"></i>
                    </a>
                    <a class="list-group-item list-group-item-action d-flex justify-content-between align-items-center" href="{{ route('contact') }}">
                        Contact <i class="fas fa-arrow-right text-muted" aria-hidden="true"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
