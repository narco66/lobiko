# SCHEMA MAP — LOBIKO (Laravel 11)

Inventaire issu de la lecture des migrations dans `database/migrations`. Format : table (PK) — colonnes principales (type, nullable, index/FK), soft deletes, UUID si présent, indexes clés.

## Utilisateurs & Auth
- users (uuid, pk) — matricule, nom, prenom, date_naissance (date), sexe, téléphone, email(unique), email_verified_at, password, 2FA (two_factor_secret/recovery_codes), adresse_rue/quartier/ville/pays, latitude/longitude, specialite, numero_ordre, certification_verified(+_at/by), photo_profil, piece_identite (+numero/type), statut_compte, notifications_* (bool), login_count, last_login_at/ip, note_moyenne (decimal), nombre_evaluations, api_token; timestamps, soft deletes. Index: email unique.
- password_reset_tokens — email, token, created_at.
- sessions — id, user_id, ip_address, user_agent, payload, last_activity; indexes on user_id/last_activity.

## Structures médicales
- structures_medicales (uuid pk) — type(enum: clinique, hôpital, labo, imagerie, pharmacie, cabinet, autre), nom, slug, responsable_id FK users, siret, email, téléphone, adresse complète (rue/quartier/ville/pays), coords (lat/long), horaires (json), services(json), statut(enum actif/inactif), verification flags; timestamps, soft deletes. Indexes: type, ville, statut.
- user_structure (uuid pk) pivot — user_id FK users, structure_id FK structures_medicales, role, actif, dates début/fin, pourcentage_honoraires; timestamps; unique(user_id,structure_id).

## Actes & Produits
- actes_medicaux (uuid pk) — code_acte unique, libelle, description, categorie, specialite, tarifs (base/urgence/weekend/nuit/domicile), remboursable+taux, contraintes (prerequis, contre_indications json), sexe_requis, age_min/max, flags urgence/teleconsultation/domicile, actif+validité dates, soft deletes. Index: code_acte, categorie, specialite, actif.
- produits_pharmaceutiques (uuid pk) — code_produit unique, dci, nom_commercial, laboratoire, forme, dosage, conditionnement, voie_administration; classification (classe_therapeutique, famille, generique, princeps); prix_unitaire/prix_boite decimals; stocks mini/alerte ints; prescription_obligatoire bool, stupefiant, listes; conservation (conditions, temp min/max, date_peremption, numero_lot); remboursement flags/taux, codes cip/ucd; risques json (contre_indications, interactions, effets_secondaires, precautions); médias (image, notice_pdf, rcp_pdf); statut (disponible, rupture flags, dates), soft deletes. Index: code_produit, dci, nom_commercial, classe_therapeutique, disponible, prescription_obligatoire.
- grilles_tarifaires (uuid pk) — nom_grille, type_client enum, zone enum, structure_id FK structures_medicales nullable, applicable_a enum(acte/produit/tous), element_id, coeff/majoration/remise/tva decimals, conditions quantités/montants, validité dates, actif, priorite int; indexes type_client+zone, structure_id, element_id, dates, actif. FK structure_id.
- forfaits (uuid pk) — code_forfait unique, nom, description, categorie, prix_forfait, durée_validité, nombre_seances, compositions json (actes_inclus, produits_inclus, examens_inclus), conditions (age_min/max, sexe_requis, pathologies_cibles json), remboursable+taux, actif, soft deletes. Index: code_forfait, categorie, actif.

## Rendez-vous / Consultations / Dossiers
- rendez_vous (uuid pk) — patient_id FK users, professionnel_id FK users, structure_id FK structures_medicales nullable, date_heure, mode (presentiel/teleconsultation/domicile), statut enums, motif, notes; paiement info; timestamps. Indexes patient/professionnel/date/statut.
- consultations (uuid pk) — numero_consultation unique, patient_id, professionnel_id, structure_id, rendez_vous_id nullable, date_consultation datetime, heure_debut/fin, type, modalite, motif_consultation, diagnostic_principal/secondaire, conduite_a_tenir, notes_cliniques, constantes json, prescriptions json, examens json, documents json, statut enum, paiement info, soft deletes. FK vers users/structures/rendez_vous. Index numero_consultation, patient/date, professionnel/date, statut.
- dossiers_medicaux (uuid pk) — patient_id FK users unique?, groupe_sanguin, allergies json, antecedents json, traitements_en_cours json, vaccinations json, historique_familial json, habitudes_vie json, notes_privees text; timestamps. Index patient_id.

