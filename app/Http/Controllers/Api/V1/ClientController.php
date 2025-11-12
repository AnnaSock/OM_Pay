<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\HttpStatusCodes;
use App\Http\Controllers\Controller;
use App\Http\Requests\SendOtpRequest;
use App\Http\Requests\VerifyOtpRequest;
use App\Http\Resources\OtpResponseResource;
use App\Http\Resources\OtpVerificationResource;
use App\Http\Resources\UserInfoResource;
use App\Http\Services\ClientService;
use App\Models\User;
use App\Traits\ApiResponse;
use App\Traits\HandlesOtp;
use Exception;
use Illuminate\Http\Request;

/**
 * @OA\Tag(
 *     name="OTP",
 *     description="Gestion des codes OTP pour l'authentification"
 * )
 *
 * @OA\Tag(
 *     name="Utilisateur",
 *     description="Gestion des informations utilisateur"
 * )
 */
class ClientController extends Controller
{
    use ApiResponse, HandlesOtp;

    protected ClientService $clientService;

    public function __construct(ClientService $clientService)
    {
        $this->clientService = $clientService;
    }

    /**
     * @OA\Get(
     *     path="/api/me",
     *     summary="Informations de l'utilisateur connecté",
     *     description="Récupère les informations complètes de l'utilisateur authentifié avec son compte et ses transactions",
     *     operationId="getUserInfo",
     *     tags={"Utilisateur"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Informations utilisateur récupérées avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Informations utilisateur récupérées avec succès"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="user", ref="#/components/schemas/User"),
     *                 @OA\Property(property="compte", ref="#/components/schemas/Compte"),
     *                 @OA\Property(property="transactions", type="array", @OA\Items(ref="#/components/schemas/Transaction")),
     *                 @OA\Property(property="summary", type="object",
     *                     @OA\Property(property="total_transactions", type="integer", example=5),
     *                     @OA\Property(property="current_balance", type="number", format="float", example=1500.50),
     *                     @OA\Property(property="last_transaction_date", type="string", format="date-time")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Token d'authentification manquant ou invalide",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Unauthenticated")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Utilisateur ou compte non trouvé",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Aucun compte trouvé pour cet utilisateur")
     *         )
     *     )
     * )
     */
    public function me(Request $request)
    {
        try {
            $user = $request->user();
            $result = $this->clientService->getUserInfo($user);

            return $this->successResponse(
                new UserInfoResource($result),
                'Informations utilisateur récupérées avec succès'
            );
        } catch (Exception $e) {
            $statusCode = HttpStatusCodes::INTERNAL_SERVER_ERROR->value;

            // Déterminer le code de statut basé sur le message d'erreur
            if (str_contains($e->getMessage(), 'non trouvé')) {
                $statusCode = HttpStatusCodes::NOT_FOUND->value;
            }

            return $this->errorResponse(
                $e->getMessage(),
                $statusCode
            );
        }
    }

    /**
     * @OA\Post(
     *     path="/api/sendOtp",
     *     summary="Envoyer un code OTP",
     *     description="Génère et envoie un code OTP à 6 chiffres pour l'authentification de l'utilisateur",
     *     operationId="sendOtp",
     *     tags={"OTP"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"numero_user"},
     *             @OA\Property(property="numero_user", type="string", example="771234567", description="Numéro d'utilisateur pour recevoir l'OTP")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OTP envoyé avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="OTP généré et envoyé"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="message", type="string", example="OTP envoyé avec succès"),
     *                 @OA\Property(property="otp", type="string", example="123456", description="OTP affiché seulement en développement"),
     *                 @OA\Property(property="sent_at", type="string", format="date-time", example="2024-01-15 10:30:00")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Numéro d'utilisateur invalide",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Numéro d'utilisateur invalide")
     *         )
     *     )
     * )
     */
    public function sendOtp(SendOtpRequest $request)
    {
        /** @var SendOtpRequest $request */
        $numeroUser = $request->input('numero_user');

        try {
            $result = $this->clientService->sendOtp($numeroUser);

            return $this->successResponse(
                new OtpResponseResource($result),
                'OTP généré et envoyé'
            );
        } catch (Exception $e) {
            $statusCode = HttpStatusCodes::INTERNAL_SERVER_ERROR->value;

            // Déterminer le code de statut basé sur le message d'erreur
            if (str_contains($e->getMessage(), 'invalide') || str_contains($e->getMessage(), 'non trouvé')) {
                $statusCode = HttpStatusCodes::NOT_FOUND->value;
            }

            return $this->errorResponse(
                $e->getMessage(),
                $statusCode
            );
        }
    }

    /**
     * @OA\Post(
     *     path="/api/verifyOtp",
     *     summary="Vérifier un code OTP",
     *     description="Vérifie le code OTP fourni et retourne les informations de vérification",
     *     operationId="verifyOtp",
     *     tags={"OTP"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"numero_user","otp"},
     *             @OA\Property(property="numero_user", type="string", example="771234567", description="Numéro d'utilisateur"),
     *             @OA\Property(property="otp", type="string", example="123456", description="Code OTP à 6 chiffres")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OTP vérifié avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Vous pouvez vous connecter maintenant"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="message", type="string", example="OTP vérifié avec succès. Vos identifiants ont été envoyés par SMS."),
     *                 @OA\Property(property="verified_at", type="string", format="date-time", example="2024-01-15 10:30:00"),
     *                 @OA\Property(property="status", type="string", example="verified"),
     *                 @OA\Property(property="user_info", type="object",
     *                     @OA\Property(property="numero_user", type="string", example="771234567")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="OTP incorrect ou expiré",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="OTP incorrect ou expiré")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Numéro d'utilisateur invalide",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Numéro d'utilisateur invalide")
     *         )
     *     )
     * )
     */
    public function verifyOtp(VerifyOtpRequest $request)
    {
        /** @var VerifyOtpRequest $request */
        $numeroUser = $request->input('numero_user');
        $otp = $request->input('otp');

        try {
            $result = $this->clientService->verifyOtp($numeroUser, $otp);

            return $this->successResponse(
                new OtpVerificationResource($result),
                'Vous pouvez vous connecter maintenant'
            );
        } catch (Exception $e) {
            $statusCode = HttpStatusCodes::INTERNAL_SERVER_ERROR->value;

            // Déterminer le code de statut basé sur le message d'erreur
            if (str_contains($e->getMessage(), 'invalide') || str_contains($e->getMessage(), 'non trouvé')) {
                $statusCode = HttpStatusCodes::NOT_FOUND->value;
            } elseif (str_contains($e->getMessage(), 'OTP incorrect')) {
                $statusCode = HttpStatusCodes::UNAUTHORIZED->value;
            }

            return $this->errorResponse(
                $e->getMessage(),
                $statusCode
            );
        }
    }

}