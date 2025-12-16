<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicServicesController;
use App\Http\Controllers\PublicServiceRequestController;
use App\Http\Controllers\Admin\ServiceRequestAdminController;
use App\Http\Controllers\Admin\RequestStatusController;
use App\Http\Controllers\TeleconsultationController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/services', function () {
    return view('services');
})->name('services');

// Pages publiques complémentaires
Route::get('/about', fn () => view('about'))->name('about');
Route::get('/pricing', [HomeController::class, 'pricing'])->name('pricing');
Route::get('/careers', fn () => view('pages.placeholder', [
    'title' => 'Carrières',
    'section' => 'Carrières',
    'message' => 'Rejoignez-nous : les offres seront publiées ici.'
]))->name('careers');
Route::get('/partners', fn () => view('pages.placeholder', [
    'title' => 'Partenaires',
    'section' => 'Partenaires',
    'message' => 'Espace partenaires en préparation.'
]))->name('partners');
Route::get('/press', fn () => view('pages.placeholder', [
    'title' => 'Presse',
    'section' => 'Presse',
    'message' => 'Kit média et communiqués à venir.'
]))->name('press');
Route::get('/faq', fn () => view('pages.placeholder', [
    'title' => 'FAQ',
    'section' => 'Support',
    'message' => 'Questions fréquentes en cours de rédaction.'
]))->name('faq');
Route::get('/help', fn () => view('pages.placeholder', [
    'title' => 'Centre d’aide',
    'section' => 'Support',
    'message' => 'Centre d’aide en cours de construction.'
]))->name('help');
Route::get('/privacy', fn () => view('pages.placeholder', [
    'title' => 'Confidentialité',
    'section' => 'Légal',
    'message' => 'Politique de confidentialité en cours de finalisation.'
]))->name('privacy');
Route::get('/terms', fn () => view('pages.placeholder', [
    'title' => 'Conditions Générales',
    'section' => 'Légal',
    'message' => 'CGU en cours de finalisation.'
]))->name('terms');
Route::get('/blog', fn () => view('pages.placeholder', [
    'title' => 'Blog',
    'section' => 'Blog',
    'message' => 'Les articles seront publiés prochainement.'
]))->name('blog.index');
Route::get('/blog/{slug}', fn ($slug) => view('pages.placeholder', [
    'title' => 'Article',
    'section' => 'Blog',
    'message' => "L'article {$slug} sera bientôt disponible."
]))->name('blog.show');
Route::get('/doctor/{doctor}', [HomeController::class, 'doctorProfile'])->name('doctor.profile');

// Pages publiques des services
Route::controller(PublicServicesController::class)->group(function () {
    Route::get('/services/teleconsultation', 'teleconsultation')->name('services.teleconsultation');
    Route::get('/services/appointment', 'appointment')->name('services.appointment');
    Route::get('/services/pharmacy', 'pharmacy')->name('services.pharmacy');
    Route::get('/services/insurance', 'insurance')->name('services.insurance');
    Route::get('/services/emergency', 'emergency')->name('services.emergency');
});

Route::get('/professionals', [HomeController::class, 'searchProfessionals'])->name('search.professionals');

// Formulaires publics (pharmacie, assurance, urgence)
Route::get('/services/pharmacy/request', [PublicServiceRequestController::class, 'pharmacy'])->name('services.pharmacy.request');
Route::post('/services/pharmacy/request', [PublicServiceRequestController::class, 'storePharmacy'])->name('services.pharmacy.request.submit');

Route::get('/services/insurance/request', [PublicServiceRequestController::class, 'insurance'])->name('services.insurance.request');
Route::post('/services/insurance/request', [PublicServiceRequestController::class, 'storeInsurance'])->name('services.insurance.request.submit');

Route::get('/services/emergency/request', [PublicServiceRequestController::class, 'emergency'])->name('services.emergency.request');
Route::post('/services/emergency/request', [PublicServiceRequestController::class, 'storeEmergency'])->name('services.emergency.request.submit');

// Rendez-vous (flux public minimal)
Route::get('/appointments', [\App\Http\Controllers\RendezVousController::class, 'index'])->name('appointments.index');
Route::get('/appointments/create', [\App\Http\Controllers\RendezVousController::class, 'create'])->name('appointments.create');
Route::post('/appointments', [\App\Http\Controllers\RendezVousController::class, 'store'])->name('appointments.store');
Route::get('/appointments/thanks', [\App\Http\Controllers\RendezVousController::class, 'thanks'])->name('appointments.thanks');

// Placeholders publics pour d'autres liens du header
Route::get('/prescriptions', fn () => view('pages.placeholder', [
    'title' => 'Ordonnances',
    'section' => 'Dossier',
    'message' => 'Espace ordonnances en préparation.'
]))->name('prescriptions.index');

Route::get('/contact', [HomeController::class, 'contact'])->name('contact');
Route::post('/contact', [HomeController::class, 'submitContact'])->name('contact.submit');

// Admin suivi des demandes (protégé auth)
Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/requests', [ServiceRequestAdminController::class, 'index'])->name('requests.index');
    Route::get('/requests/export', [ServiceRequestAdminController::class, 'export'])->name('requests.export');
    Route::post('/requests/status', [RequestStatusController::class, 'update'])->name('requests.status');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Télconsultation (protégé auth)
Route::middleware('auth')->prefix('teleconsultation')->group(function () {
    Route::get('/room/{consultation}', [TeleconsultationController::class, 'room'])->name('teleconsultation.room');
    Route::post('/room/{consultation}/join', [TeleconsultationController::class, 'join'])->name('teleconsultation.join');
    Route::post('/room/{consultation}/leave', [TeleconsultationController::class, 'leave'])->name('teleconsultation.leave');
    Route::post('/room/{consultation}/end', [TeleconsultationController::class, 'end'])->name('teleconsultation.end');
    Route::post('/room/{consultation}/message', [TeleconsultationController::class, 'sendMessage'])->name('teleconsultation.message');
    Route::post('/room/{consultation}/file', [TeleconsultationController::class, 'shareFile'])->name('teleconsultation.file');
    Route::get('/file/{consultation}/{file}', [TeleconsultationController::class, 'downloadFile'])->name('teleconsultation.file.download');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
