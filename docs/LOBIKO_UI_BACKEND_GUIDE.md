# LOBIKO – Guide UI Backend

## Menus (sidebar/topbar)
- Grouper par domaine : Tableau de bord | Patients | Dossiers médicaux | Consultations | Ordonnances | Facturation | Paiements | Référentiels | Sécurité & Audit.
- Icônes FontAwesome cohérentes (`fa-gauge`, `fa-user-injured`, `fa-notes-medical`, `fa-stethoscope`, `fa-file-prescription`, `fa-file-invoice-dollar`, `fa-credit-card`, `fa-database`, `fa-shield-halved`).
- Visibilité conditionnelle : `@can` / `@canany` selon la policy/permission ; ne pas afficher les modules non autorisés.
- Badge de compteur pour items à traiter (ex : paiements en attente).
- Route naming cohérente : `admin.<module>.*` ou `backoffice.<module>.*` ; liens déclarés via `route()` uniquement.

## Boutons
- 1 seul bouton primaire par page (action principale).
- Secondaires en `outline`; actions destructrices jamais en primaire, toujours avec modal de confirmation.
- Libellés standards : `Créer`, `Enregistrer`, `Mettre à jour`, `Annuler`, `Retour`, `Supprimer`, `Valider`, `Rejeter`.
- Désactivation + spinner pendant la soumission (anti double-clic).
- Actions sensibles (valider/rejeter/annuler) : modal avec motif obligatoire + log d’audit.

## Pages CRUD (trame)
- Index : `page-header` (titre + bouton Créer), bloc filtres/recherche (query string persistée), tableau paginé, colonne “Actions” avec icônes + tooltips, empty state si nécessaire.
- Create/Edit : formulaires en cartes (sections), erreurs globales + par champ, footer avec `Enregistrer` (primaire) + `Annuler` (secondaire).
- Show : fiche synthèse en haut (statut via badge), onglets éventuels (Détails | Historique | Documents | Audit), zone d’actions contextualisées et protégées par `@can`.

## Statuts / badges
- Couleurs : `success` (validé), `warning` (en attente), `danger` (rejeté/annulé), `secondary` (brouillon), `info` (en cours).
- Utiliser le composant `x-lobiko.ui.badge-status`.

## Actions sensibles & sécurité
- Toujours protégées par policy/permission + affichage conditionnel.
- Confirmation modale (`x-lobiko.ui.confirm-modal`), motif obligatoire pour rejets.
- Pas d’exposition de données sensibles dans l’UI (données santé minimisées).
- Journaliser les actions critiques (audit log si disponible).

## Composants “Style LOBIKO”
- Page header : `x-lobiko.page-header`
- Boutons : `x-lobiko.buttons.primary|secondary|danger`
- Table : `x-lobiko.tables.datatable`
- Form inputs : `x-lobiko.forms.input|select|textarea`
- UI : `x-lobiko.ui.badge-status`, `x-lobiko.ui.confirm-modal`, `x-lobiko.ui.flash`, `x-lobiko.ui.empty-state`

## Conventions routes & flash
- Après création/màj : redirect vers index ou show avec `with('success', '...')`.
- Après suppression : redirect vers index `with('warning', 'Enregistrement supprimé')`.
- Messages d’erreur en `withErrors`.
