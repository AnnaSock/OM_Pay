<?php

namespace App\Http\Controllers\Api\V1;

/**
 * @OA\Schema(
 *     schema="User",
 *     type="object",
 *     title="User",
 *     description="Schéma de l'utilisateur",
 *     @OA\Property(property="id", type="string", example="uuid-string", description="ID unique de l'utilisateur"),
 *     @OA\Property(property="nom", type="string", example="Dupont", description="Nom de l'utilisateur"),
 *     @OA\Property(property="prenom", type="string", example="Jean", description="Prénom de l'utilisateur"),
 *     @OA\Property(property="email", type="string", format="email", example="jean.dupont@email.com", description="Email de l'utilisateur"),
 *     @OA\Property(property="role", type="string", enum={"client", "marchand"}, example="client", description="Rôle de l'utilisateur"),
 *     @OA\Property(property="adresse", type="string", example="123 Rue de la Paix, Dakar", description="Adresse de l'utilisateur"),
 *     @OA\Property(property="nci", type="string", example="1234567890123", description="Numéro de carte d'identité"),
 *     @OA\Property(property="email_verified_at", type="string", format="date-time", nullable=true, description="Date de vérification email"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2024-01-15 10:30:00"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2024-01-15 10:30:00")
 * )
 *
 * @OA\Schema(
 *     schema="Compte",
 *     type="object",
 *     title="Compte Orange Money",
 *     description="Schéma du compte Orange Money avec génération automatique des identifiants",
 *     @OA\Property(property="id", type="string", format="uuid", example="550e8400-e29b-41d4-a716-446655440000", description="ID unique du compte (UUID généré automatiquement)"),
 *     @OA\Property(property="numero_compte", type="string", minLength=10, maxLength=10, example="7712345678", description="Numéro de compte Orange Money généré automatiquement (10 chiffres)"),
 *     @OA\Property(property="numero_user", type="string", example="771234567", description="Numéro d'utilisateur associé"),
 *     @OA\Property(property="code_marchand", type="string", nullable=true, example="M01234567", description="Code marchand généré automatiquement (null pour les clients)"),
 *     @OA\Property(property="login", type="string", example="USER12345", description="Login généré automatiquement (format USERxxxxx)"),
 *     @OA\Property(property="solde", type="number", format="float", example=1500.50, description="Solde actuel du compte Orange Money"),
 *     @OA\Property(property="user_type", type="string", example="App\\Models\\Client", description="Type d'utilisateur"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2024-01-15 10:30:00"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2024-01-15 10:30:00"),
 *     @OA\Property(property="transactions", type="array", @OA\Items(ref="#/components/schemas/Transaction"), description="Transactions du compte (conditionnel)"),
 *     @OA\Property(property="user", ref="#/components/schemas/User", description="Utilisateur propriétaire (conditionnel)")
 * )
 *
 * @OA\Schema(
 *     schema="Transaction",
 *     type="object",
 *     title="Transaction",
 *     description="Schéma d'une transaction",
 *     @OA\Property(property="id", type="string", example="uuid-string", description="ID unique de la transaction"),
 *     @OA\Property(property="montant", type="number", format="float", example=500.00, description="Montant de la transaction"),
 *     @OA\Property(property="type_transaction", type="string", enum={"Depot", "Retrait", "Payement"}, example="Depot", description="Type de transaction"),
 *     @OA\Property(property="compte_id", type="string", example="uuid-string", description="ID du compte associé"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2024-01-15 10:30:00"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2024-01-15 10:30:00"),
 *     @OA\Property(property="compte", ref="#/components/schemas/Compte", description="Compte associé (conditionnel)")
 * )
 */
class SwaggerSchemas
{
    // Cette classe ne contient que des annotations Swagger
    // Elle n'a pas besoin de méthodes
}