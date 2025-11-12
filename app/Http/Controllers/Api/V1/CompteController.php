<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateCompteRequest;
use App\Http\Services\CompteService;
use App\Http\Resources\CompteResource;
use App\Http\Resources\TransactionResource;
use Illuminate\Http\JsonResponse;
use Exception;

class CompteController extends Controller
{
    protected CompteService $compteService;

    public function __construct(CompteService $compteService)
    {
        $this->compteService = $compteService;
    }

    /**
     * Créer un compte utilisateur avec dépôt initial
     *
     * @param CreateCompteRequest $request
     * @return JsonResponse
     */
    public function store(CreateCompteRequest $request): JsonResponse
    {
        try {
            // Extraire les données de l'utilisateur
            $userData = $request->only(['nom', 'prenom', 'adresse', 'nci', 'email', 'numero_user', 'login', 'password', 'code_marchand']);

            // Créer l'utilisateur, le compte et la transaction
            $compte = $this->compteService->createUserCompteWithInitialDeposit(
                $userData,
                $request->numero_compte,
                $request->montant_initial
            );

            // Charger les relations nécessaires
            $compte->load(['user', 'transactions']);

            return response()->json([
                'success' => true,
                'message' => 'Compte créé avec succès',
                'data' => [
                    'compte' => new CompteResource($compte),
                    'transaction_depot' => new TransactionResource($compte->transactions->first()),
                ]
            ], 201);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la création du compte',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}