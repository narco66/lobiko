RUNBOOK — Exploitation & Incidents

1. Supervision
- Logs : storage/logs/laravel.log (rotation via serveur), activity_log (table).
- Santé : /up (route health), vérifier queues (`php artisan queue:failed`).

2. Déploiement
- Étapes : composer install --no-dev, npm build si frontend, php artisan migrate --force, php artisan config:cache, route:cache, view:cache.
- Vérifier connexions DB/Redis avant bascule.

3. Sauvegardes
- DB : dumps quotidiens (mysqldump) + rétention 30 jours.
- Fichiers : storage/app/public (documents téléconsultation, reçus) -> sauvegarde S3/FTP.

4. Maintenance
- Purge sessions : `php artisan security:purge-sessions 30`
- Purge logs : `php artisan security:purge-activity 180`
- Anonymisation RGPD : `php artisan security:anonymize-users 90`
- Index check : `php artisan migrate` pour appliquer les derniers index.

5. Incidents
- Perte DB : restaurer dernier dump, rejouer migrations si besoin.
- Fuite token webhook : régénérer `services.payments.webhook_secret`, invalider anciens.
- Téléconsultation inaccessible : vérifier TLS, provider jitsi/twilio credentials, purge cache `php artisan cache:clear`.
- Files manquantes : restaurer depuis sauvegarde storage/public.

6. Rôles & accès
- Rôles critiques : super-admin, admin, medecin, comptable, patient. Vérifier via Spatie (tables roles/model_has_roles).
- Changer mot de passe admin : via interface ou `php artisan tinker` (User::find()->update(['password'=>bcrypt(...)]) )

7. Performance
- Cache référentiels : `Cache::remember('referentiels.actes', 6h)` (AppServiceProvider).
- Index ajoutés sur consultations/factures (migration 2025_12_17_113124_add_indexes_for_consultations_and_factures).

8. Emails & jobs
- Files : QUEUE_CONNECTION=database. Lancer worker : `php artisan queue:work --tries=3`.
- Rejouer échecs : `php artisan queue:retry all` ou purge `queue:flush`.

9. Sécurité
- Rate limiting : login (5/min), sensitive (30/min) via RateLimiter (AppServiceProvider).
- Cookies de session sécurisés (secure=true, same_site=lax par défaut).
- Tokens téléconsultation chiffrés + expiration.
- Paiements : webhook HMAC `X-Signature` + idempotence.
