<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Bon de commande {{ $commande->numero_commande }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ddd; padding: 6px; }
        th { background: #f3f3f3; }
    </style>
</head>
<body>
    <h2>Bon de commande pharmacie</h2>
    <p><strong>Commande :</strong> {{ $commande->numero_commande }}</p>
    <p><strong>Patient :</strong> {{ $commande->patient?->name ?? '-' }}</p>
    <p><strong>Pharmacie :</strong> {{ $commande->pharmacie?->nom ?? '-' }}</p>
    <p><strong>Date :</strong> {{ optional($commande->date_commande ?? $commande->created_at)->format('d/m/Y H:i') }}</p>

    <table>
        <thead>
            <tr>
                <th>Produit</th>
                <th>Quantité</th>
                <th>PU</th>
                <th>Montant</th>
            </tr>
        </thead>
        <tbody>
            @foreach($commande->lignes as $ligne)
                <tr>
                    <td>{{ $ligne->produitPharmaceutique?->nom_commercial ?? '-' }}</td>
                    <td>{{ $ligne->quantite_commandee }}</td>
                    <td>{{ number_format($ligne->prix_unitaire ?? 0, 0, ',', ' ') }} FCFA</td>
                    <td>{{ number_format($ligne->montant_ligne ?? 0, 0, ',', ' ') }} FCFA</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <p style="text-align:right;"><strong>Total :</strong> {{ number_format($commande->montant_total ?? 0, 0, ',', ' ') }} FCFA</p>
</body>
</html>