## Ordonnances & Stocks (pharmacie V1)
- ordonnances (uuid pk) — numero_ordonnance unique, consultation_id FK, patient_id, prescripteur_id, structure_id nullable, ordonnance_initiale_id nullable (renouvellement), type enum, nature enum, dates prescription/début/fin/validité, pathologie, code_cim10, ald flags, signature_numerique, qr_code unique, code_verification, hash_securite, dispensation info (autorise, substitutions, fractionnement, pharmacie_dispensatrice_id FK structures_medicales, pharmacien_dispensateur_id FK users, date_dispensation, dispensation_complete, medicaments_non_disponibles json), instructions/notes, contrôles interactions json, validation_pharmacien, documents (ordonnance_pdf, electronique/imprimee, imprimee_at), statut enum, timestamps, soft deletes. Index numero_ordonnance, qr_code, code_verification, patient/date, prescripteur/date, pharmacie_dispensatrice_id, statut, type.
- ordonnance_lignes (uuid pk) — ordonnance_id FK, produit_id FK produits_pharmaceutiques, ordre, quantite_prescrite, unite_prise, posologie, nombre_prises_jour, moments_prise, voie_admin, durée, dates, instructions, flags (a_jeun, pendant_repas, au_coucher), horaires json, dispensation (quantite_dispensee, produit_substitue_id FK, raison, date_dispensation, prix_unitaire/total), renouvellements, statut enum, timestamps. Index ordonnance_id+ordre, produit_id, statut.
- stocks_pharmacie (uuid pk) — pharmacie_id FK structures_medicales, produit_id FK produits_pharmaceutiques, quantite_stock/disponible/reservee, stock_minimum/securite/maximum, emplacement/zone, numero_lot/date_peremption/date_entree/fournisseur, prix_achat/vente/promotion, périodes promo, alertes flags+date, dernier_mouvement_at, rotation_mensuelle; unique (pharmacie_id, produit_id, numero_lot); indexes pharmacie_id, produit_id, date_peremption, quantite_disponible+stock_minimum.
- commandes_pharmacie (uuid pk) — numero_commande unique, patient_id FK users, pharmacie_id FK structures_medicales, ordonnance_id FK nullable, type enum, origine enum, urgente bool, montants (ht,tva,ttc,remise,final), livraison flags/adresse/coords/frais, livreur_id FK users, dates commande/preparation/prete/livraison, statut enum, validation pharmacien bool+id+date, paiement flags, références doc, notes, évaluation (note_service/commentaire), soft deletes. Index numero_commande, patient/date, pharmacie/date, ordonnance_id, statut, livreur_id.
- commande_lignes (uuid pk) — commande_id FK commandes_pharmacie, produit_id FK produits_pharmaceutiques, quantite, prix_unitaire/total, remise/taux/montant, prix_final, ordonnance_ligne_id FK, prescription_requise bool, substitution flags/produit_original_id FK, disponibilité info, timestamps. Index commande_id, produit_id, ordonnance_ligne_id.

