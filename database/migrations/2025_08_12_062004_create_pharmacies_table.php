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
        // Table des pharmacies
        Schema::create('pharmacies', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('structure_medicale_id')->constrained('structures_medicales')->onDelete('cascade');
            $table->string('numero_licence')->unique();
            $table->string('nom_pharmacie');
            $table->string('nom_responsable');
            $table->string('telephone_pharmacie');
            $table->string('email_pharmacie')->nullable();
            $table->text('adresse_complete');
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->json('horaires_ouverture')->nullable();
            $table->boolean('service_garde')->default(false);
            $table->boolean('livraison_disponible')->default(false);
            $table->decimal('rayon_livraison_km', 5, 2)->nullable();
            $table->decimal('frais_livraison_base', 10, 2)->default(0);
            $table->decimal('frais_livraison_par_km', 10, 2)->default(0);
            $table->boolean('paiement_mobile_money')->default(true);
            $table->boolean('paiement_carte')->default(false);
            $table->boolean('paiement_especes')->default(true);
            $table->enum('statut', ['active', 'inactive', 'suspendue'])->default('active');
            $table->timestamps();
            $table->softDeletes();

            // Index pour la recherche géographique
            $table->index(['latitude', 'longitude']);
            $table->index('statut');
            $table->index('service_garde');
        });

        // Table des stocks de médicaments
        Schema::create('stocks_medicaments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('pharmacie_id')->constrained()->onDelete('cascade');
            $table->foreignUuid('produit_pharmaceutique_id')->constrained('produits_pharmaceutiques');
            $table->integer('quantite_disponible')->default(0);
            $table->integer('quantite_minimum')->default(10);
            $table->integer('quantite_maximum')->default(1000);
            $table->decimal('prix_vente', 10, 2);
            $table->decimal('prix_achat', 10, 2)->nullable();
            $table->string('numero_lot')->nullable();
            $table->date('date_expiration')->nullable();
            $table->string('emplacement_rayon')->nullable();
            $table->boolean('prescription_requise')->default(false);
            $table->boolean('disponible_vente')->default(true);
            $table->enum('statut_stock', ['disponible', 'faible', 'rupture', 'expire'])->default('disponible');
            $table->timestamps();

            // Nom explicite court pour MySQL (64 chars max)
            $table->unique(
                ['pharmacie_id', 'produit_pharmaceutique_id', 'numero_lot'],
                'stock_meds_pharm_prod_lot_unique'
            );
            $table->index('statut_stock');
            $table->index('date_expiration');
            $table->index('quantite_disponible');
        });

        // Table des mouvements de stock
        Schema::create('mouvements_stock', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('stock_medicament_id')->constrained('stocks_medicaments');
            $table->foreignUuid('utilisateur_id')->constrained('users');
            $table->enum('type_mouvement', ['entree', 'sortie', 'ajustement', 'perime', 'retour']);
            $table->integer('quantite');
            $table->integer('stock_avant');
            $table->integer('stock_apres');
            $table->string('reference_document')->nullable(); // Référence facture, bon de livraison, etc.
            $table->text('motif')->nullable();
            $table->decimal('prix_unitaire', 10, 2)->nullable();
            $table->timestamps();

            $table->index('type_mouvement');
            $table->index('created_at');
        });

        // Table des commandes pharmaceutiques
        Schema::create('commandes_pharmaceutiques', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('numero_commande')->unique();
            $table->foreignUuid('patient_id')->constrained('users');
            $table->foreignUuid('pharmacie_id')->constrained();
            $table->foreignUuid('ordonnance_id')->nullable()->constrained('ordonnances');
            $table->decimal('montant_total', 10, 2);
            $table->decimal('montant_assurance', 10, 2)->default(0);
            $table->decimal('montant_patient', 10, 2);
            $table->enum('mode_retrait', ['sur_place', 'livraison']);
            $table->text('adresse_livraison')->nullable();
            $table->decimal('latitude_livraison', 10, 8)->nullable();
            $table->decimal('longitude_livraison', 11, 8)->nullable();
            $table->decimal('frais_livraison', 10, 2)->default(0);
            $table->enum('statut', [
                'en_attente',
                'confirmee',
                'en_preparation',
                'prete',
                'en_livraison',
                'livree',
                'annulee',
                'remboursee'
            ])->default('en_attente');
            $table->datetime('date_commande');
            $table->datetime('date_preparation')->nullable();
            $table->datetime('date_retrait_prevue')->nullable();
            $table->datetime('date_livraison_prevue')->nullable();
            $table->datetime('date_livraison_effective')->nullable();
            $table->string('code_retrait')->nullable();
            $table->text('instructions_speciales')->nullable();
            $table->boolean('urgent')->default(false);
            $table->timestamps();
            $table->softDeletes();

            $table->index('statut');
            $table->index('numero_commande');
            $table->index('date_commande');
        });

        // Table des lignes de commande
        Schema::create('lignes_commande_pharma', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('commande_pharmaceutique_id')->constrained('commandes_pharmaceutiques')->onDelete('cascade');
            $table->foreignUuid('produit_pharmaceutique_id')->constrained('produits_pharmaceutiques');
            $table->foreignUuid('stock_medicament_id')->nullable()->constrained('stocks_medicaments');
            $table->integer('quantite_commandee');
            $table->integer('quantite_livree')->default(0);
            $table->decimal('prix_unitaire', 10, 2);
            $table->decimal('montant_ligne', 10, 2);
            $table->decimal('taux_remboursement', 5, 2)->default(0);
            $table->decimal('montant_remboursement', 10, 2)->default(0);
            $table->string('posologie')->nullable();
            $table->integer('duree_traitement_jours')->nullable();
            $table->boolean('substitution_acceptee')->default(false);
            $table->foreignUuid('produit_substitue_id')->nullable()->constrained('produits_pharmaceutiques');
            $table->text('motif_substitution')->nullable();
            $table->timestamps();

            $table->index('commande_pharmaceutique_id');
        });

        // Table des livraisons
        Schema::create('livraisons_pharmaceutiques', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('commande_pharmaceutique_id')->constrained('commandes_pharmaceutiques');
            $table->foreignUuid('livreur_id')->nullable()->constrained('users');
            $table->string('numero_livraison')->unique();
            $table->enum('statut', [
                'planifiee',
                'en_cours',
                'livree',
                'echec',
                'retour'
            ])->default('planifiee');
            $table->datetime('date_depart')->nullable();
            $table->datetime('date_arrivee_prevue')->nullable();
            $table->datetime('date_livraison')->nullable();
            $table->string('nom_receptionnaire')->nullable();
            $table->string('telephone_receptionnaire')->nullable();
            $table->string('signature_receptionnaire')->nullable(); // Base64 de la signature
            $table->string('photo_livraison')->nullable(); // Preuve de livraison
            $table->text('commentaire_livreur')->nullable();
            $table->text('motif_echec')->nullable();
            $table->json('tracking_gps')->nullable(); // Historique des positions GPS
            $table->decimal('distance_parcourue_km', 5, 2)->nullable();
            $table->timestamps();

            $table->index('statut');
            $table->index('numero_livraison');
        });

        // Table des alertes de stock
        Schema::create('alertes_stock', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('pharmacie_id')->constrained();
            $table->foreignUuid('stock_medicament_id')->constrained('stocks_medicaments');
            $table->enum('type_alerte', [
                'stock_faible',
                'rupture_stock',
                'expiration_proche',
                'expire'
            ]);
            $table->text('message');
            $table->boolean('vue')->default(false);
            $table->boolean('traitee')->default(false);
            $table->datetime('date_traitement')->nullable();
            $table->foreignUuid('traite_par')->nullable()->constrained('users');
            $table->text('action_prise')->nullable();
            $table->timestamps();

            $table->index(['pharmacie_id', 'vue']);
            $table->index('type_alerte');
        });

        // Table des fournisseurs
        Schema::create('fournisseurs_pharmaceutiques', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('nom_fournisseur');
            $table->string('numero_licence')->unique();
            $table->string('telephone');
            $table->string('email')->nullable();
            $table->text('adresse');
            $table->string('personne_contact')->nullable();
            $table->string('telephone_contact')->nullable();
            $table->json('categories_produits')->nullable();
            $table->integer('delai_livraison_jours')->default(1);
            $table->decimal('montant_minimum_commande', 10, 2)->default(0);
            $table->enum('statut', ['actif', 'inactif'])->default('actif');
            $table->timestamps();
            $table->softDeletes();
        });

        // Table de liaison pharmacie-fournisseur
        Schema::create('pharmacie_fournisseur', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('pharmacie_id')->constrained();
            $table->foreignUuid('fournisseur_id')->constrained('fournisseurs_pharmaceutiques');
            $table->string('numero_compte_client')->nullable();
            $table->enum('statut', ['actif', 'suspendu'])->default('actif');
            $table->decimal('credit_maximum', 10, 2)->default(0);
            $table->decimal('credit_utilise', 10, 2)->default(0);
            $table->timestamps();

            $table->unique(['pharmacie_id', 'fournisseur_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pharmacie_fournisseur');
        Schema::dropIfExists('fournisseurs_pharmaceutiques');
        Schema::dropIfExists('alertes_stock');
        Schema::dropIfExists('livraisons_pharmaceutiques');
        Schema::dropIfExists('lignes_commande_pharma');
        Schema::dropIfExists('commandes_pharmaceutiques');
        Schema::dropIfExists('mouvements_stock');
        Schema::dropIfExists('stocks_medicaments');
        Schema::dropIfExists('pharmacies');
    }
};
