<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CompteResource extends JsonResource
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
            'numero_compte' => $this->numero_compte,
            'numero_user' => $this->numero_user,
            'code_marchand' => $this->code_marchand,
            'login' => $this->login,
            'solde' => $this->solde,
            'user_type' => $this->user_type,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
            // Inclure les transactions seulement si demandé
            'transactions' => $this->when($request->has('include_transactions'),
                fn() => TransactionResource::collection($this->whenLoaded('transactions'))
            ),
            // Inclure l'utilisateur seulement si demandé
            'user' => $this->when($request->has('include_user'),
                fn() => new UserResource($this->whenLoaded('user'))
            ),
        ];
    }
}