## Pharmacies V2 (gestion)
- pharmacies (uuid pk) — structure_medicale_id FK, numero_licence unique, nom_pharmacie, nom_responsable, téléphone/email, adresse_complete, latitude/longitude, horaires json, service_garde bool, livraison_disponible bool, rayon_livraison_km, frais_livraison_base/par_km, paiements (mobile_money, carte, especes), statut enum, timestamps, soft deletes. Index: lat/long, statut, service_garde.
- stocks_medicaments (uuid pk) — pharmacie_id FK pharmacies, produit_pharmaceutique_id FK, quantite_disponible/int, min/max, prix_vente/achat, numero_lot, date_expiration, emplacement_rayon, prescription_requise, disponible_vente, statut_stock enum, timestamps. Unique (pharmacie_id, produit_pharmaceutique_id, numero_lot) (short name), indexes statut_stock, date_expiration, quantite_disponible.
- mouvements_stock (uuid pk) — stock_medicament_id FK, utilisateur_id FK users, type_mouvement enum (entree, sortie, ajustement, perime, retour), quantite, stock_avant/apres, reference_document, motif, prix_unitaire, timestamps. Index type_mouvement, created_at.
- commandes_pharmaceutiques (uuid pk) — numero_commande unique, patient_id FK users, pharmacie_id FK pharmacies, ordonnance_id FK ordonnances nullable, montants (total, assurance, patient), mode_retrait enum, adresse/coords livraison, frais_livraison, statut enum (workflow), dates (commande/preparation/retrait/livraison), code_retrait, instructions_speciales, urgent bool, timestamps, soft deletes. Index statut, numero_commande, date_commande.
- lignes_commande_pharma (uuid pk) — commande_pharmaceutique_id FK, produit_pharmaceutique_id FK, stock_medicament_id FK nullable, quantite_commandee/livree, prix_unitaire, montant_ligne, taux/montant_remboursement, posologie, durée_traitement, substitution info (produit_substitue_id FK, motif), timestamps. Index commande_pharmaceutique_id.
- livraisons_pharmaceutiques (uuid pk) — commande_pharmaceutique_id FK, livreur_id FK users nullable, numero_livraison unique, statut enum, dates, nom/telephone/signature/photo réceptionnaire, commentaire/motif_echec, tracking_gps json, distance_parcourue_km, timestamps. Index statut, numero_livraison.
- alertes_stock (uuid pk) — pharmacie_id FK pharmacies, stock_medicament_id FK, type_alerte enum (stock_faible, rupture_stock, expiration_proche, expire), message, vue bool, traitee bool, date_traitement, traite_par FK users, action_prise, timestamps. Index (pharmacie_id, vue), type_alerte.
- fournisseurs_pharmaceutiques (uuid pk) — nom, numero_licence unique, telephone/email, adresse, personne_contact, telephone_contact, categories_produits json, delai_livraison_jours, montants minimums, statut enum, timestamps, soft deletes.
- pharmacie_fournisseur (uuid pk) pivot — pharmacie_id FK pharmacies, fournisseur_id FK fournisseurs_pharmaceutiques, numero_compte_client, statut enum, credit_maximum/utilise, timestamps, unique(pharmacie_id,fournisseur_id).
- pharmacy_requests (uuid pk) — full_name, phone, email, prescription_code, delivery_mode enum, address, notes, status(default pending), timestamps, index status.

## Téléconsultation
- teleconsultation_sessions (uuid pk) — consultation_id FK consultations unique, status enum (pending/live/ended?), provider, room_name, patient_token, practitioner_token, token_expires_at, started_at/ended_at, created_at/updated_at.
- teleconsultation_messages (uuid pk) — session_id FK teleconsultation_sessions, sender_id FK users, message, type, read_at, created_at/updated_at.
- teleconsultation_files (uuid pk) — session_id FK teleconsultation_sessions, uploader_id FK users, original_name, path, mime_type, size, created_at/updated_at.

## Assurance / Urgences / Appels
- insurance_requests (uuid pk) — patient info (name, phone, email), insurance details (provider, policy_number, coverage, notes), status, timestamps.
- emergency_requests (uuid pk) — full_name, phone, description, location, latitude/longitude, status, timestamps.
- appointment_requests (uuid pk) — full_name, phone, email, speciality, preferred_datetime, notes, status, timestamps.

## Contact / CMS
- contact_messages (uuid pk) — name, email, subject, message, status, timestamps.
- testimonials (uuid pk) — user_id FK users nullable, message, rating, is_published, timestamps.
- article_categories (uuid pk) — name, slug, description, timestamps.
- articles (uuid pk) — category_id FK, author_id FK users, title, slug, excerpt, content, image, is_published, published_at, seo fields, timestamps.
- services (uuid pk) — title, slug, description, icon, order, is_active, timestamps.
- statistiques (uuid pk) — key, value (json), timestamps.
- faqs (uuid pk) — question, answer, category, order, is_active, timestamps.
- partners (uuid pk) — name, logo, url, order, is_active, timestamps.
- newsletter_subscribers (uuid pk) — email, status, subscribed_at, unsubscribed_at, timestamps.
- custom_pages (uuid pk) — title, slug, content, meta, is_published, timestamps.
- banners (uuid pk) — title, image, link, order, is_active, timestamps.
- team_members (uuid pk) — name, role, photo, bio, order, is_active, timestamps.
- job_offers (uuid pk) — title, description, location, type, salary, published_at, status, timestamps.
- job_applications (uuid pk) — offer_id FK, name, email, phone, cv_path, status, timestamps.

