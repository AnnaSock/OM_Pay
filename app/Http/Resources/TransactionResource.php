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
        return [
            'id' => $this->id,
            'montant' => $this->montant,
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
}