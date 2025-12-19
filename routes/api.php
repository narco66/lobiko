<?php

use App\Http\Controllers\PaiementController;
use Illuminate\Support\Facades\Route;

Route::prefix('payments')->group(function () {
    Route::get('/', [PaiementController::class, 'index'])->name('payments.index');
    Route::post('/', [PaiementController::class, 'store'])->name('payments.store');
    Route::get('/{paiement}', [PaiementController::class, 'show'])->name('payments.show');
    Route::post('/{paiement}/confirm', [PaiementController::class, 'confirm'])->name('payments.confirm');
});
