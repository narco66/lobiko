# Plan module « Médecins & Structures »

## 1. Inventaire de l’existant
- **structures_medicales** (migr. 2025_08_11_184707) : infos légales, adresse (lat/long), contacts, horaires_ouverture (JSON), services_disponibles, équipements, assurances, statut/validation, métriques, financier, soft deletes. FK `responsable_id`, `verified_by` → users. Index multiples.
- **user_structure** (pivot) : user_id, structure_id, role (praticien, assistant, secretaire, comptable, admin), actif, dates, pourcentage_honoraires, unique user/structure.
- **users** : colonnes métier (matricule, nom, prenom, date_naissance, sexe, telephone, email…), `specialite` nullable, `numero_ordre`, certification, géoloc, soft deletes, index sur specialite/certification.
- **actes_medicaux** (2025_08_11_184708) : catalogue d’actes avec categorie, specialite, tarifs, durées, flags (urgence/téléconsultation/domicile), statuts, soft deletes.
- **rendez_vous** (2025_08_11_184709) : non détaillé ici, mais contient specialite, type, orientation_specialiste, etc.
- **grilles_tarifaires / forfaits / produits_pharmaceutiques** : référentiels tarifaires/produits.
- Aucun schéma « doctors », « specialties », « medical_services », « doctor_schedule/absence » existant. Pas de controllers/vues métiers pour médecins/structures relevés. Menu backend actuel : dashboard, utilisateurs, dossiers médicaux, consultations, ordonnances, factures.

## 2. Périmètre cible
- **Référentiels** : specialties, medical_services.
- **Structures** : medical_structures (peut reprendre structures_medicales), structure_locations (optionnel multi-sites), structure_opening_hours.
- **Médecins** : doctors (liés user), doctor_specialty (pivot), doctor_structure (pivot multi-structures), doctor_schedules (disponibilités), doctor_absences.
- **Connexes** : rendez-vous/consultations déjà présents ; futurs liens vers doctor_id, structure_id, specialty_id à prévoir si champs manquent.

## 3. Relations clés
- User 1–1 Doctor (optionnel) ; Doctor n–n Structure via doctor_structure ; Doctor n–n Specialty via doctor_specialty.
- Structure 1–n StructureOpeningHour ; Structure n–n User via user_structure (déjà là).
- Doctor 1–n DoctorSchedule ; 1–n DoctorAbsence ; 1–n RendezVous/Consultations (à relier).
- Specialty 1–n ActeMedical ; Structure n–n Service.

## 4. Statuts & contraintes
- Structure : actif/suspendu/fermé/en_validation ; verified bool/date/user.
- Doctor : actif/suspendu/en_validation ; vérification des pièces (numero_ordre, certification).
- Disponibilités : pas de chevauchement, respect des heures d’ouverture structure.
- Unicités : code_structure unique (existant), matricule docteur unique, téléphone/email uniques, pivot unique (doctor_id + structure_id + rôle).

## 5. Écrans CRUD (backend Blade)
- Structures : index (filtres statut/ville/type), create/edit (infos légales, contact, horaires JSON), show (fiche + métriques), soft delete/désactivation.
- Médecins : index (filtres spécialité/structure/statut), create (identité, lien user, spécialités, structures), edit, show (profil, affectations, horaires).
- Spécialités, Services : CRUD simple (code/libellé/statut), filtres actif.
- Horaires/disponibilités : écran dédié par médecin (planning), création créneau, gestion absences.
- Affectations médecin/structure : modale/écran pour lier et définir rôle/part d’honoraires.

## 6. Permissions / Policies (spatie déjà présent)
- permissions : doctors.view/create/update/delete, doctors.assign, doctors.schedule.manage ; structures.view/create/update/delete ; specialties.view/create/update/delete ; services.view/create/update/delete.
- Menus conditionnels @canany sur ces permissions.

## 7. Plan d’implémentation
1. **DB** : ajouter migrations specialties, medical_services, doctors, doctor_specialty, doctor_structure, doctor_schedules, doctor_absences, structure_opening_hours (optionnel si on split), éventuellement compléter rendez_vous/consultations pour FK doctor/structure/specialty si manquants.
2. **Models** : Doctor, Specialty, MedicalService, MedicalStructure (wrap structures_medicales), DoctorSchedule, DoctorAbsence, pivots.
3. **Requests** : Store/Update pour doctor, structure, specialty, service, schedule.
4. **Services** : DoctorAssignmentService (affectations/partage honoraires), SchedulingService (vérif chevauchements, respect horaires structure).
5. **Policies** : par entité, registre dans AuthServiceProvider.
6. **Controllers** : resource pour structures, doctors, specialties, services ; schedule controller (store/update/destroy) ; actions assignStructure / detachStructure.
7. **Routes** : prefix admin/backoffice, middleware auth + permission.
8. **Vues** : Blade index/create/edit/show pour toutes entités, avec composants Lobiko, filtres, modals confirm.
9. **Seeders** : specialties/services sample, structures et doctors factices, affectations.
10. **Tests** : Feature CRUD + 403, tests métier (chevauchement horaires, assignation).

