<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateCompteRequest;
use App\Http\Services\CompteService;
use App\Http\Resources\CompteResource;
use App\Http\Resources\TransactionResource;
use Illuminate\Http\JsonResponse;
use Exception;

/**
 * @OA\Tag(
 *     name="Comptes",
 *     description="Gestion des comptes utilisateur"
 * )
 */

class CompteController extends Controller
{
    protected CompteService $compteService;

    public function __construct(CompteService $compteService)
    {
        $this->compteService = $compteService;
    }

    /**
     * @OA\Post(
     *     path="/api/comptes",
     *     summary="Créer un compte utilisateur avec dépôt initial",
     *     description="Crée un nouvel utilisateur (ou récupère un existant via NCI), génère automatiquement un numéro de compte, login et code marchand, puis effectue un dépôt initial",
     *     operationId="createCompte",
     *     tags={"Comptes"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"nom","prenom","adresse","nci","email","numero_user","password","montant_initial"},
     *             @OA\Property(property="nom", type="string", maxLength=255, example="Doe", description="Nom de l'utilisateur"),
     *             @OA\Property(property="prenom", type="string", maxLength=255, example="John", description="Prénom de l'utilisateur"),
     *             @OA\Property(property="adresse", type="string", maxLength=255, example="123 Rue de la Paix, Dakar", description="Adresse de l'utilisateur"),
     *             @OA\Property(property="nci", type="string", maxLength=255, example="1234567890123", description="Numéro de carte d'identité nationale (unique)"),
     *             @OA\Property(property="email", type="string", format="email", maxLength=255, example="john.doe@example.com", description="Adresse email de l'utilisateur"),
     *             @OA\Property(property="numero_user", type="string", maxLength=255, example="771234567", description="Numéro d'utilisateur unique"),
     *             @OA\Property(property="password", type="string", minLength=8, example="password123", description="Mot de passe (sera hashé automatiquement)"),
     *             @OA\Property(property="montant_initial", type="number", format="float", minimum=0, example=1000.00, description="Montant du dépôt initial")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Compte créé avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Compte créé avec succès"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="compte", type="object",
     *                     @OA\Property(property="id", type="string", format="uuid", example="550e8400-e29b-41d4-a716-446655440000"),
     *                     @OA\Property(property="numero_compte", type="string", example="1234567890", description="Numéro de compte généré automatiquement (10 chiffres)"),
     *                     @OA\Property(property="numero_user", type="string", example="771234567"),
     *                     @OA\Property(property="login", type="string", example="USER12345", description="Login généré automatiquement"),
     *                     @OA\Property(property="code_marchand", type="string", nullable=true, example="M01234567", description="Code marchand généré (null pour les clients)"),
     *                     @OA\Property(property="solde", type="number", format="float", example=1000.00),
     *                     @OA\Property(property="user", type="object",
     *                         @OA\Property(property="id", type="string", format="uuid"),
     *                         @OA\Property(property="nom", type="string"),
     *                         @OA\Property(property="prenom", type="string"),
     *                         @OA\Property(property="email", type="string")
     *                     )
     *                 ),
     *                 @OA\Property(property="transaction_depot", type="object",
     *                     @OA\Property(property="id", type="string", format="uuid"),
     *                     @OA\Property(property="montant", type="number", format="float", example=1000.00),
     *                     @OA\Property(property="type_transaction", type="string", example="Depot"),
     *                     @OA\Property(property="compte_id", type="string", format="uuid")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Erreur de validation des données d'entrée",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Erreur de validation"),
     *             @OA\Property(property="errors", type="object",
     *                 @OA\Property(property="nom", type="array", @OA\Items(type="string", example="Le nom est obligatoire.")),
     *                 @OA\Property(property="email", type="array", @OA\Items(type="string", example="Cette adresse email est déjà utilisée."))
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erreur interne du serveur",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Erreur lors de la création du compte"),
     *             @OA\Property(property="error", type="string", example="Détails de l'erreur")
     *         )
     *     )
     * )
     */
    public function store(CreateCompteRequest $request): JsonResponse
    {
        try {
            // Extraire les données de l'utilisateur
            $userData = $request->only(['nom', 'prenom', 'adresse', 'nci', 'email', 'numero_user', 'password']);

            // Créer l'utilisateur, le compte et la transaction
            $compte = $this->compteService->createUserCompteWithInitialDeposit(
                $userData,
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