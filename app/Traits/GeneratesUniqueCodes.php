<?php

namespace App\Traits;

use App\Models\Compte;

trait GeneratesUniqueCodes
{
    private function generateUniqueLogin(): string
    {
        do {
            $login = 'USER' . str_pad(mt_rand(10000, 99999), 5, '0', STR_PAD_LEFT);
        } while (Compte::where('login', $login)->exists());

        return $login;
    }

    private function generateUniqueNumeroCompte(): string
    {
        do {
            $numero = '';
            for ($i = 0; $i < 10; $i++) {
                $numero .= mt_rand(0, 9);
            }
        } while (Compte::where('numero_compte', $numero)->exists());

        return $numero;
    }

    private function generateUniqueCodeMarchand(): string
    {
        do {
            $code = 'M' . str_pad(mt_rand(1000000, 9999999), 7, '0', STR_PAD_LEFT);
        } while (Compte::where('code_marchand', $code)->exists());

        return $code;
    }
}