## Comptabilité / Facturation
- plan_comptable (uuid pk) — code, intitule, type, parent_id FK, niveau, sens, actif; timestamps, soft deletes. Index code, parent_id.
- journaux_comptables (uuid pk) — code unique, intitule, type, structure_id FK, devise, actif; timestamps.
- ecritures_comptables (uuid pk) — journal_id FK, reference, date_ecriture, description, structure_id FK, statut, total_debit/credit, devise, user_id FK, piece_jointe, timestamps. Index journal_id, date_ecriture, statut.
- lignes_ecritures (uuid pk) — ecriture_id FK, compte_id FK plan_comptable, description, debit, credit, axe_analytique, ref_piece, timestamps. Index ecriture_id, compte_id.
- rapprochements_bancaires (uuid pk) — compte_bancaire, periode_debut/fin, solde_initial/final, ecarts, statut, notes, timestamps.
- notifications (uuid pk) — user_id FK, type, data json, read_at, timestamps.
- audit_logs (uuid pk) — user_id FK, action, entity_type, entity_id, metadata json, ip_address, user_agent, created_at.
- litiges (uuid pk) — declarant_id FK users, type, description, statut, resolution, timestamps.
- evaluations (uuid pk) — evaluateur_id FK users, evalue_id FK users, type_evalue, note_globale decimal, recommande bool, commentaire, timestamps.
- devis (uuid pk) — numero_devis unique, patient_id FK users, structure_id FK, date_devis, montant_ht/tva/ttc/remise, statut enum, type (consultation, pharmacie, etc.), commande_pharmacie_id FK nullable, reference_id, timestamps, soft deletes. Index numero_devis, patient/date, structure/date, statut.
- devis_lignes (uuid pk) — devis_id FK, description, quantite, prix_unitaire, montant, taxes, timestamps.
- factures (uuid pk) — numero_facture unique, patient_id FK, praticien_id FK users, structure_id FK, date_facture, montant_ht/tva/ttc/remise, statut, mode_paiement, commande_id nullable, commande_pharmacie_id nullable, contrat_assurance_id nullable, montant_pec, timestamps, soft deletes. Index numero_facture, patient/date, statut.
- facture_lignes (uuid pk) — facture_id FK, description, quantite, prix_unitaire, montant, taxes, timestamps.
- paiements (uuid pk) — facture_id FK factures, payeur_id FK users, reference_paiement, montant, mode, statut, transaction_id, meta json, timestamps. Index facture_id, statut.
- reversements (uuid pk) — beneficiaire_id FK users, type_beneficiaire, montant, reference, statut, date_reversement, timestamps. Index beneficiaire_id, statut.

## Assurances
- compagnies_assurance (uuid pk) — nom, code, contact, garanties json, taux_couverture_* decimal, plafonds, statut, timestamps.
- contrats_assurance (uuid pk) — compagnie_id FK, patient_id FK, numero_police, date_debut/fin, statut, couverture json, taux_couverture_* decimal, plafonds, timestamps.
- prises_en_charge (uuid pk) — contrat_id FK, facture_id FK, montant_demande/montant_accorde, statut, documents, timestamps.
- remboursements_assurance (uuid pk) — contrat_id FK, facture_id FK, montant, statut, références, timestamps.

## Divers
- cache/cache_locks/jobs/job_batches/failed_jobs — infra queue/cache standard.

Sensible data: dossiers_medicaux (allergies, antécédents, traitements, vaccinations, historique_familial, habitudes_vie, notes_privees), consultations.constantes/prescriptions/examens/documents, ordonnances, paiements.transaction_id/meta, teleconsultation tokens, 2FA secrets, audit_logs IP, user coords, assurance données.

