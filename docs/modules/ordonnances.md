MODULE 4 — Ordonnances

A) Scope & Entities
- Tables : ordonnances (UUID, patient_id, consultation_id, professionnel_id, structure_id, statut, prescriptions JSON, instructions, fichiers éventuels), lignes d’ordonnance si existantes (non vues ici), médicaments référentiels (pharm_products attendu en phase pharmacie).
- Models : App\Models\Ordonnance (relations patient, consultation, professionnel), Consultation met à jour dossier et peut générer ordonnance.
- Relations : Ordonnance belongsTo Consultation, patient(User), professionnel(User); Consultation hasMany ordonnances.
- Statuts : brouillon, validee, signee, expiree (à confirmer selon code existant).

B) Schéma DB
- Migration existante `2025_08_11_184715_create_devis_table.php` ? (à vérifier) et potentiellement d’autres; ordonnances tables déjà présentes (voir migrations ordonnances). FK sur consultation_id, patient_id, professionnel_id; UUID; timestamps; soft deletes possible.
- Index/FK : consultation_id, patient_id, professionnel_id; numéro unique si présent.
- Seeders/Factories : OrdonnanceFactory (si existante) sinon à ajouter; pas modifié ici.

C) API/Controllers
- Controllers : OrdonnanceController (déjà présent, authorizeResource ajouté en Phase 1), actions show/create/store/update, affichage via Blade.
- FormRequests : non explicit dans repo; validations probablement inline dans controller.
- Routes : resource ordonnances (web) protégées par auth/verified.
- Resources/Transformers : non API; Blade.

D) Sécurité
- Policies : OrdonnancePolicy (ajoutée Phase 1) avec autorisations patient/professionnel/admin.
- Permissions : Spatie roles medecin/patient/admin; contrôleur utilise authorizeResource.
- Données sensibles : prescriptions et documents chiffrés dans Consultation model (cast encrypted) ; vérifier champs ordonnances si JSON (non modifié ici).
- Audit : activity_log existant; à étendre pour create/update/delete ordonnances si besoin.

E) Tests
- Tests existants : Feature\Feature\PrescriptionTest (basique 200). Authorization tests pour ordonnances non présents; à ajouter si besoin workflow complet.

F) Livraison fichiers
- Migrations : existantes (aucune nouvelle).
- Models : Ordonnance déjà existant.
- FormRequests : non ajoutées ici.
- Services : non ajouté ici.
- Policies : OrdonnancePolicy déjà en place.
- Controllers : OrdonnanceController déjà en place (authorizeResource).
- Routes : resource ordonnances (déjà).
- Seeders/Factories : inchangés.
- Tests : inchangés (PrescriptionTest basique).
- Docs module : présent fichier docs/modules/ordonnances.md.
