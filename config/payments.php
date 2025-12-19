<?php

return [
    // Commission plateforme appliquée au montant total payé
    'commission_pct' => env('PAYMENTS_COMMISSION_PCT', 0.05),

    // Part du livreur sur les frais de livraison (le reste reste cantonné ou revient à la plateforme)
    'livreur_pct_delivery' => env('PAYMENTS_LIVREUR_PCT_DELIVERY', 0.8),
];
