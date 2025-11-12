<?php

namespace App\Http\Services;

use App\Http\Interfaces\IUserRepository;
use App\Http\Interfaces\ICompteRepository;
use App\Http\Interfaces\ITransactionRepository;
use App\Models\Compte;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Exception;

class CompteService
{
    protected IUserRepository $userRepository;
    protected ICompteRepository $compteRepository;
    protected ITransactionRepository $transactionRepository;

    public function __construct(
        IUserRepository $userRepository,
        ICompteRepository $compteRepository,
        ITransactionRepository $transactionRepository
    ) {
        $this->userRepository = $userRepository;
        $this->compteRepository = $compteRepository;
        $this->transactionRepository = $transactionRepository;
    }

    /**
     * Crée un utilisateur, un compte et une transaction de dépôt initiale
     *
     * @param array $userData
     * @param string $accountNumber
     * @param float $initialDeposit
     * @return Compte
     * @throws Exception
     */
    public function createUserCompteWithInitialDeposit(array $userData, string $accountNumber, float $initialDeposit): Compte
    {
        return DB::transaction(function () use ($userData, $accountNumber, $initialDeposit) {
            // 1. Récupérer ou créer l'utilisateur
            $user = $this->userRepository->firstOrCreateUser($userData);

            // 2. Vérifier si le numéro de compte est unique pour cet utilisateur
            if ($this->compteRepository->compteExistsForUser($user->id, $accountNumber)) {
                throw new Exception('Un compte avec ce numéro existe déjà pour cet utilisateur');
            }

            // 3. Créer le compte
            $compteData = [
                'user_id' => $user->id,
                'user_type' => get_class($user),
                'numero_compte' => $accountNumber,
                'numero_user' => $userData['numero_user'] ?? null,
                'login' => $userData['login'] ?? null,
                'password' => $userData['password'] ?? null,
                'code_marchand' => $userData['code_marchand'] ?? null,
            ];

            $compte = $this->compteRepository->createCompte($compteData);

            // 4. Créer la transaction de dépôt initiale
            $transactionData = [
                'id' => (string) \Illuminate\Support\Str::uuid(),
                'montant' => $initialDeposit,
                'compte_id' => $compte->id,
            ];

            $this->transactionRepository->createDepositTransaction($transactionData);

            return $compte;
        });
    }
}