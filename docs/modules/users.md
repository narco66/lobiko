MODULE 1 — Utilisateurs & Profils

A) Scope & Entities
- Tables : users, password_resets, personal_access_tokens, roles, permissions, model_has_roles, model_has_permissions (Spatie), activity_log (audit), sessions (session driver), dossiers_medicaux (lien patient), consultations (lien professionnel/patient).
- Models : App\Models\User (+HasRoles, HasApiTokens, SoftDeletes), relations dossiersMedicaux, consultations, roles/permissions.
- Relations clés : User hasMany Consultation (as patient/professionnel), hasOne DossierMedical, belongsToMany roles/permissions.
- Statuts : statut_compte (actif, suspendu, en_attente), certification_verified bool, two_factor_enabled bool.

B) Schéma DB
- Migrations existantes pour users (UUID, soft deletes), rôles/permissions (Spatie), sessions.
- Index/FK : roles/permissions via Spatie; FK dossier_medical.user_id, consultation.patient_id/professionnel_id; indexes email unique, matricule maybe unique (à confirmer).
- Seeders/Factories : UserFactory (supports rôles via states medecin/patient), rôles seeds via HomeController ensureRoles; à formaliser dans DatabaseSeeder.

C) API/Controllers
- Routes : auth scaffolding (`/login`, `/register`, `/profile`), dashboard (`/dashboard`) protégées par auth/verified.
- Controllers : Auth\* (login/register/forgot/reset), ProfileController (update profile/password), HomeController (stats + ensureRoles).
- FormRequests : RegisterRequest, Login (Fortify default), ProfileUpdateRequest.
- Resources : non-API dédié (Blade).

D) Sécurité
- Policies : ConsultationPolicy, DossierMedicalPolicy, OrdonnancePolicy déjà enregistrées. User policy absente (à ajouter si gestion admin requise).
- Permissions : Spatie roles/permissions, à s’assurer seed patient/medecin/admin.
- Données sensibles : casts encrypted pour 2FA (User), dossier médical/consultation déjà chiffrés. Masquer secrets via $hidden.
- Audit : activity_log présent (AuditLogTest), traçage login_count/last_login, à compléter si besoin.

E) Tests
- Auth feature tests complets (login/register/reset/email verification).
- Profile tests (update/password/delete) OK.
- Authorization tests pour consultations/dossiers en place.
- À ajouter si gestion admin utilisateur CRUD.

F) Livraison fichiers
- Aucune nouvelle migration/modèle requise pour ce module. Documentation ajoutée ici.
