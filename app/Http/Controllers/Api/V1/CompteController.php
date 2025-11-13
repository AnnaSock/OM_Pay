<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\HttpStatusCodes;
use App\Enums\ResponseMessages;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateCompteRequest;
use App\Http\Resources\TransactionCollection;
use App\Http\Services\CompteService;
use App\Http\Services\TransactionService;
use App\Models\Compte;
use Illuminate\Http\JsonResponse;
use Exception;

/**
 * @OA\Tag(
 *     name="Comptes Orange Money",
 *     description="Gestion des comptes Orange Money"
 * )
 */

class CompteController extends Controller
{
    protected CompteService $compteService;
    protected TransactionService $transactionService;

    public function __construct(CompteService $compteService, TransactionService $transactionService)
    {
        $this->compteService = $compteService;
        $this->transactionService = $transactionService;
    }

    /**
     * @OA\Post(
     *     path="/api/comptes",
     *     summary="Créer un compte utilisateur avec dépôt initial",
     *     description="Crée un nouvel utilisateur (ou récupère un existant via NCI), génère automatiquement un numéro de compte Orange Money, login et code marchand, puis effectue un dépôt initial",
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
     *             @OA\Property(property="message", type="string", example="Compte créé avec succès")
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
     *             @OA\Property(property="message", type="string", example="Erreur interne du serveur"),
     *             @OA\Property(property="error", type="string", example="Détails de l'erreur")
     *         )
     *     )
     * )
     */
    public function store(CreateCompteRequest $request): JsonResponse
    {
        try {
            // Récupérer les données validées
            $validated = $request->validated();

            // Extraire les données de l'utilisateur
            $userData = [
                'nom' => $validated['nom'],
                'prenom' => $validated['prenom'],
                'adresse' => $validated['adresse'],
                'nci' => $validated['nci'],
                'email' => $validated['email'],
                'numero_user' => $validated['numero_user'],
                'password' => $validated['password'],
            ];

            // Créer l'utilisateur, le compte et la transaction
            $this->compteService->createUserCompteWithInitialDeposit(
                $userData,
                $validated['montant_initial']
            );

            return response()->json([
                'success' => true,
                'message' => ResponseMessages::COMPTE_CREE->value
            ], HttpStatusCodes::CREATED->value);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => ResponseMessages::SERVER_ERROR->value,
                'error' => $e->getMessage()
            ], HttpStatusCodes::INTERNAL_SERVER_ERROR->value);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/{numero-compte}/solde",
     *     summary="Consulter le solde d'un compte",
     *     description="Récupère le solde actuel d'un compte Orange Money. L'utilisateur doit être authentifié et propriétaire du compte.",
     *     operationId="getSolde",
     *     tags={"Comptes"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="numero-compte",
     *         in="path",
     *         required=true,
     *         description="Numéro du compte bancaire",
     *         @OA\Schema(type="string", example="1234567890")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Solde récupéré avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Solde récupéré avec succès"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="compte_id", type="string", format="uuid", example="550e8400-e29b-41d4-a716-446655440000"),
     *                 @OA\Property(property="solde", type="number", format="float", example=1500.50)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Accès refusé - L'utilisateur n'est pas propriétaire du compte",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Accès non autorisé")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Compte non trouvé",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Compte non trouvé")
     *         )
     *     )
     * )
     */
    public function getSolde(string $numeroCompte): JsonResponse
    {
        try {
            // Récupérer l'utilisateur authentifié (middleware auth:api garantit l'authentification)
            $user = auth()->user();

            // Récupérer le compte par numéro
            $compte =Compte::byNumeroCompte($numeroCompte)->first();

            // Vérifier si le compte existe
            if (!$compte) {
                return response()->json([
                    'success' => false,
                    'message' => ResponseMessages::COMPTE_NON_TROUVE->value
                ], HttpStatusCodes::NOT_FOUND->value);
            }

            // Vérifier que l'utilisateur connecté est propriétaire du compte
            if ($compte->user_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => ResponseMessages::UNAUTHORIZED->value
                ], HttpStatusCodes::FORBIDDEN->value);
            }

            // Retourner le solde
            return response()->json([
                'success' => true,
                'message' => 'Solde récupéré avec succès',
                'data' => [
                    'compte_id' => $compte->id,
                    'solde' => $compte->getSolde()
                ]
            ], HttpStatusCodes::OK->value);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => ResponseMessages::SERVER_ERROR->value,
                'error' => $e->getMessage()
            ], HttpStatusCodes::INTERNAL_SERVER_ERROR->value);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/{numero-compte}/transaction",
     *     summary="Récupérer les transactions d'un compte",
     *     description="Récupère les transactions associées à un compte Orange Money avec pagination et filtrage optionnel par type. L'utilisateur doit être authentifié et propriétaire du compte.",
     *     operationId="getTransactions",
     *     tags={"Transactions"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="numero-compte",
     *         in="path",
     *         required=true,
     *         description="Numéro du compte Orange Money",
     *         @OA\Schema(type="string", example="7712345678")
     *     ),
     *     @OA\Parameter(
     *         name="type_transaction",
     *         in="query",
     *         required=false,
     *         description="Filtrer par type de transaction",
     *         @OA\Schema(type="string", enum={"Depot", "Retrait", "Payement"}, example="Depot")
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         required=false,
     *         description="Numéro de la page",
     *         @OA\Schema(type="integer", minimum=1, example=1)
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         required=false,
     *         description="Nombre d'éléments par page",
     *         @OA\Schema(type="integer", minimum=1, maximum=100, example=15)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Transactions récupérées avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Transactions récupérées avec succès"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="transactions", type="object",
     *                     @OA\Property(property="data", type="array", @OA\Items(
     *                         @OA\Property(property="id", type="string", format="uuid", example="550e8400-e29b-41d4-a716-446655440000"),
     *                         @OA\Property(property="montant", type="number", format="float", example=500.00),
     *                         @OA\Property(property="montant_formate", type="string", example="+500.00", description="Montant avec signe (+ pour dépôt, - pour retrait/paiement)"),
     *                         @OA\Property(property="type_transaction", type="string", enum={"Depot", "Retrait", "Payement"}, example="Depot"),
     *                         @OA\Property(property="compte_id", type="string", format="uuid", example="550e8400-e29b-41d4-a716-446655440001"),
     *                         @OA\Property(property="created_at", type="string", format="date-time", example="2025-11-13 08:45:00"),
     *                         @OA\Property(property="updated_at", type="string", format="date-time", example="2025-11-13 08:45:00")
     *                     )),
     *                     @OA\Property(property="current_page", type="integer", example=1),
     *                     @OA\Property(property="first_page_url", type="string", example="http://localhost:8000/api/7712345678/transaction?page=1"),
     *                     @OA\Property(property="from", type="integer", example=1),
     *                     @OA\Property(property="last_page", type="integer", example=1),
     *                     @OA\Property(property="last_page_url", type="string", example="http://localhost:8000/api/7712345678/transaction?page=1"),
     *                     @OA\Property(property="next_page_url", type="string", nullable=true, example=null),
     *                     @OA\Property(property="path", type="string", example="http://localhost:8000/api/7712345678/transaction"),
     *                     @OA\Property(property="per_page", type="integer", example=15),
     *                     @OA\Property(property="prev_page_url", type="string", nullable=true, example=null),
     *                     @OA\Property(property="to", type="integer", example=3),
     *                     @OA\Property(property="total", type="integer", example=3),
     *                     @OA\Property(property="meta", type="object",
     *                         @OA\Property(property="total", type="integer", example=3),
     *                         @OA\Property(property="total_amount", type="number", format="float", example=1500.00)
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Accès refusé - L'utilisateur n'est pas propriétaire du compte",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Accès non autorisé")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Compte non trouvé",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Compte non trouvé")
     *         )
     *     )
     * )
     */
    public function getTransactions(string $numeroCompte, \Illuminate\Http\Request $request): JsonResponse
    {
        try {
            // Récupérer l'utilisateur authentifié
            $user = auth()->user();

            // Récupérer le compte par numéro
            $compte = Compte::byNumeroCompte($numeroCompte)->first();

            // Vérifier si le compte existe
            if (!$compte) {
                return response()->json([
                    'success' => false,
                    'message' => ResponseMessages::COMPTE_NON_TROUVE->value
                ], HttpStatusCodes::NOT_FOUND->value);
            }

            // Vérifier que l'utilisateur connecté est propriétaire du compte
            if ($compte->user_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => ResponseMessages::UNAUTHORIZED->value
                ], HttpStatusCodes::FORBIDDEN->value);
            }

            // Récupérer les paramètres de requête
            $typeTransaction = $request->query('type_transaction');
            $perPage = $request->query('per_page', 15);

            // Valider les paramètres
            if ($typeTransaction && !in_array($typeTransaction, ['Depot', 'Retrait', 'Payement'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Type de transaction invalide. Valeurs autorisées: Depot, Retrait, Payement'
                ], HttpStatusCodes::BAD_REQUEST->value);
            }

            if ($perPage < 1 || $perPage > 100) {
                return response()->json([
                    'success' => false,
                    'message' => 'Le paramètre per_page doit être entre 1 et 100'
                ], HttpStatusCodes::BAD_REQUEST->value);
            }

            // Récupérer les transactions via le service
            $transactions = $this->transactionService
                ->getTransactionsByComptePaginated($numeroCompte, $typeTransaction, $perPage);

            return response()->json([
                'success' => true,
                'message' => 'Transactions récupérées avec succès',
                'data' => [
                    'transactions' => new TransactionCollection($transactions)
                ]
            ], HttpStatusCodes::OK->value);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => ResponseMessages::SERVER_ERROR->value,
                'error' => $e->getMessage()
            ], HttpStatusCodes::INTERNAL_SERVER_ERROR->value);
        }
    }

}