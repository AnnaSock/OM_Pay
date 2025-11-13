<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\HttpStatusCodes;
use App\Enums\ResponseMessages;
use App\Http\Requests\LoginRequest;
use App\Models\Compte;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

/**
 * @OA\Tag(
 *     name="Authentification",
 *     description="Gestion de l'authentification des utilisateurs"
 * )
 */
class AuthController extends \App\Http\Controllers\Controller
{
    use ApiResponse;

    /**
     * @OA\Post(
     *     path="/api/login",
     *     summary="Authentification utilisateur",
     *     description="Authentifie un utilisateur avec login et mot de passe, retourne des tokens Bearer",
     *     operationId="login",
     *     tags={"Authentification"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"login","password"},
     *             @OA\Property(property="login", type="string", example="user123", description="Login de l'utilisateur"),
     *             @OA\Property(property="password", type="string", format="password", example="password123", description="Mot de passe")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Authentification réussie",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Authentification réussie"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="access_token", type="string", example="eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9..."),
     *                 @OA\Property(property="refresh_token", type="string", example="eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9..."),
     *                 @OA\Property(property="user_type", type="string", example="App\\Models\\Client")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Échec d'authentification",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Authentification échouée"),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
     */
    public function login(LoginRequest $request)
    {
        $validated = $request->validated();

        // Rechercher le compte par login
        $compte = Compte::byLogin($validated['login'])->first();

        if (!$compte) {
            return $this->errorResponse(
                ResponseMessages::AUTHENTIFICATION_ECHOUEE->value,
                HttpStatusCodes::UNAUTHORIZED->value,
                ['login' => 'Login invalide']
            );
        }

        // Vérifier le mot de passe
        if (!Hash::check($validated['password'], $compte->password)) {
            return $this->errorResponse(
                ResponseMessages::AUTHENTIFICATION_ECHOUEE->value,
                HttpStatusCodes::UNAUTHORIZED->value,
                ['password' => 'Mot de passe incorrect']
            );
        }

        // Authentifier l'utilisateur associé au compte
        $user = $compte->user;

        if (!$user) {
            return $this->errorResponse(
                ResponseMessages::AUTHENTIFICATION_ECHOUEE->value,
                HttpStatusCodes::UNAUTHORIZED->value,
                ['login' => 'Utilisateur non trouvé pour ce compte']
            );
        }

        // Générer les tokens OAuth avec Passport
        $token = $user->createToken('Personal Access Token')->accessToken;
        $refreshToken = $user->createToken('Refresh Token')->accessToken;

        // Retourner la réponse de succès avec les tokens dans la data et dans des cookies HTTP-only
        $response = $this->successResponse([
            'access_token' => $token,
            'refresh_token' => $refreshToken,
            'user_type' => get_class($user),
        ], ResponseMessages::AUTHENTIFICATION_REUSSIE->value);

        return $response->cookie('access_token', $token, 60, '/', null, true, true)
                        ->cookie('refresh_token', $refreshToken, 1440, '/', null, true, true);
    }

}
