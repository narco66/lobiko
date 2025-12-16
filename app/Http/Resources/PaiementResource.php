<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaiementResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'numero_paiement' => $this->numero_paiement,
            'facture_id' => $this->facture_id,
            'payeur_id' => $this->payeur_id,
            'commande_id' => $this->commande_id,
            'reference_id' => $this->reference_id,
            'type_reference' => $this->type_reference,
            'type_payeur' => $this->type_payeur,
            'mode_paiement' => $this->mode_paiement,
            'montant' => $this->montant,
            'devise' => $this->devise,
            'taux_change' => $this->taux_change,
            'montant_devise_locale' => $this->montant_devise_locale,
            'frais_transaction' => $this->frais_transaction,
            'montant_net' => $this->montant_net,
            'reference_transaction' => $this->reference_transaction,
            'reference_passerelle' => $this->reference_passerelle,
            'statut' => $this->statut,
            'idempotence_key' => $this->idempotence_key,
            'passerelle' => $this->passerelle,
            'code_autorisation' => $this->code_autorisation,
            'code_erreur' => $this->code_erreur,
            'message_erreur' => $this->message_erreur,
            'remboursable' => $this->remboursable,
            'montant_rembourse' => $this->montant_rembourse,
            'reference_remboursement' => $this->reference_remboursement,
            'valide' => $this->valide,
            'valide_par' => $this->valide_par,
            'agent_id' => $this->agent_id,
            'code_agent' => $this->code_agent,
            'lieu_paiement' => $this->lieu_paiement,
            'recu_pdf' => $this->recu_pdf,
            'preuve_paiement' => $this->preuve_paiement,
            'date_initiation' => optional($this->date_initiation)->toIso8601String(),
            'date_confirmation' => optional($this->date_confirmation)->toIso8601String(),
            'date_annulation' => optional($this->date_annulation)->toIso8601String(),
            'date_remboursement' => optional($this->date_remboursement)->toIso8601String(),
            'valide_at' => optional($this->valide_at)->toIso8601String(),
            'created_at' => optional($this->created_at)->toIso8601String(),
            'updated_at' => optional($this->updated_at)->toIso8601String(),
        ];
    }
}
