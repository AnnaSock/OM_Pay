<?php

namespace App\Traits;

use App\Models\Compte;

trait GeneratesUniqueLogin
{
    private function generateUniqueLogin(): string
    {
        do {
            $login = 'USER' . str_pad(mt_rand(10000, 99999), 5, '0', STR_PAD_LEFT);
        } while (Compte::where('login', $login)->exists());

        return $login;
    }
}