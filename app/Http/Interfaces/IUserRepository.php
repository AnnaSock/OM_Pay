<?php

namespace App\Http\Interfaces;

use App\Models\User;

interface IUserRepository
{
    public function firstOrCreateUser(array $userData): User;
    public function userExists(int $userId): bool;
}