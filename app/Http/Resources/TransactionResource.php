<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // Formater le montant avec le signe approprié
        $montantFormate = $this->formatMontantAvecSigne($this->montant, $this->type_transaction->value);

        return [
            'id' => $this->id,
            'montant_formate' => $montantFormate,
            'type_transaction' => $this->type_transaction,
            'compte_id' => $this->compte_id,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
            // Inclure le compte seulement si demandé
            'compte' => $this->when($request->has('include_compte'),
                fn() => new CompteResource($this->whenLoaded('compte'))
            ),
        ];
    }

    /**
     * Formate le montant avec le signe approprié selon le type de transaction
     *
     * @param float $montant
     * @param string $typeTransaction
     * @return string
     */
    private function formatMontantAvecSigne(float $montant, string $typeTransaction): string
    {
        if ($typeTransaction === 'Depot') {
            return '+' . number_format($montant, 2, '.', '');
        } elseif (in_array($typeTransaction, ['Retrait', 'Payement'])) {
            return '-' . number_format($montant, 2, '.', '');
        }

        // Par défaut, retourner le montant sans signe
        return number_format($montant, 2, '.', '');
    }
}