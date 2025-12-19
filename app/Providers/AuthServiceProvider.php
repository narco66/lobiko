<?php

namespace App\Providers;

use App\Models\Consultation;
use App\Models\DossierMedical;
use App\Models\Ordonnance;
use App\Models\Facture;
use App\Models\User;
use App\Models\Doctor;
use App\Models\MedicalStructure;
use App\Models\MedicalService;
use App\Models\Specialty;
use App\Models\DoctorSchedule;
use App\Models\Paiement;
use App\Policies\DoctorSchedulePolicy;
use App\Policies\PaiementPolicy;
use App\Models\Article;
use App\Models\ArticleCategory;
use App\Models\BlogTag;
use App\Models\MediaFile;
use App\Models\CommandePharmaceutique;
use App\Policies\CommandePharmaceutiquePolicy;
use App\Models\RendezVous;
use App\Policies\RendezVousPolicy;
use App\Models\Convention;
use App\Models\Claim;
use App\Policies\ConventionPolicy;
use App\Policies\ClaimPolicy;
use App\Models\Partner;
use App\Policies\PartnerPolicy;
use App\Policies\ArticlePolicy;
use App\Policies\ArticleCategoryPolicy;
use App\Policies\BlogTagPolicy;
use App\Policies\MediaFilePolicy;
use App\Policies\DoctorPolicy;
use App\Policies\MedicalStructurePolicy;
use App\Policies\MedicalServicePolicy;
use App\Policies\SpecialtyPolicy;
use App\Policies\ConsultationPolicy;
use App\Policies\DossierMedicalPolicy;
use App\Policies\OrdonnancePolicy;
use App\Policies\FacturePolicy;
use App\Policies\UserPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Consultation::class => ConsultationPolicy::class,
        DossierMedical::class => DossierMedicalPolicy::class,
        Ordonnance::class => OrdonnancePolicy::class,
        Facture::class => FacturePolicy::class,
        User::class => UserPolicy::class,
        Doctor::class => DoctorPolicy::class,
        MedicalStructure::class => MedicalStructurePolicy::class,
        MedicalService::class => MedicalServicePolicy::class,
        Specialty::class => SpecialtyPolicy::class,
        DoctorSchedule::class => DoctorSchedulePolicy::class,
        Paiement::class => PaiementPolicy::class,
        Article::class => ArticlePolicy::class,
        ArticleCategory::class => ArticleCategoryPolicy::class,
        BlogTag::class => BlogTagPolicy::class,
        MediaFile::class => MediaFilePolicy::class,
        CommandePharmaceutique::class => CommandePharmaceutiquePolicy::class,
        RendezVous::class => RendezVousPolicy::class,
        Convention::class => ConventionPolicy::class,
        Claim::class => ClaimPolicy::class,
        Partner::class => PartnerPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Super-admin bypass
        Gate::before(function ($user) {
            return $user->hasRole('super-admin') ? true : null;
        });
    }
}
