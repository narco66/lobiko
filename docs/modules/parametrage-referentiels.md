MODULE 10 — Paramétrage / Référentiels

A) Scope & Entities
- Tables : référentiels métiers (ex. actes_medicaux, structures, pharmacies, pharm_categories/units/suppliers/products en phase Pharmacie, plan_comptable, settings divers). Rendez-vous modalités, listes spécialisations (users.specialite), etc.
- Models : StructureMedicale, ActeMedical, Pharmacie (et futurs modèles Pharm*), PlanComptable, Settings éventuels.
- Relations : structures liées aux consultations/rendez_vous; actes médicaux liés aux consultations; référentiels pharmacie liés aux commandes/paiements.
- Statuts : actif/inactif sur référentiels (à vérifier selon tables).

B) Schéma DB
- Migrations : create_plan_comptable_table, create_pharmacies_table, pharmacy requests, actes_medicaux, structures, etc.
- Index/FK : FK vers users/structures/consultations selon entités; uniques sur codes (actes, plan comptable, produits pharma).
- Seeders/Factories : non systématiques; à prévoir pour référentiels de base (ex. catégories pharma, unités, actes).

C) API/Controllers
- Controllers : StructuresController, ActesMedicaux, PharmacieController (recherche/haversine), SettingsController si présent.
- Routes : web resources pour référentiels (pharmacies, actes, structures), protégées par auth/verified; API pour recherche proximale (pharmacie/structures).
- FormRequests : validations ponctuelles dans controllers; à ajouter pour cohérence (ex. store/update référentiels).
- Resources : non API, Blade.

D) Sécurité
- Policies : selon entités (structures, actes, plan comptable) — à vérifier/ajouter; rôles admin/gestionnaire pour CRUD référentiels.
- Données sensibles : peu; s’assurer de la validation stricte (codes uniques, coordonnées float sécurisées contre injections Raw déjà corrigées).
- Audit : activity_log peut tracer les modifications des référentiels.

E) Tests
- Tests existants : Feature\Feature\* basiques (PharmacieController exemple), Haversine sécurisé; pas de tests CRUD référentiels complets — à ajouter si nécessaire.

F) Livraison fichiers
- Migrations : existantes (structures, actes, plan_comptable, pharmacies, pharmacy_requests).
- Models : existants (StructureMedicale, ActeMedical, Pharmacie, PlanComptable).
- FormRequests : non ajoutés ici.
- Services : non.
- Policies : à compléter si besoin.
- Controllers : existants (PharmacieController, etc.), sécurisés sur raw SQL (Phase 1).
- Routes : existantes.
- Seeders/Factories : non livrés ici.
- Tests : non livrés ici (à ajouter pour CRUD référentiels).
- Docs module : présent fichier docs/modules/parametrage-referentiels.md.
