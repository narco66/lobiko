MODULE 3 — Consultations

A) Scope & Entities
- Tables : consultations (UUID, patient_id, professionnel_id, structure_id, rendez_vous_id, dates/heures, modalite, motifs, diagnostiques, conduites, vitaux, status), rendez_vous (lien consultation), dossiers_medicaux (mis à jour par consultation), ordonnances (liées).
- Models : App\Models\Consultation (relations patient, professionnel, rendezVous, structure, ordonnance, dossierMedical), rendez-vous, dossier médical.
- Relations : Consultation belongsTo patient(User), professionnel(User), structure(StructureMedicale), rendezVous; hasMany ordonnances; updates dossier_medical.
- Statuts : statut (ex: planifiee, en_cours, terminee, annulee), modalite (teleconsultation/presentiel/domicile).

B) Schéma DB
- Migration existante `2025_08_11_184709_create_rendez_vous_table.php` crée consultations et dossiers_medicaux.
- Index/FK : patient_id, professionnel_id, structure_id, rendez_vous_id, numero_consultation (unique), soft deletes, timestamps. Dossiers_medicaux numero_dossier unique, index patient_id.
- Seeders/Factories : ConsultationFactory existante (utilisée dans tests). RendezVousFactory si présent.

C) API/Controllers
- Controllers : ConsultationController (création, show, vitaux, DME update, téléconsultation start/join, etc.). TeleconsultationController pour partie temps réel.
- FormRequests : (à ajouter si besoin pour validation stricte store/update) – actuellement géré inline dans ConsultationController.
- Routes : resources/consultations (? à confirmer dans routes/web/api), téléconsultation routes (teleconsultation.*) déjà en place.
- Resources/Transformers : non API; Blade.

D) Sécurité
- Policies : ConsultationPolicy (view/update patient ou professionnel, admin, medecin).
- Permissions : via Spatie roles (medecin, patient, admin). Autorisations appliquées dans ConsultationController (authorizeResource déjà en place).
- Données sensibles : champs JSON/array (examens, prescriptions, documents) chiffrés (casts encrypted ajoutés en Phase 1). Audit : activity_log présent.

E) Tests
- Tests Feature : Authorization/ConsultationPolicyTest, Feature\ConsultationTest (basic example), TeleconsultationTest (flux join/room). Ajouter si besoin CRUD complet consultation et workflow statut.

F) Livraison fichiers
- Migrations : existantes (consultations dans 2025_08_11_184709_create_rendez_vous_table.php).
- Models : App\Models\Consultation (déjà présent, avec casts encrypted).
- FormRequests : à créer si validation stricte souhaitée (non fourni ici).
- Services : ConsultationService déjà utilisé (voir ConsultationController); non modifié ici.
- Policies : ConsultationPolicy déjà en place.
- Controllers : ConsultationController déjà en place (authorizeResource).
- Routes : existantes (web.php pour consultations, teleconsultation routes).
- Seeders/Factories : ConsultationFactory existante.
- Tests : Authorization\ConsultationPolicyTest, Feature\ConsultationTest déjà présents.
- Docs : présent fichier docs/modules/consultations.md.
