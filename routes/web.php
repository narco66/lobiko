<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicServicesController;
use App\Http\Controllers\PublicServiceRequestController;
use App\Http\Controllers\Admin\ServiceRequestAdminController;
use App\Http\Controllers\Admin\RequestStatusController;
use App\Http\Controllers\TeleconsultationController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CommandePharmaceutiqueController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/services', function () {
    return view('services');
})->name('services');

// Pages publiques complémentaires
Route::get('/about', fn () => view('about'))->name('about');
Route::get('/pricing', [HomeController::class, 'pricing'])->name('pricing');
Route::get('/careers', fn () => view('pages.careers'))->name('careers');
Route::prefix('partners')->group(function () {
    Route::get('/', [\App\Http\Controllers\PartnerController::class, 'index'])->name('partners');
    Route::middleware(['auth', 'verified'])->group(function () {
        Route::get('/create', [\App\Http\Controllers\PartnerController::class, 'create'])->name('partners.create');
        Route::post('/', [\App\Http\Controllers\PartnerController::class, 'store'])->name('partners.store');
        Route::get('/{partner}/edit', [\App\Http\Controllers\PartnerController::class, 'edit'])->name('partners.edit');
        Route::put('/{partner}', [\App\Http\Controllers\PartnerController::class, 'update'])->name('partners.update');
        Route::delete('/{partner}', [\App\Http\Controllers\PartnerController::class, 'destroy'])->name('partners.destroy');
    });
});
Route::get('/press', fn () => view('pages.placeholder', [
    'title' => 'Presse',
    'section' => 'Presse',
    'message' => 'Kit média et communiqués à venir.'
]))->name('press');
Route::get('/faq', [\App\Http\Controllers\FaqController::class, 'index'])->name('faq');
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

    Route::resource('structures', \App\Http\Controllers\MedicalStructureController::class);
    Route::resource('doctors', \App\Http\Controllers\DoctorController::class);
    Route::resource('specialties', \App\Http\Controllers\SpecialtyController::class)->except(['show']);
    Route::resource('services', \App\Http\Controllers\MedicalServiceController::class);
    Route::post('doctor-schedules', [\App\Http\Controllers\DoctorScheduleController::class, 'store'])->name('doctor-schedules.store');
    Route::delete('doctor-schedules/{doctorSchedule}', [\App\Http\Controllers\DoctorScheduleController::class, 'destroy'])->name('doctor-schedules.destroy');
    Route::get('payments', [\App\Http\Controllers\PaymentWebController::class, 'index'])->name('payments.index');
});

