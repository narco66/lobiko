<?php

namespace App\Http\Controllers;

use App\Http\Requests\PaiementRequest;
use App\Http\Resources\PaiementResource;
use App\Models\Paiement;
use App\Services\Payments\PaymentService;
use Illuminate\Http\Request;

class PaiementController extends Controller
{
    public function __construct(private readonly PaymentService $paymentService)
    {
    }

    /**
     * Liste paginée des paiements (lecture seule).
     */
    public function index(Request $request)
    {
        $query = Paiement::query()
            ->select(['id', 'reference', 'statut', 'montant', 'payeur_id', 'facture_id', 'created_at'])
            ->orderByDesc('created_at');

        if ($request->filled('statut')) {
            $query->where('statut', $request->input('statut'));
        }

        $payments = $query->paginate(15);

        return PaiementResource::collection($payments);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PaiementRequest $request)
    {
        $validated = $request->validated();

        // Forcer la référence commande si fournie
        if (!empty($validated['commande_id']) && empty($validated['type_reference'])) {
            $validated['type_reference'] = 'commande_pharmaceutique';
            $validated['reference_id'] = $validated['reference_id'] ?? $validated['commande_id'];
        }

        $result = $this->paymentService->createPayment(
            $validated,
            $request->header('Idempotency-Key'),
            $request
        );

        $status = $result['created'] ? 201 : 200;

        return (new PaiementResource($result['payment']))
            ->response()
            ->setStatusCode($status);
    }

    /**
     * Display the specified resource.
     */
    public function show(Paiement $paiement): PaiementResource
    {
        return new PaiementResource($paiement);
    }

    /**
     * Confirmer un paiement (hook passerelle ou backoffice)
     */
    public function confirm(Request $request, Paiement $paiement): PaiementResource
    {
        $updated = $this->paymentService->confirmPayment(
            $paiement,
            $request->all(),
            $request->getContent(),
            $request->header('X-Signature')
        );

        return new PaiementResource($updated);
    }
}
