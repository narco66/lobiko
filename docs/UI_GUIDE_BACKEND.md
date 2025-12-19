UI GUIDE BACKEND

Conventions générales
- Layout : sidebar fixe + topbar, breadcrumbs systématiques, flash messages unifiés (success/error/warning/info).
- Un seul bouton primaire par écran (Créer / Enregistrer / Valider). Actions destructrices jamais en primaire, toujours avec modal de confirmation.
- Actions sensibles (dossier médical, consultation, ordonnance, paiement) affichées uniquement si autorisées (@can/@canany) et loguées côté backend.
- Formulaires : sections (cards), erreurs visibles en haut et au champ, prévention double soumission (disable + spinner).
- Pages show : fiche avec résumé (statut, propriétaire, timestamps) + onglets (Détails | Historique | Documents | Paiements | Audit selon module) + zone actions contextualisées.

Composants Blade (resources/views/components/ui)
- page-header : titre, breadcrumbs, bouton principal.
- button : types primary/secondary/danger/link avec icônes optionnels.
- badge-status : badge coloré selon statut (success/info/warning/danger/secondary).
- filters : barre de filtres/recherche persistée (query string).
- datatable : tableau responsif + slot actions.
- confirm-modal : modal de confirmation standard.
- empty-state : état vide avec CTA optionnel.

Menus (sidebar)
- Tableau de bord
- Patients
- Dossiers médicaux
- Consultations
- Ordonnances
- Téléconsultation
- Facturation (Factures, Paiements)
- Référentiels (Actes médicaux, Structures/Spécialités/Tarifs, Pharmacie*)
- Sécurité & Traçabilité (Audit logs, Accès & permissions)
- Paramètres
Règles : @canany pour masquer les entrées non autorisées, badges de compteur pour items à traiter.

Statuts (badges)
- Consultation : en_attente (warning), en_cours (info), terminee (success), annulee (secondary/danger).
- Ordonnance : brouillon (secondary), validee/signee (success), expiree/annulee (danger).
- Facture : en_attente (warning), partiel (info), soldee (success), annulee (secondary).
- Paiement : initie/en_cours (info), confirme (success), echoue/annule (danger), rembourse (primary).

Positions des boutons
- Index : bandeau titre + bouton “Créer” (primary). Actions par ligne en colonne “Actions” (icônes + tooltip).
- Create/Edit : bas de formulaire : Enregistrer (primary), Enregistrer & continuer (secondary), Annuler (link).
- Show : zone actions à droite ou dans un bloc “Actions”, incluant Valider/Rejeter (modal motif) et Export.
- Suppressions : bouton danger dans dropdown ou actions, toujours confirm-modal.

Accès & sécurité
- @can/@canany sur actions et menus. Policies enregistrées (Consultation, Dossier, Ordonnance, Facture).
- Liens de téléchargement signés pour documents (téléconsultation, ordonnances).
- Rate limiting déjà en place (login/sensitive).

Accessibilité & qualité
- Labels explicites, aria-label sur icônes de boutons, aria-modal sur modals.
- Tables responsives (overflow-x), contrastes suffisants, messages d’erreur clairs.
- Loaders pour actions longues (export PDF, etc.).
