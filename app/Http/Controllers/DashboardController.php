<?php

namespace App\Http\Controllers;

use App\Services\Dashboard\DashboardService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class DashboardController extends Controller
{
    public function __construct(private readonly DashboardService $service)
    {
    }

    public function __invoke(Request $request)
    {
        $user = $request->user();

        // Autorisation générique (l'accès au dashboard backend requiert l'auth)
        if (!$user) {
            abort(403);
        }

        $viewModel = $this->service->forUser($user);

        return view('dashboard.index', [
            'viewModel' => $viewModel,
            'user' => $user,
        ]);
    }
}
