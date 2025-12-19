UI BACKEND MAP

Layout & Navigation
- Layout principal : resources/views/layouts/app.blade.php (navbar top). Pas de sidebar dédiée backend (à ajouter). Breadcrumbs absents.
- Navigation actuelle : Accueil, Services (dropdown), Backend/Admin (simple lien vers dashboard).

Modules et vues repérées (resources/views/*)
- Auth (login/register/reset/verify) : resources/views/auth/*.blade.php
- Dashboard : resources/views/dashboard/*.blade.php (admin/financial/medical/patient/pharmacist vides)
- Consultations : resources/views/consultations (listes/flux ? non explicit, contrôleur ConsultationController gère affichage)
- Dossier médical : dossiers_medicaux contrôleur présent, vues manquantes (à créer)
- Ordonnances : resources/views/ordonnances/{create,index,show}.blade.php
- Factures : vues non trouvées (FactureController squelette)
- Téléconsultation : resources/views/teleconsultation/room.blade.php
- Pharmacie : resources/views/pharmacie/index.blade.php (public/recherche)
- Services publics : resources/views/services/*.blade.php (pharmacy_request, insurance_request, etc.)
- Profile : resources/views/profile/edit + partials
- Admin requests : resources/views/admin/requests/index.blade.php
- Autres : dossiers rendez-vous, dossiers médicaux intégrés aux pages consultations/structures (non consolidés).

Routes backend (routes/web.php)
- Dashboard route('dashboard')
- Services publics (pharmacy, assurance, emergency)
- DossierMedical routes absentes -> à ajouter (resource)
- Consultation routes existantes via ConsultationController (à vérifier pour index/create/show/edit)
- Ordonnance routes existantes (authorizeResource ajouté)
- Facture routes non configurées
- Téléconsultation routes (room/join/leave/end/files)

Incohérences / manques
- Pas de sidebar backend ; navigation dispersée.
- Breadcrumbs absents ; flash messages hétérogènes.
- Vues dossiers médicaux manquantes ; FactureController sans vues.
- Boutons/CTA non standardisés (multiples styles, pas de confirmation).
- Actions sensibles non confirmées (delete/annuler).

Navigation proposée (backend)
- Tableau de bord
- Patients
- Dossiers médicaux
- Consultations
- Ordonnances
- Téléconsultation
- Facturation (Factures, Paiements)
- Référentiels (Structures, Actes médicaux, Pharmacie*)
- Sécurité & Traçabilité (Audit logs, Accès & permissions)
- Paramètres

Points à refondre en priorité
- Créer trame backend (sidebar, topbar, breadcrumbs, flash unifiés).
- Ajouter vues CRUD Dossier médical cohérentes.
- Standardiser pages consultations/ordonnances (header, actions, status badges).
- Gating @can sur actions sensibles ; confirm modals pour delete/valider.
