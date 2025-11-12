<?php

namespace App\Http\Interfaces;

use App\Models\Compte;
use App\Models\User;

interface IClientRepository
{
    public function findCompteByNumeroUser(string $numeroUser): ?Compte;
    public function getUserFromCompte(Compte $compte): ?User;
    public function getFirstCompteFromUser(User $user): ?Compte;
    public function getTransactionsFromCompte(Compte $compte);
}