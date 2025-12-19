MODULE 9 — Audit Logs

A) Scope & Entities
- Tables : activity_log (Spatie activitylog), audit des accès et actions (create/update/delete/view) sur entités médicales.
- Models : pas de modèle dédié, utilisation via facade/activity().
- Relations : activity_log garde log_name, description, subject_type/id, causer_type/id, properties JSON, created_at.

B) Schéma DB
- Migration : activité Spatie (id big int, log_name index, description, subject, causer, properties JSON, batch_uuid, created_at).
- Index : log_name, subject_type/id, causer_type/id, batch_uuid.
- Seeders/Factories : none.

C) API/Controllers
- AuditLogController (présent, testé par Tests\Feature\AuditLogTest) pour lister/afficher logs (probablement backend).
- Routes : route vers AuditLogController (non détaillée ici, présente dans web.php).

D) Sécurité
- Accès aux logs réservé aux rôles admin/super-admin (à vérifier dans contrôleur/policy). Sensible car expose des traces.
- Données sensibles : properties JSON; veiller à ne pas exposer secrets (tokens) dans logs.

E) Tests
- Tests\Feature\AuditLogTest (présent, passe).

F) Livraison fichiers
- Migrations : existantes (activity_log).
- Models/Services : usage Spatie activitylog.
- Policies : à prévoir si besoin pour restreindre l’accès (non présent).
- Controllers : AuditLogController existant.
- Routes : existantes (web).
- Seeders/Factories : none.
- Tests : AuditLogTest.
- Docs module : présent fichier docs/modules/audit-logs.md.
