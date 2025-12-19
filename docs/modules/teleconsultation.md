MODULE 7 — Téléconsultation

A) Scope & Entities
- Tables : teleconsultation_sessions (consultation_id, status, provider, room_name, patient_token, practitioner_token, token_expires_at, started_at, ended_at, metadata), teleconsultation_messages, teleconsultation_files. Consultations table porte modalite=teleconsultation.
- Models : TeleconsultationSession (tokens chiffrés, expiration), TeleconsultationMessage, TeleconsultationFile, Consultation (lien session).
- Relations : Session belongsTo Consultation; Session hasMany messages/files; Consultation hasOne teleconsultationSession; Files/messages belongTo Session; users accèdent via consultation patient/professionnel.
- Statuts : pending, live, ended (session.status).

B) Schéma DB
- Migrations : 2025_08_13_000001_create_teleconsultation_sessions_table.php, 000002_messages, 000004_files, 000003_add_tokens (patient_token, practitioner_token, token_expires_at).
- Index/FK : FK consultation_id; files/messages FK session_id; timestamps. Tokens longueur 64 chars.
- Seeders/Factories : non spécifiques (sessions/messages/files créés en tests).

C) API/Controllers
- Controllers : TeleconsultationController (room/join/leave/end/sendMessage/shareFile/downloadFile), ConsultationController pour startTeleconsultation.
- Routes : teleconsultation.* (room, join, leave, end, file download) protégées par auth/authorize.
- FormRequests : validations inline (join, sendMessage, shareFile).
- Resources : non API; JSON responses pour join/messages/files.

D) Sécurité
- Policies : accès contrôlé par authorizeAccess (patient ou professionnel de la consultation).
- Permissions : rôles patient/professionnel; middleware auth.
- Données sensibles : patient_token/practitioner_token chiffrés (casts encrypted), expiration token + régénération; liens de téléchargement signés; fichiers MIME vérifiés et limités.
- Audit : logs pour start/end/upload; signatures temporaires pour download.

E) Tests
- tests/Feature/TeleconsultationTest : patient_can_view_room, join_sets_live_and_returns_token, signed_file_download, tokens_regenerate_after_expiry, unauthorized_user_cannot_join.

F) Livraison fichiers
- Migrations : existantes (sessions/messages/files/tokens).
- Models : TeleconsultationSession (casts encrypted tokens, expiration), TeleconsultationMessage, TeleconsultationFile.
- FormRequests : validations inline dans controller.
- Services : non spécifiques (géré dans TeleconsultationController).
- Policies : authorizeAccess custom dans contrôleur.
- Controllers : TeleconsultationController (tokens 64 chars, régénération, contrôle accès, fichiers signés).
- Routes : téléconsultation.* existantes.
- Seeders/Factories : non fournis.
- Tests : TeleconsultationTest (mis à jour Phase 1).
- Docs module : présent fichier docs/modules/teleconsultation.md.
