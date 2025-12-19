MODULE 6 — Paiements

A) Scope & Entities
- Tables : paiements (UUID, facture_id, payeur_id, commande_id, type_payeur, mode_paiement, montants, frais, montants nets, références transaction/passerelle/idempotence, statut, timestamps d’initiation/confirmation, champs passerelle, remboursement), factures (liées), commandes_pharmacie (optionnel).
- Models : App\Models\Paiement (casts décimaux, reponse_passerelle array, booleans, dates), relations facture/payeur/commande/reference.
- Relations : Paiement belongsTo Facture, User(payeur), CommandePharmacie; Facture hasMany Paiements.
- Statuts : initie, en_cours, confirme, echoue, annule, rembourse, timeout.

B) Schéma DB
- Migrations existantes pour paiements; migration ajout indexes uniques idempotence/reference et payeur_id (`2025_12_20_000002_add_indexes_to_paiements_table.php`).
- Index/FK : FK facture_id/payeur_id/commande_id; unique idempotence_key, unique reference_transaction (validation), index payeur_id.
- Seeders/Factories : PaiementFactory (montant 1000, idempotence set), FactureFactory pour rattacher.

C) API/Controllers
- Controllers : PaiementController (store + confirm + show) utilisant PaymentService.
- Services : PaymentService (idempotence, création transactionnelle, confirmation HMAC).
- Routes : API prefix payments (store, show, confirm) dans routes/api.php.
- FormRequests : PaiementRequest (validation stricte, unique idempotence/reference).
- Resources : PaiementResource (API).

D) Sécurité
- Policies : non spécifique; accès protégé par contrôleur + rôles si besoin. Signature HMAC webhook (`services.payments.webhook_secret`).
- Données sensibles : pas de secrets stockés; tokens passerelle non en clair (réponse passerelle en array). Audit via logs dans PaymentService et controller.
- Idempotence : header Idempotency-Key ou champ idempotence_key.

E) Tests
- tests/Feature/Payments/PaymentIdempotenceTest (idempotence store)
- tests/Feature/Payments/PaymentWebhookSignatureTest (signature HMAC valide/invalide)

F) Livraison fichiers
- Migrations : existantes (paiements + indexes).
- Models : Paiement (déjà chiffré pour reponse_passerelle array).
- FormRequests : PaiementRequest (déjà présent).
- Services : PaymentService (créé Phase 1).
- Policies : non ajoutées.
- Controllers : PaiementController (Phase 1).
- Routes : routes/api.php (prefix payments).
- Seeders/Factories : PaiementFactory existante.
- Tests : PaymentIdempotenceTest, PaymentWebhookSignatureTest.
- Docs module : présent fichier docs/modules/paiements.md.
