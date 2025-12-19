<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AnonymizeSoftDeletedUsersCommand extends Command
{
    protected $signature = 'security:anonymize-users {days=90 : Anonymiser les utilisateurs supprimés depuis N jours}';

    protected $description = 'Anonymise les comptes soft-deleted après rétention (RGPD).';

    public function handle(): int
    {
        $days = (int) $this->argument('days');
        $cutoff = now()->subDays($days);

        $users = User::onlyTrashed()
            ->where('deleted_at', '<', $cutoff)
            ->get();

        $count = 0;
        foreach ($users as $user) {
            $anonEmail = 'anon+' . Str::uuid() . '@example.com';
            $user->update([
                'email' => $anonEmail,
                'telephone' => null,
                'adresse_rue' => null,
                'adresse_quartier' => null,
                'adresse_ville' => null,
                'adresse_pays' => null,
                'piece_identite' => null,
                'piece_identite_numero' => null,
                'piece_identite_type' => null,
                'api_token' => null,
            ]);
            $count++;
        }

        $this->info("Utilisateurs anonymisés : {$count}");
        return self::SUCCESS;
    }
}
