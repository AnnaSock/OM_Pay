<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\HttpStatusCodes;
use App\Enums\ResponseMessages;
use App\Http\Controllers\Controller;
use App\Http\Requests\TransfertRequest;
use App\Http\Requests\PaiementRequest;
use App\Http\Services\TransactionService;
use App\Models\Compte;
use Illuminate\Http\JsonResponse;
use Exception;

/**
 * @OA\Tag(
 *     name="Transactions",
 *     description="Gestion des transactions (transferts, retraits, dépôts)"
 * )
 */

class TransactionController extends Controller
{
    protected $transactionService;

    public function __construct(TransactionService $transactionService)
    {
        $this->transactionService = $transactionService;
    }

    /**
     * @OA\Post(
     *     path="/api/{numero_compte}/transfert",
     *     summary="Effectuer un transfert d'argent",
     *     description="Permet à un utilisateur authentifié d'effectuer un transfert d'argent vers un autre compte. L'utilisateur doit être propriétaire du compte émetteur.",
     *     operationId="transfert",
     *     tags={"Transactions"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="numero_compte",
     *         in="path",
     *         required=true,
     *         description="Numéro de compte de l'émetteur",
     *         @OA\Schema(type="string", example="1234567890")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"numero_user_destinataire","montant"},
     *             @OA\Property(property="numero_user_destinataire", type="string", maxLength=255, example="771234568", description="Numéro utilisateur du destinataire"),
     *             @OA\Property(property="montant", type="number", format="float", minimum=0.01, example=100.00, description="Montant à transférer")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Transfert effectué avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Transfert effectué avec succès")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Solde insuffisant ou transfert à soi-même",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Solde insuffisant")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Accès refusé - L'utilisateur n'est pas propriétaire du compte émetteur",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Accès non autorisé")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Compte émetteur ou destinataire non trouvé",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Compte non trouvé")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Erreur de validation des données d'entrée",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Erreur de validation"),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
     */
    public function transfert(string $numeroCompte, TransfertRequest $request): JsonResponse
    {
        try {
            // Récupérer l'utilisateur authentifié
            $user = auth()->user();

            // Récupérer le compte émetteur par numero_compte
            $compteEmetteur = Compte::byNumeroCompte($numeroCompte)->first();

            // Vérifier si le compte émetteur existe
            if (!$compteEmetteur) {
                return response()->json([
                    'success' => false,
                    'message' => ResponseMessages::COMPTE_NON_TROUVE->value
                ], HttpStatusCodes::NOT_FOUND->value);
            }

            // Vérifier que l'utilisateur connecté est propriétaire du compte émetteur
            if ($compteEmetteur->user_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => ResponseMessages::UNAUTHORIZED->value
                ], HttpStatusCodes::FORBIDDEN->value);
            }

            // Récupérer les données validées
            $validated = $request->validated();
            $numeroUserDestinataire = $validated['numero_user_destinataire'];
            $montant = $validated['montant'];

            // Récupérer le compte destinataire
            $compteDestinataire = Compte::byNumero($numeroUserDestinataire)->first();

            // Vérifier que le destinataire existe (bien que validé dans la requête, on revérifie)
            if (!$compteDestinataire) {
                return response()->json([
                    'success' => false,
                    'message' => 'Destinataire non trouvé'
                ], HttpStatusCodes::NOT_FOUND->value);
            }

            // Empêcher l'utilisateur de s'envoyer de l'argent à lui-même
            if ($compteEmetteur->id === $compteDestinataire->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Transfert à soi-même interdit'
                ], HttpStatusCodes::BAD_REQUEST->value);
            }

            // Vérifier que le montant demandé est disponible dans le solde actuel de l'émetteur
            if ($compteEmetteur->getSolde() < $montant) {
                return response()->json([
                    'success' => false,
                    'message' => 'Solde insuffisant'
                ], HttpStatusCodes::BAD_REQUEST->value);
            }

            // Effectuer le transfert
            $this->transactionService->performTransfer($compteEmetteur->id, $compteDestinataire->id, $montant);

            return response()->json([
                'success' => true,
                'message' => 'Transfert effectué avec succès'
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
     *     path="/api/{numero_compte}/paiement",
     *     summary="Effectuer un paiement vers un marchand",
     *     description="Permet à un utilisateur authentifié d'effectuer un paiement vers un marchand spécifique. L'utilisateur doit être propriétaire du compte émetteur.",
     *     operationId="paiement",
     *     tags={"Transactions"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="numero_compte",
     *         in="path",
     *         required=true,
     *         description="Numéro de compte de l'émetteur",
     *         @OA\Schema(type="string", example="1234567890")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"code_marchand","montant"},
     *             @OA\Property(property="code_marchand", type="string", maxLength=255, example="MARCHAND001", description="Code identifiant du marchand destinataire"),
     *             @OA\Property(property="montant", type="number", format="float", minimum=0.01, example=500.00, description="Montant du paiement")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Paiement effectué avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Paiement effectué avec succès")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Solde insuffisant ou paiement interdit",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Solde insuffisant")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Accès refusé - L'utilisateur n'est pas propriétaire du compte émetteur",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Accès non autorisé")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Compte émetteur ou marchand non trouvé",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Marchand non trouvé")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Erreur de validation des données d'entrée",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Erreur de validation"),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
     */
    public function paiement(string $numeroCompte, PaiementRequest $request): JsonResponse
    {
        try {
            // Récupérer l'utilisateur authentifié
            $user = auth()->user();

            // Récupérer le compte émetteur par numero_compte
            $compteEmetteur = Compte::byNumeroCompte($numeroCompte)->first();

            // Vérifier si le compte émetteur existe
            if (!$compteEmetteur) {
                return response()->json([
                    'success' => false,
                    'message' => ResponseMessages::COMPTE_NON_TROUVE->value
                ], HttpStatusCodes::NOT_FOUND->value);
            }

            // Vérifier que l'utilisateur connecté est propriétaire du compte émetteur
            if ($compteEmetteur->user_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => ResponseMessages::UNAUTHORIZED->value
                ], HttpStatusCodes::FORBIDDEN->value);
            }

            // Récupérer les données validées
            $validated = $request->validated();
            $codeMarchand = $validated['code_marchand'];
            $montant = $validated['montant'];

            // Récupérer le compte marchand par code_marchand
            $compteMarchand = Compte::byCodeMarchand($codeMarchand)->first();

            // Vérifier que le marchand existe (bien que validé dans la requête, on revérifie)
            if (!$compteMarchand) {
                return response()->json([
                    'success' => false,
                    'message' => 'Marchand non trouvé'
                ], HttpStatusCodes::NOT_FOUND->value);
            }

            // Empêcher l'utilisateur de se payer lui-même
            if ($compteEmetteur->id === $compteMarchand->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Paiement à soi-même interdit'
                ], HttpStatusCodes::BAD_REQUEST->value);
            }

            // Vérifier que le montant demandé est disponible dans le solde actuel de l'émetteur
            if ($compteEmetteur->getSolde() < $montant) {
                return response()->json([
                    'success' => false,
                    'message' => 'Solde insuffisant'
                ], HttpStatusCodes::BAD_REQUEST->value);
            }

            // Effectuer le paiement
            $this->transactionService->performPayment($compteEmetteur->id, $compteMarchand->id, $montant);

            return response()->json([
                'success' => true,
                'message' => 'Paiement effectué avec succès'
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