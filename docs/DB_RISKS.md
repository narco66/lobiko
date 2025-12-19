# DB RISKS — LOBIKO

## Indexes manquants / potentiels
- `users`: latitude/longitude non indexés (recherches proximité). last_login_at non indexé (tri).
- `consultations`: status + modalite pourraient être indexés pour filtres.
- `dossiers_medicaux`: patient_id index présent, OK.
- `teleconsultation_sessions`: status, token_expires_at non indexés (purges).
- `teleconsultation_messages/files`: session_id index implicite? Vérifier présence (non explicitée).
- `paiements`: payeur_id non indexé; transaction_id devrait être unique.
- `audit_logs`: entity_type/entity_id non indexés pour consultation.
- `stocks_medicaments`: statut_stock/date_expiration index OK; ajouter index sur produit_pharmaceutique_id pour reporting.
- `alertes_stock`: type_alerte index OK; ajouter index sur vue+traitee pour traitement.
- `pharmacy_requests` / `appointment_requests` / `insurance_requests` / `emergency_requests`: index status déjà présent pour pharmacy_requests; autres tables pas indexées sur status.
- `notifications`: user_id non indexé explicitement (vérifier).
- `evaluations`: evalue_id/evaluateur_id non indexés.
- `litiges`: declarant_id, statut non indexés.

## FK manquantes / incohérences
- Plusieurs tables de contenu (services, articles, etc.) sans FK explicites vers utilisateurs (author_id dans articles) — à confirmer dans migrations.
- `teleconsultation_sessions`/messages/files: FK consult/session/uploader déclarées mais vérifier onDelete (non spécifié pour certains).
- `paiements`: facture_id FK OK; payeur_id FK? (à confirmer dans migration).
- `reversements`: beneficiaire_id FK? vérifier.
- `evaluations`: evalue_id/evaluateur_id FK? vérifier.
- `notifications`: FK user_id? (migrations à confirmer).
- `insurance_requests`, `appointment_requests`, `emergency_requests`, `pharmacy_requests`: pas de FK (normal), mais statut enum limité? status string.

## Types / contraintes
- Données sensibles non chiffrées: dossiers_medicaux.*, consultations.constantes/prescriptions/examens/documents, ordonnances notes, teleconsultation tokens, 2FA secrets (users.two_factor_*), paiements.meta, assurance couvertures.
- Tokens téléconsultation stockés en clair (patient_token, practitioner_token).
- Champs `email` / `phone` de requêtes publiques sans validation DB (longueur, indexes).
- Tables financières: montants en decimal OK mais absence de contraintes CHECK (statut, positifs).
- `paiements.transaction_id` pas unique; risque de doublon.
- `consultations.constantes`, prescriptions, examens en json sans validation DB.
- `rendez_vous`: statut enum, pas de contrainte sur cohérence dates.

## Observations complémentaires
- Beaucoup de tables en UUID; vérifier collation/longueur index pour MySQL (OK avec default 36 chars).
- Soft deletes présents sur users, ordonnances, consultations? (consultations non soft deletes; ordonnances oui; produits pharmaceutiques, pharmacies).
- Absence d’index fulltext sur recherche (nom/prenom/prescripteur).

## Priorités
1) Chiffrement: dossiers_medicaux, consultations (constantes/prescriptions/examens/documents), ordonnances (notes, medicaments_non_disponibles), users two_factor_*, teleconsultation tokens, paiements.meta/transaction_id.
2) Tokens téléconsultation: hasher ou encrypt + expiration + rotation.
3) Paiements: transaction_id unique + FK payeur_id + statut enum + idempotence.
4) Audit: indexation audit_logs sur entity_type/entity_id, ajout FK user_id.
5) Indexes statut sur requêtes publiques (appointment/insurance/emergency).

