<?php

namespace App\Services\Dashboard;

use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DashboardService
{
    public function forUser(User $user): array
    {
        if ($user->hasRole(['super-admin', 'admin'])) {
            return $this->forAdmin($user);
        }

        if ($user->hasRole(['medecin', 'professionnel'])) {
            return $this->forProfessional($user);
        }

        return $this->forPatient($user);
    }

    public function forAdmin(User $user): array
    {
        $stats = Cache::remember('dashboard.admin.' . $user->id, now()->addMinutes(10), function () {
            return [
                'users_total' => $this->countIfTable('users'),
                'users_last7' => $this->countIfTable('users', fn($q) => $q->where('created_at', '>=', now()->subDays(7))),
                'patients_total' => $this->countUsersByRole(['patient']),
                'pros_total' => $this->countUsersByRole(['medecin', 'pharmacien']),
                'consultations_month' => $this->countIfTable('consultations', fn($q) => $q->whereMonth('created_at', now()->month)),
                'consultations_today' => $this->countIfTable('consultations', fn($q) => $q->whereDate('created_at', now()->toDateString())),
                'payments_pending' => $this->countIfTable('paiements', fn($q) => $q->where('statut', 'en_cours')->orWhere('statut', 'initie')),
                'payments_confirmed' => $this->countIfTable('paiements', fn($q) => $q->where('statut', 'confirme')),
                'payments_failed' => $this->countIfTable('paiements', fn($q) => $q->where('statut', 'echoue')),
            ];
        });

        $alerts = [];
        if ($stats['payments_pending'] > 0) {
            $alerts[] = ['type' => 'warning', 'label' => "{$stats['payments_pending']} paiement(s) en attente de confirmation"];
        }

        return [
            'role' => 'admin',
            'kpis' => [
                ['label' => 'Utilisateurs', 'value' => $stats['users_total'], 'icon' => 'fas fa-users', 'variant' => 'primary'],
                ['label' => 'Nouveaux (7j)', 'value' => $stats['users_last7'], 'icon' => 'fas fa-user-plus', 'variant' => 'info'],
                ['label' => 'Consultations (mois)', 'value' => $stats['consultations_month'], 'icon' => 'fas fa-stethoscope', 'variant' => 'success'],
                ['label' => 'Paiements confirmés', 'value' => $stats['payments_confirmed'], 'icon' => 'fas fa-check-circle', 'variant' => 'success'],
            ],
        'alerts' => $alerts,
            'tables' => [
                'activity' => $this->recentActivity(),
            ],
        ];
    }

    public function forProfessional(User $user): array
    {
        $stats = Cache::remember('dashboard.pro.' . $user->id, now()->addMinutes(5), function () use ($user) {
            return [
                'consultations_today' => $this->countIfTable('consultations', fn($q) => $q->where('professionnel_id', $user->id)->whereDate('created_at', now()->toDateString())),
                'consultations_month' => $this->countIfTable('consultations', fn($q) => $q->where('professionnel_id', $user->id)->whereMonth('created_at', now()->month)),
                'patients_seen' => $this->countDistinctIfTable('consultations', 'patient_id', fn($q) => $q->where('professionnel_id', $user->id)),
                'ordonnances' => $this->countIfTable('ordonnances', fn($q) => $q->where('prescripteur_id', $user->id)),
            ];
        });

        return [
            'role' => 'pro',
            'kpis' => [
                ['label' => 'Consultations (jour)', 'value' => $stats['consultations_today'], 'icon' => 'fas fa-calendar-day', 'variant' => 'info'],
                ['label' => 'Consultations (mois)', 'value' => $stats['consultations_month'], 'icon' => 'fas fa-calendar-alt', 'variant' => 'secondary'],
                ['label' => 'Patients suivis', 'value' => $stats['patients_seen'], 'icon' => 'fas fa-user-md', 'variant' => 'primary'],
                ['label' => 'Ordonnances', 'value' => $stats['ordonnances'], 'icon' => 'fas fa-file-prescription', 'variant' => 'success'],
            ],
            'tables' => [
                'next_consultations' => $this->recentConsultations($user, 'professionnel_id'),
                'recent_ordonnances' => $this->recentOrdonnances($user, 'prescripteur_id'),
            ],
            'alerts' => [],
        ];
    }

    public function forPatient(User $user): array
    {
        $stats = Cache::remember('dashboard.patient.' . $user->id, now()->addMinutes(5), function () use ($user) {
            return [
                'consultations' => $this->countIfTable('consultations', fn($q) => $q->where('patient_id', $user->id)),
                'factures' => $this->countIfTable('factures', fn($q) => $q->where('patient_id', $user->id)),
                'paiements' => $this->countIfTable('paiements', fn($q) => $q->where('payeur_id', $user->id)),
            ];
        });

        return [
            'role' => 'patient',
            'kpis' => [
                ['label' => 'Consultations', 'value' => $stats['consultations'], 'icon' => 'fas fa-stethoscope', 'variant' => 'primary'],
                ['label' => 'Factures', 'value' => $stats['factures'], 'icon' => 'fas fa-file-invoice', 'variant' => 'secondary'],
                ['label' => 'Paiements', 'value' => $stats['paiements'], 'icon' => 'fas fa-credit-card', 'variant' => 'success'],
            ],
            'tables' => [
                'next_consultations' => $this->recentConsultations($user, 'patient_id'),
                'recent_ordonnances' => $this->recentOrdonnances($user, 'patient_id'),
            ],
            'alerts' => [],
        ];
    }

    private function countIfTable(string $table, callable $callback = null): int
    {
        if (!Schema::hasTable($table)) {
            return 0;
        }
        $query = DB::table($table);
        if ($callback) {
            $callback($query);
        }
        return (int) $query->count();
    }

    /**
     * Compte les utilisateurs par r徼le via Eloquent (n鯳ssaire pour les jointures Spatie)
     */
    private function countUsersByRole(array $roles): int
    {
        if (!Schema::hasTable('users') || !Schema::hasTable('roles') || !Schema::hasTable('model_has_roles')) {
            return 0;
        }

        return User::role($roles)->count();
    }

    private function countDistinctIfTable(string $table, string $column, callable $callback = null): int
    {
        if (!Schema::hasTable($table)) {
            return 0;
        }
        $query = DB::table($table);
        if ($callback) {
            $callback($query);
        }
        return (int) $query->distinct($column)->count($column);
    }

    private function recentActivity(): array
    {
        if (!Schema::hasTable('activity_log')) {
            return [];
        }

        return Cache::remember('dashboard.activity', now()->addMinutes(5), function () {
            return DB::table('activity_log')
                ->orderByDesc('created_at')
                ->limit(10)
                ->get(['description', 'created_at', 'causer_type', 'subject_type'])
                ->toArray();
        });
    }

    private function recentConsultations(User $user, string $column): array
    {
        if (!Schema::hasTable('consultations')) {
            return [];
        }

        return DB::table('consultations')
            ->where($column, $user->id)
            ->orderByDesc('date_consultation')
            ->limit(5)
            ->get(['id', 'numero_consultation', 'date_consultation', 'motif_consultation'])
            ->toArray();
    }

    private function recentOrdonnances(User $user, string $column): array
    {
        if (!Schema::hasTable('ordonnances')) {
            return [];
        }

        return DB::table('ordonnances')
            ->where($column, $user->id)
            ->orderByDesc('created_at')
            ->limit(5)
            ->get(['id', 'consultation_id', 'statut', 'created_at'])
            ->toArray();
    }
}
