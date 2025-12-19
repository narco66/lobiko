<?php

namespace App\Services;

use App\Models\RendezVous;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class RendezVousService
{
    public const STATUTS = ['en_attente', 'confirme', 'reporte', 'en_cours', 'termine', 'annule', 'no_show'];

    /**
     * Crée un rendez-vous en contrôlant les collisions de créneaux.
     */
    public function creer(array $data): RendezVous
    {
        $debut = Carbon::parse($data['date_heure']);
        $fin = isset($data['date_heure_fin'])
            ? Carbon::parse($data['date_heure_fin'])
            : $debut->copy()->addMinutes($data['duree_prevue'] ?? 30);

        $this->verifierDisponibilite($data['professionnel_id'], $debut, $fin);

        return DB::transaction(function () use ($data, $debut, $fin) {
            return RendezVous::create(array_merge($data, [
                'id' => $data['id'] ?? (string) \Illuminate\Support\Str::uuid(),
                'date_heure' => $debut,
                'date_heure_fin' => $fin,
                'statut' => $data['statut'] ?? 'en_attente',
                'numero_rdv' => $data['numero_rdv'] ?? ('RDV-' . now()->format('Ymd') . '-' . random_int(1000, 9999)),
            ]));
        });
    }

    /**
     * Annule un rendez-vous avec motif.
     */
    public function annuler(RendezVous $rdv, string $motif, string $annulePar): RendezVous
    {
        if (in_array($rdv->statut, ['termine', 'annule'])) {
            throw new \RuntimeException('Rendez-vous déjà terminé ou annulé');
        }

        $rdv->update([
            'statut' => 'annule',
            'raison_annulation' => $motif,
            'annule_par' => $annulePar,
            'annule_at' => now(),
        ]);

        return $rdv;
    }

    /**
     * Reprogramme un rendez-vous et marque l'ancien comme reporté.
     */
    public function reprogrammer(RendezVous $rdv, Carbon $nouvelleDate, int $dureeMinutes = 30): RendezVous
    {
        $fin = $nouvelleDate->copy()->addMinutes($dureeMinutes);
        $this->verifierDisponibilite($rdv->professionnel_id, $nouvelleDate, $fin, $rdv->id);

        $rdv->update([
            'statut' => 'reporte',
            'reporte_vers' => null,
        ]);

        $reprogramme = RendezVous::create([
            'id' => (string) \Illuminate\Support\Str::uuid(),
            'numero_rdv' => 'RDV-' . now()->format('Ymd') . '-' . random_int(1000, 9999),
            'patient_id' => $rdv->patient_id,
            'professionnel_id' => $rdv->professionnel_id,
            'structure_id' => $rdv->structure_id,
            'date_heure' => $nouvelleDate,
            'date_heure_fin' => $fin,
            'duree_prevue' => $dureeMinutes,
            'type' => $rdv->type,
            'modalite' => $rdv->modalite,
            'specialite' => $rdv->specialite,
            'motif' => $rdv->motif,
            'statut' => 'confirme',
            'reporte_de' => $rdv->id,
        ]);

        $rdv->update(['reporte_vers' => $reprogramme->id]);

        return $reprogramme;
    }

    /**
     * Vérifie qu'un professionnel n'a pas de chevauchement de créneau.
     */
    protected function verifierDisponibilite(string $professionnelId, Carbon $debut, Carbon $fin, ?string $ignoreId = null): void
    {
        $collision = RendezVous::where('professionnel_id', $professionnelId)
            ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
            ->where(function ($q) use ($debut, $fin) {
                $q->whereBetween('date_heure', [$debut, $fin])
                    ->orWhereBetween('date_heure_fin', [$debut, $fin])
                    ->orWhere(function ($q2) use ($debut, $fin) {
                        $q2->where('date_heure', '<=', $debut)->where('date_heure_fin', '>=', $fin);
                    });
            })
            ->exists();

        if ($collision) {
            throw new \RuntimeException('Créneau déjà réservé pour ce professionnel');
        }
    }
}

