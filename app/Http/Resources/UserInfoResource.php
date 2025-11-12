<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserInfoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'user' => new UserResource($this->resource['user']),
            'compte' => new CompteResource($this->resource['compte']),
            'transactions' => TransactionResource::collection($this->resource['transactions']),
            'summary' => [
                'total_transactions' => $this->resource['transactions']->count(),
                'current_balance' => $this->resource['compte']->solde,
                'last_transaction_date' => $this->resource['transactions']->first()?->created_at?->format('Y-m-d H:i:s'),
            ],
        ];
    }
}