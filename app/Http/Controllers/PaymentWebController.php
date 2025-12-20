<?php

namespace App\Http\Controllers;

use App\Models\Paiement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class PaymentWebController extends Controller
{
    public function index(Request $request)
    {
        Gate::authorize('viewAny', Paiement::class);

        $query = Paiement::with(['payeur'])
            ->orderByDesc('created_at')
            ->when($request->filled('statut'), fn($q) => $q->where('statut', $request->string('statut')))
            ->when($request->filled('reference'), fn($q) => $q->where('numero_paiement', 'like', '%'.$request->reference.'%'))
            ->when($request->filled('mode_paiement'), fn($q) => $q->where('mode_paiement', $request->mode_paiement));

        $payments = $query->paginate(15);

        return view('payments.index', compact('payments'));
    }
}
