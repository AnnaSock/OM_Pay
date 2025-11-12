<?php

namespace App\Http\Interfaces;

use App\Models\Compte;

interface ICompteRepository
{
    public function compteExistsForUser(int $userId, string $accountNumber): bool;
    public function createCompte(array $compteData): Compte;
}