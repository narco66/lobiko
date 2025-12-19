MODULE 5 — Facturation

A) Scope & Entities
- Tables : factures (UUID, patient_id, praticien_id, structure_id, consultation_id, montants HT/TVA/TTC, part patient/assurance/subvention, reste_a_charge, statut_paiement, dates, nombre_paiements/relances), paiements (liés), commandes_pharmacie si vente, devis/assurances éventuels.
- Models : App\Models\Facture (relations patient, praticien, structure, consultation, paiements).
- Relations : Facture belongsTo patient(User), praticien(User), structure(StructureMedicale), consultation; hasMany paiements.
- Statuts : statut_paiement (en_attente, partiel, soldé, annulé), dates facture/échéance, delai_paiement.

B) Schéma DB
- Migration : `2025_08_11_184715_create_devis_table.php` contient la création des factures (vérifiée dans repo); FK sur patient_id/praticien_id/structure_id/consultation_id, numéro_facture unique, soft deletes, timestamps.
- Index/FK : indexes sur FK, unique numero_facture; reste_a_charge et montants décimaux; nombres paiements/relances ints.
- Seeders/Factories : FactureFactory présente (utilisée dans tests); à compléter pour différents statuts.

C) API/Controllers
- Controllers : FactureController (affichage, création depuis consultations, update montants, validation), PaiementController gère paiements liés.
- FormRequests : non explicites pour facture; validations probablement inline.
- Routes : web routes pour factures (resource ?), paiements via api `/payments`.
- Resources/Transformers : non API; Blade.

D) Sécurité
- Policies : pas de FacturePolicy explicite (à ajouter si besoin); paiements protégés via contrôleur et rôles.
- Permissions : Spatie roles (medecin, admin, patient accès à ses factures).
- Données sensibles : montants décimaux; pas de secrets; audit via activity_log possible.

E) Tests
- Tests : Feature\Feature\InvoiceTest (basique 200); Payment tests (idempotence/webhook) couvrent flux de paiement; ajouter tests de statut_paiement et reste_a_charge si nécessaire.

F) Livraison fichiers
- Migrations : existantes (factures dans 2025_08_11_184715_create_devis_table.php).
- Models : Facture existant.
- FormRequests : none ajoutés ici.
- Services : PaiementService couvre paiements; service facture non modifié.
- Policies : à créer si besoin; non modifié.
- Controllers : FactureController existant (non modifié).
- Routes : existantes (web pour factures, api pour payments).
- Seeders/Factories : FactureFactory existante.
- Tests : existants (InvoiceTest basique, Payment*).
- Docs module : présent fichier docs/modules/facturation.md.
