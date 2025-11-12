<?php

namespace App\Http\Controllers\Api\V1;

/**
 * @OA\Info(
 *     title="OM Pay API",
 *     description="API de paiement mobile OM Pay avec authentification OTP",
 *     version="1.0.0",
 *     @OA\Contact(
 *         email="support@ompay.com"
 *     )
 * )
 *
 * @OA\Server(
 *     url="http://localhost:8000",
 *     description="Serveur de développement"
 * )
 *
 * @OA\Server(
 *     url="https://api.ompay.com",
 *     description="Serveur de production"
 * )
 *
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT",
 *     description="Enter your Bearer token in the format: Bearer {token}"
 * )
 */
class ApiDocumentation
{
    // Cette classe ne contient que des annotations Swagger
    // Elle n'a pas besoin de méthodes
}