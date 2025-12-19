<?php

namespace Tests\Feature\Pharmacy;

use App\Models\CommandePharmaceutique;
use App\Models\DevisPharmaceutique;
use App\Models\FacturePharmaceutique;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class CommandePaiementFactureTest extends TestCase
{
    use RefreshDatabase;

    public function test_commande_paiement_facture_commission(): void
    {
        $commande = CommandePharmaceutique::factory()->create([
            'montant_total' => 5000,
            'commission_taux' => 10,
            'commission_montant' => 500,
            'montant_net_pharmacie' => 4500,
            'statut_commission' => 'en_attente',
        ]);

        $devis = DevisPharmaceutique::create([
            'id' => (string) Str::uuid(),
            'commande_pharmaceutique_id' => $commande->id,
            'pharmacie_id' => $commande->pharmacie_id,
            'cree_par' => $commande->pharmacie->responsable_id ?? null,
            'montant_medicaments' => 5000,
            'frais_livraison' => 0,
            'commission_montant' => 500,
            'total_general' => 5000,
            'statut' => 'accepte',
        ]);

        $facture = FacturePharmaceutique::create([
            'id' => (string) Str::uuid(),
            'commande_pharmaceutique_id' => $commande->id,
            'numero_facture' => 'FAC-' . now()->format('Ymd') . '-' . rand(1000, 9999),
            'montant_medicaments' => 5000,
            'commission_montant' => 500,
            'total_ttc' => 5000,
            'mode_paiement' => 'mobile_money',
            'statut' => 'generee',
            'emise_le' => now(),
        ]);

        $this->assertEquals(500, $facture->commission_montant);
        $this->assertEquals('accepte', $devis->statut);
        $this->assertEquals(4500, $commande->montant_net_pharmacie);
    }
}