Route::get('/dashboard', DashboardController::class)
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// Télconsultation (protégé auth)
Route::middleware('auth')->prefix('teleconsultation')->group(function () {
    Route::get('/room/{consultation}', [TeleconsultationController::class, 'room'])->name('teleconsultation.room');
    Route::post('/room/{consultation}/join', [TeleconsultationController::class, 'join'])->name('teleconsultation.join');
    Route::post('/room/{consultation}/leave', [TeleconsultationController::class, 'leave'])->name('teleconsultation.leave');
    Route::post('/room/{consultation}/end', [TeleconsultationController::class, 'end'])->name('teleconsultation.end');
    Route::post('/room/{consultation}/message', [TeleconsultationController::class, 'sendMessage'])->name('teleconsultation.message');
    Route::post('/room/{consultation}/file', [TeleconsultationController::class, 'shareFile'])->name('teleconsultation.file');
    Route::get('/file/{consultation}/{file}', [TeleconsultationController::class, 'downloadFile'])->name('teleconsultation.file.download');
    Route::get('/', fn () => view('teleconsultations.index'))->name('teleconsultation.index');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('dossiers-medicaux', \App\Http\Controllers\DossierMedicalController::class);
    Route::resource('users', UserController::class)->except(['destroy']);
    Route::resource('consultations', \App\Http\Controllers\ConsultationController::class)->except(['destroy']);
    Route::resource('ordonnances', \App\Http\Controllers\OrdonnanceController::class);

    // Commandes pharmaceutiques (commande → préparation → livraison/retrait)
    Route::prefix('commandes-pharma')->as('commandes-pharma.')->group(function () {
        Route::get('/', [CommandePharmaceutiqueController::class, 'index'])->name('index');
        Route::get('/create', [CommandePharmaceutiqueController::class, 'create'])->name('create');
        Route::post('/', [CommandePharmaceutiqueController::class, 'store'])->name('store');
        Route::get('/dashboard', [CommandePharmaceutiqueController::class, 'dashboard'])->name('dashboard');
        Route::get('/suivi/{numeroCommande}', [CommandePharmaceutiqueController::class, 'suivre'])->name('suivi');
        Route::get('/valider-code/{code}', [CommandePharmaceutiqueController::class, 'validerCode'])->name('valider-code');
        Route::get('/{commande}/bon', [CommandePharmaceutiqueController::class, 'telechargerBon'])->name('bon');
        Route::get('/recherche/produits', [CommandePharmaceutiqueController::class, 'rechercherProduits'])->name('recherche-produits');
        Route::get('/{commande}', [CommandePharmaceutiqueController::class, 'show'])->name('show');
        Route::post('/{commande}/confirmer', [CommandePharmaceutiqueController::class, 'confirmer'])->name('confirmer');
        Route::post('/{commande}/preparer', [CommandePharmaceutiqueController::class, 'preparer'])->name('preparer');
        Route::post('/{commande}/prete', [CommandePharmaceutiqueController::class, 'marquerPrete'])->name('prete');
        Route::post('/{commande}/livraison', [CommandePharmaceutiqueController::class, 'demarrerLivraison'])->name('livraison');
        Route::post('/{commande}/livree', [CommandePharmaceutiqueController::class, 'confirmerLivraison'])->name('livree');
        Route::post('/{commande}/annuler', [CommandePharmaceutiqueController::class, 'annuler'])->name('annuler');
    });
});

// Déconnexion via GET pour éviter 419 si l’URL est appelée directement
Route::get('/logout', function () {
    \Illuminate\Support\Facades\Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect()->route('home');
})->name('logout.get');

// Blog (admin)
Route::middleware(['auth', 'verified'])
    ->prefix('admin/blog')
    ->as('admin.blog.')
    ->group(function () {
        Route::get('/posts', [\App\Http\Controllers\BlogPostController::class, 'index'])->name('posts.index');
        Route::get('/posts/create', [\App\Http\Controllers\BlogPostController::class, 'create'])->name('posts.create');
        Route::post('/posts', [\App\Http\Controllers\BlogPostController::class, 'store'])->name('posts.store');
        Route::get('/posts/{article}/edit', [\App\Http\Controllers\BlogPostController::class, 'edit'])->name('posts.edit');
        Route::put('/posts/{article}', [\App\Http\Controllers\BlogPostController::class, 'update'])->name('posts.update');
        Route::delete('/posts/{article}', [\App\Http\Controllers\BlogPostController::class, 'destroy'])->name('posts.destroy');
        Route::post('/posts/{article}/publish', [\App\Http\Controllers\BlogPostController::class, 'publish'])->name('posts.publish');
        Route::post('/posts/{article}/unpublish', [\App\Http\Controllers\BlogPostController::class, 'unpublish'])->name('posts.unpublish');

        Route::resource('categories', \App\Http\Controllers\BlogCategoryController::class)->except(['show']);
        Route::resource('tags', \App\Http\Controllers\BlogTagController::class)->except(['show']);

        Route::get('/media', [\App\Http\Controllers\MediaController::class, 'index'])->name('media.index');
        Route::post('/media', [\App\Http\Controllers\MediaController::class, 'store'])->name('media.store');
        Route::delete('/media/{media}', [\App\Http\Controllers\MediaController::class, 'destroy'])->name('media.destroy');
    });

require __DIR__.'/auth.php';
