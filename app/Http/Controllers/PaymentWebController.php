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

        $query = Paiement::query()->orderByDesc('created_at');
        if ($request->filled('statut')) {
            $query->where('statut', $request->string('statut'));
        }

        $payments = $query->paginate(15);

        return view('payments.index', compact('payments'));
    }
}
