<?php

namespace App\Http\Controllers;

use App\Models\CommandePharmaceutique;
use App\Models\Pharmacie;
use App\Models\Ordonnance;
use App\Models\ProduitPharmaceutique;
use App\Models\StockMedicament;
use App\Http\Requests\CommandePharmaceutiqueRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Barryvdh\DomPDF\Facade\Pdf;

class CommandePharmaceutiqueController extends Controller
{
    /**
     * Liste des commandes
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = CommandePharmaceutique::with(['patient', 'pharmacie', 'ordonnance']);

        // Filtrer selon le rôle
        if ($user->hasRole('patient')) {
            $query->where('patient_id', $user->id);
        } elseif ($user->hasRole('pharmacien')) {
            $pharmacie = Pharmacie::where('structure_medicale_id', $user->structure_medicale_id)->first();
            if ($pharmacie) {
                $query->where('pharmacie_id', $pharmacie->id);
            }
        }

        // Filtres
        if ($request->filled('numero_commande')) {
            $query->where('numero_commande', 'like', "%{$request->numero_commande}%");
        }

        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        if ($request->filled('mode_retrait')) {
            $query->where('mode_retrait', $request->mode_retrait);
        }

        if ($request->filled('date_debut')) {
            $query->whereDate('date_commande', '>=', $request->date_debut);
        }

        if ($request->filled('date_fin')) {
            $query->whereDate('date_commande', '<=', $request->date_fin);
        }

        if ($request->has('urgent')) {
            $query->where('urgent', true);
        }

        $commandes = $query->orderBy('urgent', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('commandes-pharma.index', compact('commandes'));
    }

    /**
     * Créer une nouvelle commande
     */
    public function create(Request $request)
    {
        $ordonnance = null;
        $pharmacies = Pharmacie::active()->get();
        $produits = [];

        // Si une ordonnance est fournie
        if ($request->has('ordonnance_id')) {
            $ordonnance = Ordonnance::with('lignes.produitPharmaceutique')->findOrFail($request->ordonnance_id);
            $produits = $ordonnance->lignes->map(function ($ligne) {
                return [
                    'produit' => $ligne->produitPharmaceutique,
                    'quantite' => $ligne->quantite,
                    'posologie' => $ligne->posologie,
                    'duree_traitement' => $ligne->duree_traitement_jours,
                ];
            });
        }

        return view('commandes-pharma.create', compact('pharmacies', 'ordonnance', 'produits'));
    }

