<?php

namespace App\Http\Repositories;

use App\Http\Interfaces\IUserRepository;
use App\Models\Client;
use App\Models\User;
use App\Enums\Role;

class UserRepository implements IUserRepository
{
    /**
     * Récupère ou crée un utilisateur avec les données fournies
     *
     * @param array $userData
     * @return User
     */
    public function firstOrCreateUser(array $userData): User
    {
        // Définir le rôle par défaut à Client si non fourni
        $userData['role'] = $userData['role'] ?? Role::CLIENT;

        return Client::firstOrCreate(
            ['email' => $userData['email']], // Critères de recherche
            $userData // Données à créer si inexistant
        );
    }

    /**
     * Vérifie si un utilisateur existe par ID
     *
     * @param int $userId
     * @return bool
     */
    public function userExists(int $userId): bool
    {
        return User::where('id', $userId)->exists();
    }
}