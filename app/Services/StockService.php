<?php

namespace App\Services;

use App\Models\ProduitPharmaceutique;
use Illuminate\Support\Facades\Log;

class StockService
{
    /**
     * Met à jour le stock d'un produit pharmaceutique.
     *
     * @param int $produitId L'ID du produit.
     * @param int $quantity La quantité à ajuster (positive pour ajouter, négative pour retirer).
     * @return bool
     */
    public function adjustStock(int $produitId, int $quantity): bool
    {
        try {
            $produit = ProduitPharmaceutique::find($produitId);

            if (!$produit) {
                Log::warning("Tentative d'ajustement de stock pour un produit inexistant: ID {$produitId}");
                return false;
            }

            $produit->stock_disponible += $quantity;
            $produit->save();

            Log::info("Stock du produit {$produitId} ajusté de {$quantity}. Nouveau stock: {$produit->stock_disponible}");

            return true;
        } catch (\Exception $e) {
            Log::error("Erreur lors de l'ajustement du stock du produit {$produitId}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Vérifie si une quantité de produit est disponible en stock.
     *
     * @param int $produitId L'ID du produit.
     * @param int $quantity La quantité requise.
     * @return bool
     */
    public function checkStockAvailability(int $produitId, int $quantity): bool
    {
        $produit = ProduitPharmaceutique::find($produitId);

        if (!$produit) {
            return false;
        }

        return $produit->stock_disponible >= $quantity;
    }
}
