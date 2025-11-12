<?php

namespace App\Http\Services;

use App\Http\Interfaces\IUserRepository;
use App\Http\Interfaces\ICompteRepository;
use App\Http\Interfaces\ITransactionRepository;
use App\Models\Compte;
use App\Models\User;
use App\Traits\GeneratesUniqueCodes;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Exception;

class CompteService
{
    use GeneratesUniqueCodes;

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
     * @param float $initialDeposit
     * @return Compte
     * @throws Exception
     */
    public function createUserCompteWithInitialDeposit(array $userData, float $initialDeposit): Compte
    {
        return DB::transaction(function () use ($userData, $initialDeposit) {
            // 1. Récupérer ou créer l'utilisateur
            $user = $this->userRepository->firstOrCreateUser($userData);

            // 2. Générer les valeurs uniques pour le compte
            $numeroCompte = $this->generateUniqueNumeroCompte();
            $login = $this->generateUniqueLogin();
            $codeMarchand = $user instanceof \App\Models\Marchand ? $this->generateUniqueCodeMarchand() : null;

            // 3. Créer le compte avec les valeurs générées
            $compteData = [
                'id' => (string) \Illuminate\Support\Str::uuid(),
                'user_id' => $user->id,
                'user_type' => get_class($user),
                'numero_compte' => $numeroCompte,
                'numero_user' => $userData['numero_user'] ?? null,
                'login' => $login,
                'password' => isset($userData['password']) ? Hash::make($userData['password']) : null,
                'code_marchand' => $codeMarchand,
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