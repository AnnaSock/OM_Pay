<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OtpVerificationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'message' => $this->resource['message'],
            'verified_at' => now()->format('Y-m-d H:i:s'),
            'status' => 'verified',
            'user_info' => [
                'numero_user' => $request->input('numero_user'),
            ],
        ];
    }
}