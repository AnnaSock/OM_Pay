<?php

namespace App\Http\Services;

use App\Events\OtpVerified;
use App\Http\Interfaces\IClientRepository;
use App\Models\Compte;
use App\Models\User;
use App\Traits\HandlesOtp;
use Exception;

class ClientService
{
    use HandlesOtp;

    protected IClientRepository $clientRepository;

    public function __construct(IClientRepository $clientRepository)
    {
        $this->clientRepository = $clientRepository;
    }

    /**
     * Récupère les informations de l'utilisateur connecté
     *
     * @param User $user
     * @return array
     * @throws Exception
     */
    public function getUserInfo(User $user): array
    {
        // Récupérer le compte de l'utilisateur
        $compte = $this->clientRepository->getFirstCompteFromUser($user);
        if (!$compte) {
            throw new Exception('Aucun compte trouvé pour cet utilisateur');
        }

        // Récupérer les transactions du compte
        $transactions = $this->clientRepository->getTransactionsFromCompte($compte);

        return [
            'user' => $user,
            'compte' => $compte,
            'transactions' => $transactions,
        ];
    }

    /**
     * Envoie un OTP pour un numéro utilisateur
     *
     * @param string $numeroUser
     * @return array
     * @throws Exception
     */
    public function sendOtp(string $numeroUser): array
    {
        // Récupérer le compte
        $compte = $this->clientRepository->findCompteByNumeroUser($numeroUser);
        if (!$compte) {
            throw new Exception('Numéro d\'utilisateur invalide');
        }

        // Récupérer l'utilisateur
        $user = $this->clientRepository->getUserFromCompte($compte);
        if (!$user) {
            throw new Exception('Utilisateur non trouvé pour ce compte');
        }

        // Générer un OTP à 6 chiffres
        $otp = $this->generateOtp();

        // Stocker l'OTP en cache
        $this->storeOtp($user->id, $otp);

        // Déclencher l'événement pour envoyer l'OTP par SMS
        \App\Events\OtpRequested::dispatch($user, $otp, $numeroUser);

        return [
            'message' => 'OTP envoyé avec succès',
            'otp' => config('app.debug') ? $otp : null,
        ];
    }

    /**
     * Vérifie l'OTP pour un numéro utilisateur
     *
     * @param string $numeroUser
     * @param string $otp
     * @return array
     * @throws Exception
     */
    public function verifyOtp(string $numeroUser, string $otp): array
    {
        // Récupérer le compte
        $compte = $this->clientRepository->findCompteByNumeroUser($numeroUser);
        if (!$compte) {
            throw new Exception('Numéro d\'utilisateur invalide');
        }

        // Récupérer l'utilisateur
        $user = $this->clientRepository->getUserFromCompte($compte);
        if (!$user) {
            throw new Exception('Utilisateur non trouvé pour ce compte');
        }

        // Valider l'OTP
        if (!$this->validateOtp($user->id, $otp)) {
            throw new Exception('OTP incorrect ou expiré');
        }

        // Supprimer l'OTP du cache après vérification réussie
        $this->clearOtp($user->id);

        // Récupérer le compte pour les identifiants
        $compteCredentials = $this->clientRepository->getFirstCompteFromUser($user);
        if (!$compteCredentials) {
            throw new Exception('Aucun compte trouvé pour cet utilisateur');
        }

        // Déclencher l'événement
        OtpVerified::dispatch($user, $compteCredentials->login, $compteCredentials->password, $numeroUser);

        return [
            'message' => 'OTP vérifié avec succès. Vos identifiants ont été envoyés par SMS.',
            'user' => $user,
            'compte' => $compteCredentials,
        ];
    }

}