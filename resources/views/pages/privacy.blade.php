@extends('layouts.app')
@section('title', 'Politique de confidentialité')

@section('content')
<div class="container py-4">
    <x-lobiko.page-header
        title="Politique de confidentialité"
        subtitle="Protection des données et vie privée"
        :breadcrumbs="[
            ['label' => 'Accueil', 'href' => route('home')],
            ['label' => 'Confidentialité']
        ]"
    />

    <div class="row g-3">
        <div class="col-lg-8">
            <div class="card shadow-sm mb-3">
                <div class="card-body">
                    <h5 class="fw-bold mb-3">1. Données collectées</h5>
                    <p class="mb-2">Nous collectons les données nécessaires pour fournir nos services : identité et coordonnées (nom, email, téléphone), informations médicales partagées dans votre dossier, rendez-vous, ordonnances, documents transmis (pièces jointes), ainsi que des données techniques (logs de connexion, appareils, cookies essentiels).</p>
                    <p class="mb-0">Aucune donnée n’est vendue ou louée à des tiers.</p>
                </div>
            </div>

            <div class="card shadow-sm mb-3">
                <div class="card-body">
                    <h5 class="fw-bold mb-3">2. Finalités et bases légales</h5>
                    <ul class="mb-0">
                        <li>Fourniture des services de santé numériques (dossier patient, rendez-vous, téléconsultation, e-pharmacie).</li>
                        <li>Suivi administratif : facturation, paiements, prise en charge assurance.</li>
                        <li>Sécurité et lutte contre la fraude (journalisation des accès, contrôle d’intégrité).</li>
                        <li>Communication d’information de service (notifications de rendez-vous, statut de commande).</li>
                    </ul>
                </div>
            </div>

            <div class="card shadow-sm mb-3">
                <div class="card-body">
                    <h5 class="fw-bold mb-3">3. Partage et destinataires</h5>
                    <ul class="mb-0">
                        <li>Professionnels et structures de santé impliqués dans votre parcours.</li>
                        <li>Pharmacies et fournisseurs pour l’exécution des commandes.</li>
                        <li>Assurances/tiers payant lorsque vous activez une prise en charge.</li>
                        <li>Prestataires techniques (hébergement, SMS/email) strictement nécessaires.</li>
                    </ul>
                </div>
            </div>

            <div class="card shadow-sm mb-3">
                <div class="card-body">
                    <h5 class="fw-bold mb-3">4. Durées de conservation</h5>
                    <p class="mb-2">Les données sont conservées pendant la durée de l’utilisation du service, puis archivées le temps nécessaire aux obligations légales (facturation, traçabilité médicale) avant suppression ou anonymisation.</p>
                    <p class="mb-0">Les journaux techniques sont conservés pour la sécurité et l’audit sur une durée limitée.</p>
                </div>
            </div>

            <div class="card shadow-sm mb-3">
                <div class="card-body">
                    <h5 class="fw-bold mb-3">5. Sécurité</h5>
                    <p class="mb-2">Nous appliquons le chiffrement des communications (TLS), la segmentation des accès, et la journalisation des actions sensibles. Les accès sont limités aux personnes habilitées selon leur rôle.</p>
                    <p class="mb-0">Vous pouvez renforcer la sécurité de votre compte via la vérification email et un mot de passe robuste.</p>
                </div>
            </div>

            <div class="card shadow-sm mb-3">
                <div class="card-body">
                    <h5 class="fw-bold mb-3">6. Vos droits</h5>
                    <ul class="mb-2">
                        <li>Droit d’accès et de rectification de vos données.</li>
                        <li>Droit d’effacement et de limitation, dans le respect des obligations légales de conservation.</li>
                        <li>Droit d’opposition au traitement pour prospection (nous n’en faisons pas par défaut).</li>
                        <li>Droit à la portabilité des données fournies.</li>
                    </ul>
                    <p class="mb-0">Pour exercer vos droits, contactez-nous via le support indiqué ci-dessous.</p>
                </div>
            </div>

            <div class="card shadow-sm mb-3">
                <div class="card-body">
                    <h5 class="fw-bold mb-3">7. Cookies et traceurs</h5>
                    <p class="mb-0">Seuls les cookies techniques nécessaires au fonctionnement (session, sécurité) sont utilisés. Les cookies analytiques ne sont pas activés par défaut.</p>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="fw-bold mb-3">8. Contact</h5>
                    <p class="mb-1">Pour toute question ou demande relative à vos données personnelles :</p>
                    <ul class="mb-0">
                        <li>Email : privacy@lobiko.example</li>
                        <li>Support : via votre espace utilisateur ou la page Contact</li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h6 class="mb-0">Résumé</h6>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-3">
                        <li class="mb-2"><strong>Responsable :</strong> LOBIKO</li>
                        <li class="mb-2"><strong>Finalité :</strong> services de santé numériques</li>
                        <li class="mb-2"><strong>Base légale :</strong> exécution du service, obligations légales</li>
                        <li class="mb-2"><strong>Partage :</strong> acteurs de soin, prestataires techniques essentiels</li>
                        <li class="mb-2"><strong>Conservation :</strong> usage + archivage légal</li>
                        <li class="mb-0"><strong>Droits :</strong> accès, rectification, effacement, portabilité</li>
                    </ul>
                    <a class="btn btn-primary w-100" href="{{ route('contact') }}"><i class="fas fa-envelope me-2"></i>Contacter le support</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
