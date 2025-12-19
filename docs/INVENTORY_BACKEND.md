# INVENTORY BACKEND — LOBIKO

## Models (app/Models)
ActeMedical.php; AppointmentRequest.php; Article.php; ArticleCategory.php; AuditLog.php; Banner.php; CommandeLigne.php; CommandePharmaceutique.php; CommandePharmacie.php; CompagnieAssurance.php; Consultation.php; ContactMessage.php; ContratAssurance.php; CustomPage.php; Devis.php; DevisLigne.php; DossierMedical.php; EcritureComptable.php; EmergencyRequest.php; Evaluation.php; Facture.php; FactureLigne.php; Faq.php; Forfait.php; GrilleTarifaire.php; Incident.php; InsuranceRequest.php; JobApplication.php; JobOffer.php; JournalComptable.php; LigneEcriture.php; Litige.php; Livraison.php; NewsletterSubscriber.php; Notification.php; Ordonnance.php; OrdonnanceLigne.php; Paiement.php; Partner.php; PharmacyRequest.php; PlanComptable.php; PriseEnCharge.php; ProduitPharmaceutique.php; RapprochementBancaire.php; RemboursementAssurance.php; RendezVous.php; Reversement.php; Service.php; Statistique.php; StockPharmacie.php; StructureMedicale.php; TeamMember.php; TeleconsultationFile.php; TeleconsultationMessage.php; TeleconsultationSession.php; Testimonial.php; User.php.

## Controllers (app/Http/Controllers)
Root: ActeMedicalController.php; AgendaController.php; AssuranceController.php; AuditController.php; CatalogueController.php; CommandePharmaceutiqueController.php; CommandePharmacieController.php; ComptabiliteController.php; ConsultationController.php; ContratAssuranceController.php; Controller.php; DashboardController.php; DevisController.php; DispensationController.php; DossierMedicalController.php; EvaluationController.php; FactureController.php; ForfaitController.php; GrilleTarifaireController.php; HomeController.php; LitigeController.php; NotificationController.php; OrdonnanceController.php; PaiementController.php; PatientController.php; PharmacieController.php; PriseEnChargeController.php; ProduitPharmaceutiqueController.php; ProfileController.php; PublicServiceRequestController.php; PublicServicesController.php; RapportController.php; RemboursementAssuranceController.php; RendezVousController.php; ReversementController.php; SettingsController.php; StockPharmacieController.php; StructureMedicaleController.php; TeleconsultationController.php; UserController.php.
Subdirs: Admin/*; Auth/* (login/register/etc.).

## FormRequests (app/Http/Requests)
ProfileUpdateRequest.php plus éventuels dans modules (non listés exhaustivement ici : rechercher `app/Http/Requests`).

## Policies (app/Policies)
Non listées explicitement (répertoire à créer pour phase 1).

## Services (app/Services)
Present: GeolocationService?, PaymentService? (à confirmer en phase 1) — inventaire détaillé non présent dans arborescence par défaut (répertoire Services absent ou vide).

## Providers
App\Providers\AuthServiceProvider.php; BroadcastServiceProvider.php; EventServiceProvider.php; RouteServiceProvider.php.

## Seeders (database/seeders)
ActesMedicauxSeeder.php; CompagniesAssuranceSeeder.php; ConsultationsSeeder.php; ContratAssuranceSeeder.php; DatabaseSeeder.php; DossiersMedicauxSeeder.php; FactoryDemoSeeder.php; FacturesSeeder.php; ForfaitsSeeder.php; GrillesTarifairesSeeder.php; MarketingSeeder.php; OrdonnancesSeeder.php; PharmacieSeeder.php; PlanComptableSeeder.php; ProduitsPharmaceutiquesSeeder.php; RendezVousSeeder.php; RolesAndPermissionsSeeder.php; StructuresMedicalesSeeder.php; UsersSeeder.php; PharmSeeder.php (placeholder).

## Factories (database/factories)
UserFactory.php; plus autres générés par default? (vérifier répertoire pour produire liste exhaustive).

## Routes
- routes/web.php — front + tableau de bord; pharmacie, consultations, etc.
- routes/api.php — API endpoints (à inspecter en phase 1).

## Tests
- Unit: Tests\Unit\* + Tests\Unit\Unit\* (ConsultationServiceTest, GeolocationServiceTest, PaymentServiceTest, UserTest).
- Feature: Auth\* (login/register/verification), AuditLogTest, ExampleTest, ProfileTest, TeleconsultationTest, Feature subfolder (AuthenticationTest, ConsultationTest, InvoiceTest, PaymentTest, PrescriptionTest, RendezVousTest), PublicPagesTest.

## Jobs / Events / Listeners
Non inventoriés spécifiquement (répertoires à inspecter lors des phases suivantes). Aucun fichier listé dans arborescence immédiate.

## Observations
- Spatie Permission utilisé (RolesAndPermissionsSeeder, User HasRoles).
- Nombreux contrôleurs sans Policies associées (à corriger).
- Aucun dossier Services/Policies créé pour audits critiques.

