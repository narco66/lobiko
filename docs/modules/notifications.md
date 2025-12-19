MODULE 8 — Notifications

A) Scope & Entities
- Tables : notifications (Laravel default morph table), activity_log (audit), users flags notifications_sms/email/push (colonne sur users).
- Models : App\Models\User (Notifiable), Notification classes éventuelles (à recenser), logs.
- Relations : notifications morph to notifiable (User).
- Statuts : read_at pour notifications.

B) Schéma DB
- Migration : default notifications table (UUID id, type, notifiable_type/id, data JSON, read_at, timestamps).
- Index : index notifiable_type/notifiable_id, read_at.
- Seeders/Factories : none pour notifications; UserFactory gère flags notifications_*.

C) API/Controllers
- Envoi via facades Notification / Mail; pas de controller dédié repéré. AuditLogController existe pour logs.
- Routes : non spécifiques; consommées en backend (events).

D) Sécurité
- Notifications limitées aux notifiable_id; aucune donnée sensible en clair dans data (à vérifier); privilégier payload minimal.
- Audit : activity_log déjà en place pour traçabilité.

E) Tests
- Tests dédiés aux notifications non présents; à ajouter si besoin (ex: envoi sur action, markAsRead).

F) Livraison fichiers
- Migrations : notifications (existante).
- Models : User (Notifiable).
- FormRequests/Services/Controllers : non ajoutés ici.
- Policies : non applicable.
- Routes : aucune.
- Seeders/Factories : inchangés.
- Tests : non livrés.
- Docs module : présent fichier docs/modules/notifications.md.
