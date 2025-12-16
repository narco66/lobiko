<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Table des ordonnances
        Schema::create('ordonnances', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('numero_ordonnance')->unique();
            $table->uuid('consultation_id')->nullable();
            $table->uuid('patient_id');
            $table->uuid('prescripteur_id');
            $table->uuid('structure_id')->nullable();

            // Type et nature
            $table->enum('type', ['simple', 'bizone', 'exception', 'stupefiant']);
            $table->enum('nature', ['initiale', 'renouvellement', 'modification']);
            $table->uuid('ordonnance_initiale_id')->nullable(); // Si renouvellement

            // Dates
            $table->date('date_prescription');
            $table->date('date_debut_traitement')->nullable();
            $table->date('date_fin_traitement')->nullable();
            $table->integer('duree_traitement')->nullable(); // En jours
            $table->date('valide_jusqu_au');

            // Pathologie
            $table->string('pathologie')->nullable();
            $table->string('code_cim10')->nullable();
            $table->boolean('ald')->default(false); // Affection Longue Durée
            $table->string('numero_ald')->nullable();

            // Sécurisation
            $table->string('signature_numerique');
            $table->string('qr_code')->unique();
            $table->string('code_verification', 6); // Code à 6 chiffres
            $table->string('hash_securite'); // Hash pour vérifier l'intégrité

            // Dispensation
            $table->boolean('dispensation_autorisee')->default(true);
            $table->integer('nombre_renouvellements')->default(0);
            $table->integer('renouvellements_effectues')->default(0);
            $table->boolean('substitution_autorisee')->default(true);
            $table->boolean('fractionnement_autorise')->default(false);

            // Pharmacie
            $table->uuid('pharmacie_dispensatrice_id')->nullable();
            $table->uuid('pharmacien_dispensateur_id')->nullable();
            $table->timestamp('date_dispensation')->nullable();
            $table->boolean('dispensation_complete')->default(false);
            $table->json('medicaments_non_disponibles')->nullable();

            // Instructions et notes
            $table->text('instructions_patient')->nullable();
            $table->text('notes_pharmacien')->nullable();
            $table->text('conseils_associes')->nullable();

            // Contrôles
            $table->boolean('controle_interactions')->default(false);
            $table->json('interactions_detectees')->nullable();
            $table->boolean('validation_pharmacien')->default(false);
            $table->timestamp('validation_pharmacien_at')->nullable();

            // Documents
            $table->string('ordonnance_pdf')->nullable();
            $table->boolean('electronique')->default(true);
            $table->boolean('imprimee')->default(false);
            $table->timestamp('imprimee_at')->nullable();

            // Statut
            $table->enum('statut', [
                'active',
                'dispensee',
                'partiellement_dispensee',
                'expiree',
                'annulee'
            ])->default('active');

            $table->timestamps();
            $table->softDeletes();

            // Index
            $table->index('numero_ordonnance');
            $table->index('qr_code');
            $table->index('code_verification');
            $table->index(['patient_id', 'date_prescription']);
            $table->index(['prescripteur_id', 'date_prescription']);
            $table->index('pharmacie_dispensatrice_id');
            $table->index('statut');
            $table->index('type');

            // Foreign keys
            $table->foreign('consultation_id')->references('id')->on('consultations')->nullOnDelete();
            $table->foreign('patient_id')->references('id')->on('users')->restrictOnDelete();
            $table->foreign('prescripteur_id')->references('id')->on('users')->restrictOnDelete();
            $table->foreign('structure_id')->references('id')->on('structures_medicales')->nullOnDelete();
            $table->foreign('ordonnance_initiale_id')->references('id')->on('ordonnances')->nullOnDelete();
            $table->foreign('pharmacie_dispensatrice_id')->references('id')->on('structures_medicales')->nullOnDelete();
            $table->foreign('pharmacien_dispensateur_id')->references('id')->on('users')->nullOnDelete();
        });

        // Table des lignes d'ordonnance
        Schema::create('ordonnance_lignes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('ordonnance_id');
            $table->uuid('produit_id');
            $table->integer('ordre')->default(1);

            // Prescription
            $table->integer('quantite_prescrite');
            $table->string('unite_prise'); // comprimé, ml, gouttes, etc.
            $table->string('posologie'); // Ex: 2 comprimés 3 fois par jour
            $table->integer('nombre_prises_jour')->nullable();
            $table->string('moments_prise')->nullable(); // matin, midi, soir
            $table->string('voie_administration');
            $table->integer('duree_traitement')->nullable(); // En jours
            $table->date('date_debut')->nullable();
            $table->date('date_fin')->nullable();

            // Instructions spécifiques
            $table->text('instructions')->nullable(); // Avant/après repas, etc.
            $table->boolean('a_jeun')->default(false);
            $table->boolean('pendant_repas')->default(false);
            $table->boolean('au_coucher')->default(false);
            $table->json('horaires_prise')->nullable(); // Horaires spécifiques

            // Dispensation
            $table->integer('quantite_dispensee')->default(0);
            $table->uuid('produit_substitue_id')->nullable();
            $table->string('raison_substitution')->nullable();
            $table->timestamp('date_dispensation')->nullable();
            $table->decimal('prix_unitaire', 10, 2)->nullable();
            $table->decimal('prix_total', 10, 2)->nullable();

            // Renouvellement
            $table->boolean('renouvellable')->default(true);
            $table->integer('nombre_renouvellements')->default(0);
            $table->integer('renouvellements_effectues')->default(0);

            // Statut
            $table->enum('statut', [
                'prescrit',
                'dispense',
                'partiellement_dispense',
                'non_dispense',
                'substitue'
            ])->default('prescrit');

            $table->timestamps();

            // Index
            $table->index(['ordonnance_id', 'ordre']);
            $table->index('produit_id');
            $table->index('statut');

            // Foreign keys
            $table->foreign('ordonnance_id')->references('id')->on('ordonnances')->cascadeOnDelete();
            $table->foreign('produit_id')->references('id')->on('produits_pharmaceutiques')->restrictOnDelete();
            $table->foreign('produit_substitue_id')->references('id')->on('produits_pharmaceutiques')->nullOnDelete();
        });

        // Table des stocks pharmacie
        Schema::create('stocks_pharmacie', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('pharmacie_id');
            $table->uuid('produit_id');

            // Quantités
            $table->integer('quantite_stock');
            $table->integer('quantite_reservee')->default(0);
            $table->integer('quantite_disponible');
            $table->integer('stock_minimum');
            $table->integer('stock_securite');
            $table->integer('stock_maximum')->nullable();

            // Emplacement
            $table->string('emplacement')->nullable(); // Rayon, étagère
            $table->string('zone_stockage')->nullable(); // Frigo, armoire, etc.

            // Lot et péremption
            $table->string('numero_lot')->nullable();
            $table->date('date_peremption')->nullable();
            $table->date('date_entree')->nullable();
            $table->string('fournisseur')->nullable();

            // Prix
            $table->decimal('prix_achat', 10, 2);
            $table->decimal('prix_vente', 10, 2);
            $table->decimal('taux_marge', 5, 2)->nullable();
            $table->decimal('prix_promotion', 10, 2)->nullable();
            $table->date('debut_promotion')->nullable();
            $table->date('fin_promotion')->nullable();

            // Alertes
            $table->boolean('alerte_stock_bas')->default(false);
            $table->boolean('alerte_peremption')->default(false);
            $table->timestamp('derniere_alerte_at')->nullable();

            // Mouvements
            $table->timestamp('dernier_mouvement_at')->nullable();
            $table->integer('rotation_mensuelle')->default(0);

            $table->timestamps();

            // Index
            $table->unique(['pharmacie_id', 'produit_id', 'numero_lot']);
            $table->index('pharmacie_id');
            $table->index('produit_id');
            $table->index('date_peremption');
            $table->index(['quantite_disponible', 'stock_minimum']);

            // Foreign keys
            $table->foreign('pharmacie_id')->references('id')->on('structures_medicales')->cascadeOnDelete();
            $table->foreign('produit_id')->references('id')->on('produits_pharmaceutiques')->cascadeOnDelete();
        });

        // Table des commandes pharmacie
        Schema::create('commandes_pharmacie', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('numero_commande')->unique();
            $table->uuid('patient_id');
            $table->uuid('pharmacie_id');
            $table->uuid('ordonnance_id')->nullable();

            // Type et origine
            $table->enum('type', ['ordonnance', 'libre', 'conseil']);
            $table->enum('origine', ['comptoir', 'telephone', 'web', 'app']);
            $table->boolean('urgente')->default(false);

            // Montants
            $table->decimal('montant_ht', 10, 2);
            $table->decimal('montant_tva', 10, 2)->default(0);
            $table->decimal('montant_ttc', 10, 2);
            $table->decimal('montant_remise', 10, 2)->default(0);
            $table->decimal('montant_final', 10, 2);

            // Livraison
            $table->boolean('livraison')->default(false);
            $table->enum('mode_livraison', ['domicile', 'point_relais', 'retrait'])->nullable();
            $table->string('adresse_livraison')->nullable();
            $table->decimal('latitude_livraison', 10, 8)->nullable();
            $table->decimal('longitude_livraison', 11, 8)->nullable();
            $table->decimal('frais_livraison', 10, 2)->default(0);
            $table->uuid('livreur_id')->nullable();

            // Planning
            $table->dateTime('date_commande');
            $table->dateTime('date_preparation')->nullable();
            $table->dateTime('date_prete')->nullable();
            $table->dateTime('date_livraison_prevue')->nullable();
            $table->dateTime('date_livraison_effective')->nullable();

            // Statut
            $table->enum('statut', [
                'en_attente',
                'confirmee',
                'en_preparation',
                'prete',
                'en_livraison',
                'livree',
                'retiree',
                'annulee'
            ])->default('en_attente');

            // Validation et paiement
            $table->boolean('valide_pharmacien')->default(false);
            $table->uuid('pharmacien_validateur_id')->nullable();
            $table->timestamp('valide_at')->nullable();
            $table->boolean('payee')->default(false);
            $table->string('reference_paiement')->nullable();

            // Documents et preuves
            $table->string('bon_livraison')->nullable();
            $table->string('signature_client')->nullable();
            $table->string('photo_livraison')->nullable();
            $table->string('code_retrait')->nullable();

            // Notes
            $table->text('notes_client')->nullable();
            $table->text('notes_pharmacien')->nullable();
            $table->text('notes_livreur')->nullable();

            // Évaluation
            $table->integer('note_service')->nullable();
            $table->text('commentaire_evaluation')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Index
            $table->index('numero_commande');
            $table->index(['patient_id', 'date_commande']);
            $table->index(['pharmacie_id', 'date_commande']);
            $table->index('ordonnance_id');
            $table->index('statut');
            $table->index('livreur_id');

            // Foreign keys
            $table->foreign('patient_id')->references('id')->on('users')->restrictOnDelete();
            $table->foreign('pharmacie_id')->references('id')->on('structures_medicales')->restrictOnDelete();
            $table->foreign('ordonnance_id')->references('id')->on('ordonnances')->nullOnDelete();
            $table->foreign('livreur_id')->references('id')->on('users')->nullOnDelete();
            $table->foreign('pharmacien_validateur_id')->references('id')->on('users')->nullOnDelete();
        });

        // Table des lignes de commande
        Schema::create('commande_lignes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('commande_id');
            $table->uuid('produit_id');

            // Quantités et prix
            $table->integer('quantite');
            $table->decimal('prix_unitaire', 10, 2);
            $table->decimal('prix_total', 10, 2);
            $table->decimal('taux_remise', 5, 2)->default(0);
            $table->decimal('montant_remise', 10, 2)->default(0);
            $table->decimal('prix_final', 10, 2);

            // Référence ordonnance
            $table->uuid('ordonnance_ligne_id')->nullable();
            $table->boolean('prescription_requise')->default(false);

            // Substitution
            $table->boolean('substitue')->default(false);
            $table->uuid('produit_original_id')->nullable();
            $table->string('raison_substitution')->nullable();

            // Disponibilité
            $table->boolean('disponible')->default(true);
            $table->integer('quantite_disponible')->nullable();
            $table->date('date_disponibilite')->nullable();

            $table->timestamps();

            // Index
            $table->index('commande_id');
            $table->index('produit_id');
            $table->index('ordonnance_ligne_id');

            // Foreign keys
            $table->foreign('commande_id')->references('id')->on('commandes_pharmacie')->cascadeOnDelete();
            $table->foreign('produit_id')->references('id')->on('produits_pharmaceutiques')->restrictOnDelete();
            $table->foreign('ordonnance_ligne_id')->references('id')->on('ordonnance_lignes')->nullOnDelete();
            $table->foreign('produit_original_id')->references('id')->on('produits_pharmaceutiques')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('commande_lignes');
        Schema::dropIfExists('commandes_pharmacie');
        Schema::dropIfExists('stocks_pharmacie');
        Schema::dropIfExists('ordonnance_lignes');
        Schema::dropIfExists('ordonnances');
    }
};
