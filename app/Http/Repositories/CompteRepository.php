<?php

namespace App\Http\Repositories;

use App\Http\Interfaces\ICompteRepository;
use App\Models\Compte;

class CompteRepository implements ICompteRepository
{
    /**
     * Vérifie si un compte existe pour un utilisateur avec ce numéro
     *
     * @param int $userId
     * @param string $accountNumber
     * @return bool
     */
    public function compteExistsForUser(int $userId, string $accountNumber): bool
    {
        return Compte::where('user_id', $userId)
            ->where('numero_compte', $accountNumber)
            ->exists();
    }

    /**
     * Crée un nouveau compte
     *
     * @param array $compteData
     * @return Compte
     */
    public function createCompte(array $compteData): Compte
    {
        return Compte::create($compteData);
    }
}