    /**
     * Enregistrer une nouvelle commande
     */
    public function store(CommandePharmaceutiqueRequest $request)
    {
        DB::beginTransaction();
        try {
            // Créer la commande
            $data = $request->validated();
            $data['patient_id'] = Auth::id();
            $data['date_commande'] = now();
            $data['statut'] = 'en_attente';

            // Calculer les frais de livraison si nécessaire
            if ($data['mode_retrait'] === 'livraison') {
                $pharmacie = Pharmacie::find($data['pharmacie_id']);
                $fraisLivraison = $pharmacie->calculerFraisLivraison(
                    $data['latitude_livraison'],
                    $data['longitude_livraison']
                );

                if ($fraisLivraison === null) {
                    throw new \Exception('Adresse de livraison hors zone');
                }

                $data['frais_livraison'] = $fraisLivraison;
            }

            $commande = CommandePharmaceutique::create($data);

            // Ajouter les lignes de commande
            $montantTotal = 0;
            $montantAssurance = 0;

            foreach ($request->produits as $produitData) {
                $stock = StockMedicament::where('pharmacie_id', $data['pharmacie_id'])
                    ->where('produit_pharmaceutique_id', $produitData['produit_id'])
                    ->disponible()
                    ->first();

                if (!$stock) {
                    throw new \Exception("Produit non disponible: {$produitData['produit_id']}");
                }

                if ($stock->quantite_disponible < $produitData['quantite']) {
                    throw new \Exception("Stock insuffisant pour: {$stock->produitPharmaceutique->nom_commercial}");
                }

                $montantLigne = $stock->prix_vente * $produitData['quantite'];
                $tauxRemboursement = $produitData['taux_remboursement'] ?? 0;
                $montantRemboursement = ($montantLigne * $tauxRemboursement) / 100;

                $commande->lignes()->create([
                    'produit_pharmaceutique_id' => $produitData['produit_id'],
                    'stock_medicament_id' => $stock->id,
                    'quantite_commandee' => $produitData['quantite'],
                    'prix_unitaire' => $stock->prix_vente,
                    'montant_ligne' => $montantLigne,
                    'taux_remboursement' => $tauxRemboursement,
                    'montant_remboursement' => $montantRemboursement,
                    'posologie' => $produitData['posologie'] ?? null,
                    'duree_traitement_jours' => $produitData['duree_traitement'] ?? null,
                ]);

                $montantTotal += $montantLigne;
                $montantAssurance += $montantRemboursement;
            }

            // Ajouter les frais de livraison au total
            if ($commande->mode_retrait === 'livraison') {
                $montantTotal += $commande->frais_livraison;
            }

            // Mettre à jour les montants
            $commande->update([
                'montant_total' => $montantTotal,
                'montant_assurance' => $montantAssurance,
                'montant_patient' => $montantTotal - $montantAssurance,
            ]);

            DB::commit();

            return redirect()->route('commandes-pharma.show', $commande)
                ->with('success', 'Commande créée avec succès. Code de retrait: ' . $commande->code_retrait);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erreur lors de la création: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Afficher une commande
     */
    public function show(CommandePharmaceutique $commande)
    {
        $this->authorize('view', $commande);

        $commande->load([
            'patient',
            'pharmacie',
            'ordonnance.consultation.medecin',
            'lignes.produitPharmaceutique',
            'livraison',
            'paiements'
        ]);

        // Générer le QR Code
        $qrCode = base64_encode(QrCode::format('png')
            ->size(200)
            ->generate(route('commandes-pharma.valider-code', $commande->code_retrait)));

        return view('commandes-pharma.show', compact('commande', 'qrCode'));
    }

    /**
     * Confirmer une commande
     */
    public function confirmer(CommandePharmaceutique $commande)
    {
        $this->authorize('update', $commande);

        try {
            $commande->confirmer();
            return back()->with('success', 'Commande confirmée avec succès');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Préparer une commande
     */
    public function preparer(CommandePharmaceutique $commande)
    {
        $this->authorize('update', $commande);

        if ($commande->statut !== 'confirmee') {
            return back()->with('error', 'La commande doit être confirmée avant la préparation');
        }

        $commande->update([
            'statut' => 'en_preparation',
            'date_preparation' => now(),
        ]);

        return back()->with('success', 'Préparation de la commande démarrée');
    }

    /**
     * Marquer une commande comme prête
     */
    public function marquerPrete(CommandePharmaceutique $commande)
    {
        $this->authorize('update', $commande);

        try {
            $commande->marquerPrete();
            return back()->with('success', 'Commande marquée comme prête');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Démarrer la livraison
     */
    public function demarrerLivraison(Request $request, CommandePharmaceutique $commande)
    {
        $this->authorize('update', $commande);

        $request->validate([
            'livreur_id' => 'required|exists:users,id',
        ]);

        if ($commande->statut !== 'prete' || $commande->mode_retrait !== 'livraison') {
            return back()->with('error', 'Cette commande ne peut pas être livrée');
        }

        $commande->update(['statut' => 'en_livraison']);

        if ($commande->livraison) {
            $commande->livraison->update([
                'livreur_id' => $request->livreur_id,
                'statut' => 'en_cours',
                'date_depart' => now(),
            ]);
        }

        return back()->with('success', 'Livraison démarrée');
    }

    /**
     * Confirmer la livraison
     */
    public function confirmerLivraison(Request $request, CommandePharmaceutique $commande)
    {
        $this->authorize('update', $commande);

        $request->validate([
            'signature' => 'nullable|string',
            'photo' => 'nullable|image|max:5000',
            'nom_receptionnaire' => 'required|string',
            'telephone_receptionnaire' => 'required|string',
        ]);

        try {
            $photoPath = null;
            if ($request->hasFile('photo')) {
                $photoPath = $request->file('photo')->store('livraisons', 'public');
            }

            $commande->marquerLivree($request->signature, $photoPath);

            if ($commande->livraison) {
                $commande->livraison->update([
                    'nom_receptionnaire' => $request->nom_receptionnaire,
                    'telephone_receptionnaire' => $request->telephone_receptionnaire,
                ]);
            }

            return redirect()->route('commandes-pharma.show', $commande)
                ->with('success', 'Livraison confirmée avec succès');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Annuler une commande
     */
    public function annuler(Request $request, CommandePharmaceutique $commande)
    {
        $this->authorize('update', $commande);

        $request->validate([
            'motif' => 'required|string|min:10',
        ]);

        try {
            $commande->annuler($request->motif);
            return back()->with('success', 'Commande annulée avec succès');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Valider le code de retrait
     */
    public function validerCode($code)
    {
        $commande = CommandePharmaceutique::where('code_retrait', $code)
            ->whereIn('statut', ['prete'])
            ->first();

        if (!$commande) {
            return response()->json(['valid' => false, 'message' => 'Code invalide ou commande non prête']);
        }

        return response()->json([
            'valid' => true,
            'commande' => [
                'numero' => $commande->numero_commande,
                'patient' => $commande->patient->name,
                'montant' => $commande->montant_total,
                'nombre_produits' => $commande->lignes->count(),
            ]
        ]);
    }

    /**
     * Télécharger le bon de commande
     */
    public function telechargerBon(CommandePharmaceutique $commande)
    {
        $this->authorize('view', $commande);

        $commande->load([
            'patient',
            'pharmacie',
            'lignes.produitPharmaceutique'
        ]);

        $pdf = Pdf::loadView('commandes-pharma.bon', compact('commande'));

        return $pdf->download("bon-commande-{$commande->numero_commande}.pdf");
    }

    /**
     * Rechercher des produits disponibles
     */
    public function rechercherProduits(Request $request)
    {
        $request->validate([
            'pharmacie_id' => 'required|exists:pharmacies,id',
            'terme' => 'required|string|min:2',
        ]);

        $stocks = StockMedicament::where('pharmacie_id', $request->pharmacie_id)
            ->disponible()
            ->whereHas('produitPharmaceutique', function ($q) use ($request) {
                $q->where('nom_commercial', 'like', "%{$request->terme}%")
                    ->orWhere('dci', 'like', "%{$request->terme}%");
            })
            ->with('produitPharmaceutique')
            ->take(10)
            ->get();

        return response()->json($stocks->map(function ($stock) {
            return [
                'id' => $stock->produit_pharmaceutique_id,
                'nom' => $stock->produitPharmaceutique->nom_commercial,
                'dci' => $stock->produitPharmaceutique->dci,
                'prix' => $stock->prix_vente,
                'stock' => $stock->quantite_disponible,
                'prescription_requise' => $stock->prescription_requise,
            ];
        }));
    }

    /**
     * Suivi de commande
     */
    public function suivre($numeroCommande)
    {
        $commande = CommandePharmaceutique::where('numero_commande', $numeroCommande)
            ->with(['pharmacie', 'livraison'])
            ->firstOrFail();

        $historique = [
            [
                'statut' => 'en_attente',
                'label' => 'Commande reçue',
                'date' => $commande->date_commande,
                'complete' => true,
            ],
            [
                'statut' => 'confirmee',
                'label' => 'Commande confirmée',
                'date' => $commande->date_preparation,
                'complete' => in_array($commande->statut, ['confirmee', 'en_preparation', 'prete', 'en_livraison', 'livree']),
            ],
            [
                'statut' => 'en_preparation',
                'label' => 'En préparation',
                'date' => $commande->date_preparation,
                'complete' => in_array($commande->statut, ['en_preparation', 'prete', 'en_livraison', 'livree']),
            ],
            [
                'statut' => 'prete',
                'label' => 'Prête',
                'date' => null,
                'complete' => in_array($commande->statut, ['prete', 'en_livraison', 'livree']),
            ],
        ];

        if ($commande->mode_retrait === 'livraison') {
            $historique[] = [
                'statut' => 'en_livraison',
                'label' => 'En livraison',
                'date' => $commande->livraison?->date_depart,
                'complete' => in_array($commande->statut, ['en_livraison', 'livree']),
            ];
        }

        $historique[] = [
            'statut' => 'livree',
            'label' => $commande->mode_retrait === 'livraison' ? 'Livrée' : 'Retirée',
            'date' => $commande->date_livraison_effective,
            'complete' => $commande->statut === 'livree',
        ];

        return view('commandes-pharma.suivi', compact('commande', 'historique'));
    }

    /**
     * Tableau de bord des commandes pour pharmacien
     */
    public function dashboard()
    {
        $user = Auth::user();

        if (!$user->hasRole('pharmacien')) {
            abort(403);
        }

        $pharmacie = Pharmacie::where('structure_medicale_id', $user->structure_medicale_id)->first();

        if (!$pharmacie) {
            return redirect()->route('home')->with('error', 'Aucune pharmacie associée');
        }

        $statistiques = [
            'commandes_jour' => $pharmacie->commandes()->whereDate('created_at', today())->count(),
            'en_attente' => $pharmacie->commandes()->where('statut', 'en_attente')->count(),
            'en_preparation' => $pharmacie->commandes()->where('statut', 'en_preparation')->count(),
            'pretes' => $pharmacie->commandes()->where('statut', 'prete')->count(),
            'en_livraison' => $pharmacie->commandes()->where('statut', 'en_livraison')->count(),
            'urgentes' => $pharmacie->commandes()->where('urgent', true)->whereIn('statut', ['en_attente', 'confirmee'])->count(),
        ];

        $commandesRecentes = $pharmacie->commandes()
            ->with(['patient', 'lignes'])
            ->whereIn('statut', ['en_attente', 'confirmee', 'en_preparation'])
            ->orderBy('urgent', 'desc')
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        $livraisonsEnCours = $pharmacie->commandes()
            ->with(['livraison.livreur', 'patient'])
            ->where('statut', 'en_livraison')
            ->get();

        return view('commandes-pharma.dashboard', compact('pharmacie', 'statistiques', 'commandesRecentes', 'livraisonsEnCours'));
    }
}
