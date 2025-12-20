<?php

namespace App\Http\Controllers;

use App\Models\Pharmacie;
use App\Models\StockMedicament;
use App\Models\AlerteStock;
use App\Models\ProduitPharmaceutique;
use App\Http\Requests\PharmacieRequest;
use App\Http\Requests\StockRequest;
use App\Models\MedicalStructure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class PharmacieController extends Controller
{
    /**
     * Afficher la liste des pharmacies
     */
    public function index(Request $request)
    {
        $query = Pharmacie::with(['structureMedicale'])
            ->withCount(['stocks', 'commandes', 'alertes' => function ($q) {
                $q->where('traitee', false);
            }]);

        // Filtres
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nom_pharmacie', 'like', "%{$search}%")
                    ->orWhere('numero_licence', 'like', "%{$search}%")
                    ->orWhere('adresse_complete', 'like', "%{$search}%");
            });
        }

        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        if ($request->filled('service_garde')) {
            $query->where('service_garde', true);
        }

        if ($request->filled('livraison_disponible')) {
            $query->where('livraison_disponible', true);
        }

        // Recherche par proximité
        if ($request->filled('latitude') && $request->filled('longitude')) {
            $latitude = (float) $request->latitude;
            $longitude = (float) $request->longitude;
            $rayon = (float) ($request->rayon ?? 10); // Rayon par défaut de 10 km

            $query->select('*')
                ->selectRaw(
                    '(6371 * acos(cos(radians(?)) * cos(radians(latitude)) *
                    cos(radians(longitude) - radians(?)) + sin(radians(?)) *
                    sin(radians(latitude)))) AS distance',
                    [$latitude, $longitude, $latitude]
                )
                ->having('distance', '<=', $rayon)
                ->orderBy('distance');
        }

        $pharmacies = $query->orderBy('nom_pharmacie')->paginate(15)->withQueryString();

        return view('pharmacie.index', compact('pharmacies'));
    }

    /**
     * Afficher le formulaire de création
     */
    public function create()
    {
        $structures = MedicalStructure::orderBy('nom_structure')->get();
        return view('pharmacie.create', compact('structures'));
    }

    /**
     * Enregistrer une nouvelle pharmacie
     */
    public function store(PharmacieRequest $request)
    {
        DB::beginTransaction();
        try {
            $data = $this->sanitize($request->validated());
            $pharmacie = Pharmacie::create($data);

            // Créer les stocks initiaux si fournis
            if ($request->has('stocks_initiaux')) {
                foreach ($request->stocks_initiaux as $stock) {
                    $pharmacie->stocks()->create($stock);
                }
            }

            DB::commit();

            return redirect()->route('admin.pharmacies.show', $pharmacie)
                ->with('success', 'Pharmacie créée avec succès');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erreur lors de la création: ' . $e->getMessage());
        }
    }

    /**
     * Afficher une pharmacie
     */
    public function show(Pharmacie $pharmacie)
    {
        $pharmacie->load(['structureMedicale', 'stocks.produitPharmaceutique', 'fournisseurs']);

        $statistiques = $pharmacie->getStatistiques();

        // Commandes récentes
        $commandesRecentes = $pharmacie->commandes()
            ->with('patient')
            ->latest()
            ->take(10)
            ->get();

        // Alertes actives
        $alertes = $pharmacie->alertesNonTraitees()
            ->latest()
            ->get();

        return view('pharmacie.show', compact('pharmacie', 'statistiques', 'commandesRecentes', 'alertes'));
    }

    /**
     * Afficher le formulaire d'édition
     */
    public function edit(Pharmacie $pharmacie)
    {
        $structures = MedicalStructure::orderBy('nom_structure')->get();
        return view('pharmacie.edit', compact('pharmacie', 'structures'));
    }

    /**
     * Mettre à jour une pharmacie
     */
    public function update(PharmacieRequest $request, Pharmacie $pharmacie)
    {
        $pharmacie->update($this->sanitize($request->validated()));

        return redirect()->route('admin.pharmacies.show', $pharmacie)
            ->with('success', 'Pharmacie mise à jour avec succès');
    }

    /**
     * Supprimer une pharmacie
     */
    public function destroy(Pharmacie $pharmacie)
    {
        try {
            $pharmacie->delete();
            return redirect()->route('admin.pharmacies.index')
                ->with('success', 'Pharmacie supprimée avec succès');
        } catch (\Exception $e) {
            return back()->with('error', 'Impossible de supprimer cette pharmacie');
        }
    }

    /**
     * Gestion des stocks
     */
    public function stocks(Pharmacie $pharmacie, Request $request)
    {
        $query = $pharmacie->stocks()
            ->with('produitPharmaceutique');

        // Filtres
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('produitPharmaceutique', function ($q) use ($search) {
                $q->where('nom_commercial', 'like', "%{$search}%")
                    ->orWhere('dci', 'like', "%{$search}%");
            });
        }

        if ($request->filled('statut_stock')) {
            $query->where('statut_stock', $request->statut_stock);
        }

        if ($request->has('prescription_requise')) {
            $query->where('prescription_requise', $request->prescription_requise);
        }

        if ($request->has('stock_faible')) {
            $query->stockFaible();
        }

        if ($request->has('expiration_proche')) {
            $query->expirationProche();
        }

        $stocks = $query->orderBy('statut_stock')
            ->orderBy('quantite_disponible')
            ->paginate(20);

        return view('pharmacie.stocks', compact('pharmacie', 'stocks'));
    }

    /**
     * Ajouter un produit au stock
     */
    public function ajouterStock(Request $request, Pharmacie $pharmacie)
    {
        $request->validate([
            'produit_pharmaceutique_id' => 'required|exists:produits_pharmaceutiques,id',
            'quantite' => 'required|integer|min:1',
            'prix_vente' => 'required|numeric|min:0',
            'prix_achat' => 'nullable|numeric|min:0',
            'numero_lot' => 'nullable|string',
            'date_expiration' => 'nullable|date|after:today',
            'emplacement_rayon' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            // Vérifier si le produit existe déjà dans le stock
            $stock = $pharmacie->stocks()
                ->where('produit_pharmaceutique_id', $request->produit_pharmaceutique_id)
                ->where('numero_lot', $request->numero_lot)
                ->first();

            if ($stock) {
                // Ajouter à l'existant
                $stock->ajouterStock($request->quantite, 'Réapprovisionnement');
                $stock->update($request->only(['prix_vente', 'prix_achat', 'date_expiration']));
            } else {
                // Créer nouveau stock
                $stock = $pharmacie->stocks()->create($request->all());
            }

            DB::commit();

            return back()->with('success', 'Stock ajouté avec succès');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erreur lors de l\'ajout du stock: ' . $e->getMessage());
        }
    }

    /**
     * Ajuster le stock d'un produit
     */
    public function ajusterStock(Request $request, Pharmacie $pharmacie, StockMedicament $stock)
    {
        $request->validate([
            'nouvelle_quantite' => 'required|integer|min:0',
            'motif' => 'required|string',
        ]);

        try {
            $stock->ajusterStock($request->nouvelle_quantite, $request->motif);
            return back()->with('success', 'Stock ajusté avec succès');
        } catch (\Exception $e) {
            return back()->with('error', 'Erreur lors de l\'ajustement: ' . $e->getMessage());
        }
    }

    /**
     * Mouvements de stock
     */
    public function mouvementsStock(Pharmacie $pharmacie, Request $request)
    {
        $query = DB::table('mouvements_stock')
            ->join('stocks_medicaments', 'mouvements_stock.stock_medicament_id', '=', 'stocks_medicaments.id')
            ->join('produits_pharmaceutiques', 'stocks_medicaments.produit_pharmaceutique_id', '=', 'produits_pharmaceutiques.id')
            ->leftJoin('users', 'mouvements_stock.utilisateur_id', '=', 'users.id')
            ->where('stocks_medicaments.pharmacie_id', $pharmacie->id)
            ->select(
                'mouvements_stock.*',
                'produits_pharmaceutiques.nom_commercial',
                'produits_pharmaceutiques.dci',
                'users.name as utilisateur_nom'
            );

        // Filtres
        if ($request->filled('type_mouvement')) {
            $query->where('mouvements_stock.type_mouvement', $request->type_mouvement);
        }

        if ($request->filled('date_debut')) {
            $query->whereDate('mouvements_stock.created_at', '>=', $request->date_debut);
        }

        if ($request->filled('date_fin')) {
            $query->whereDate('mouvements_stock.created_at', '<=', $request->date_fin);
        }

        $mouvements = $query->orderBy('mouvements_stock.created_at', 'desc')
            ->paginate(30);

        return view('pharmacie.mouvements-stock', compact('pharmacie', 'mouvements'));
    }

    /**
     * Gérer les alertes
     */
    public function alertes(Pharmacie $pharmacie)
    {
        $alertes = $pharmacie->alertes()
            ->with('stockMedicament.produitPharmaceutique')
            ->orderBy('traitee')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('pharmacie.alertes', compact('pharmacie', 'alertes'));
    }

    /**
     * Traiter une alerte
     */
    public function traiterAlerte(Request $request, Pharmacie $pharmacie, AlerteStock $alerte)
    {
        $request->validate([
            'action_prise' => 'required|string',
        ]);

        $alerte->update([
            'traitee' => true,
            'date_traitement' => now(),
            'traite_par' => auth()->id(),
            'action_prise' => $request->action_prise,
        ]);

        return back()->with('success', 'Alerte traitée avec succès');
    }

    /**
     * Vérifier les stocks et créer les alertes
     */
    public function verifierStocks(Pharmacie $pharmacie)
    {
        $stocksProblematiques = 0;

        foreach ($pharmacie->stocks as $stock) {
            $alertes = $stock->necessiteAlerte();
            if (count($alertes) > 0) {
                $stock->creerAlertes();
                $stocksProblematiques++;
            }
        }

        return back()->with('info', "{$stocksProblematiques} stocks nécessitent une attention");
    }

    /**
     * Rapport d'inventaire
     */
    public function inventaire(Pharmacie $pharmacie)
    {
        $stocks = $pharmacie->stocks()
            ->with('produitPharmaceutique')
            ->get();

        $valeurTotale = $stocks->sum(function ($stock) {
            return $stock->getValeurStock();
        });

        $stocksParCategorie = $stocks->groupBy('produitPharmaceutique.categorie');

        $produitsExpires = $stocks->filter(function ($stock) {
            return $stock->date_expiration && $stock->date_expiration < now();
        });

        $produitsExpirationProche = $stocks->filter(function ($stock) {
            return $stock->date_expiration &&
                   $stock->date_expiration > now() &&
                   $stock->date_expiration <= now()->addDays(30);
        });

        return view('pharmacie.inventaire', compact(
            'pharmacie',
            'stocks',
            'valeurTotale',
            'stocksParCategorie',
            'produitsExpires',
            'produitsExpirationProche'
        ));
    }

    /**
     * Export inventaire Excel
     */
    public function exportInventaire(Pharmacie $pharmacie)
    {
        // Implémenter l'export Excel avec Laravel Excel
        // return Excel::download(new InventaireExport($pharmacie), 'inventaire.xlsx');
    }

    /**
     * Recherche de médicaments disponibles
     */
    public function rechercherMedicament(Request $request)
    {
        $request->validate([
            'terme' => 'required|string|min:3',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);

        $query = StockMedicament::with(['pharmacie', 'produitPharmaceutique'])
            ->disponible()
            ->whereHas('produitPharmaceutique', function ($q) use ($request) {
                $q->where('nom_commercial', 'like', "%{$request->terme}%")
                    ->orWhere('dci', 'like', "%{$request->terme}%");
            })
            ->whereHas('pharmacie', function ($q) {
                $q->active();
            });

        // Si coordonnées fournies, trier par distance
        if ($request->filled('latitude') && $request->filled('longitude')) {
            $latitude = $request->latitude;
            $longitude = $request->longitude;

            $query->join('pharmacies', 'stocks_medicaments.pharmacie_id', '=', 'pharmacies.id')
                ->selectRaw("stocks_medicaments.*,
                    (6371 * acos(cos(radians(?)) * cos(radians(pharmacies.latitude)) *
                    cos(radians(pharmacies.longitude) - radians(?)) + sin(radians(?)) *
                    sin(radians(pharmacies.latitude)))) AS distance",
                    [$latitude, $longitude, $latitude])
                ->orderBy('distance');
        }

        $resultats = $query->take(20)->get();

        if ($request->wantsJson()) {
            return response()->json($resultats);
        }

        return view('pharmacie.recherche-medicament', compact('resultats'));
    }

    /**
     * Dashboard pharmacie
     */
    public function dashboard(Pharmacie $pharmacie)
    {
        $statistiques = Cache::remember("pharmacie_{$pharmacie->id}_stats", 300, function () use ($pharmacie) {
            return [
                'commandes_jour' => $pharmacie->commandes()->whereDate('created_at', today())->count(),
                'commandes_semaine' => $pharmacie->commandes()->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
                'ca_jour' => $pharmacie->commandes()->whereDate('created_at', today())->where('statut', 'livree')->sum('montant_total'),
                'ca_semaine' => $pharmacie->commandes()->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->where('statut', 'livree')->sum('montant_total'),
                'stocks_faibles' => $pharmacie->stocks()->stockFaible()->count(),
                'stocks_expires' => $pharmacie->stocks()->expire()->count(),
                'alertes_actives' => $pharmacie->alertesNonTraitees()->count(),
                'taux_satisfaction' => 95, // À implémenter avec système de notation
            ];
        });

        // Graphiques
        $ventesParJour = $pharmacie->commandes()
            ->where('statut', 'livree')
            ->whereBetween('created_at', [now()->subDays(7), now()])
            ->selectRaw('DATE(created_at) as date, COUNT(*) as nombre, SUM(montant_total) as montant')
            ->groupBy('date')
            ->get();

        $topProduits = DB::table('lignes_commande_pharma')
            ->join('commandes_pharmaceutiques', 'lignes_commande_pharma.commande_pharmaceutique_id', '=', 'commandes_pharmaceutiques.id')
            ->join('produits_pharmaceutiques', 'lignes_commande_pharma.produit_pharmaceutique_id', '=', 'produits_pharmaceutiques.id')
            ->where('commandes_pharmaceutiques.pharmacie_id', $pharmacie->id)
            ->where('commandes_pharmaceutiques.statut', 'livree')
            ->whereBetween('commandes_pharmaceutiques.created_at', [now()->subDays(30), now()])
            ->selectRaw('produits_pharmaceutiques.nom_commercial, SUM(lignes_commande_pharma.quantite_commandee) as quantite_vendue')
            ->groupBy('produits_pharmaceutiques.id', 'produits_pharmaceutiques.nom_commercial')
            ->orderBy('quantite_vendue', 'desc')
            ->take(10)
            ->get();

        return view('pharmacie.dashboard', compact('pharmacie', 'statistiques', 'ventesParJour', 'topProduits'));
    }

    private function sanitize(array $data): array
    {
        $data['service_garde'] = (bool) ($data['service_garde'] ?? false);
        $data['livraison_disponible'] = (bool) ($data['livraison_disponible'] ?? false);
        $data['paiement_mobile_money'] = (bool) ($data['paiement_mobile_money'] ?? false);
        $data['paiement_carte'] = (bool) ($data['paiement_carte'] ?? false);
        $data['paiement_especes'] = (bool) ($data['paiement_especes'] ?? false);

        return $data;
    }
}
