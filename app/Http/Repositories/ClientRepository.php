<?php

namespace App\Http\Repositories;

use App\Http\Interfaces\IClientRepository;
use App\Models\Compte;
use App\Models\User;

class ClientRepository implements IClientRepository
{
    /**
     * Récupère le compte par numéro utilisateur
     *
     * @param string $numeroUser
     * @return Compte|null
     */
    public function findCompteByNumeroUser(string $numeroUser): ?Compte
    {
        return Compte::where('numero_user', $numeroUser)->first();
    }

    /**
     * Récupère l'utilisateur associé à un compte
     *
     * @param Compte $compte
     * @return User|null
     */
    public function getUserFromCompte(Compte $compte): ?User
    {
        return $compte->user;
    }

    /**
     * Récupère le premier compte d'un utilisateur
     *
     * @param User $user
     * @return Compte|null
     */
    public function getFirstCompteFromUser(User $user): ?Compte
    {
        return $user->comptes()->first();
    }

    /**
     * Récupère les transactions d'un compte
     *
     * @param Compte $compte
     * @return mixed
     */
    public function getTransactionsFromCompte(Compte $compte)
    {
        return $compte->transactions()->latest()->get();
    }
}