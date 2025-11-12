<?php

namespace App\Enums;

enum HttpStatusCodes: int
{
    // Codes de succès (2xx)
    case OK = 200;                    // Requête réussie (GET, PATCH, DELETE)
    case CREATED = 201;               // Ressource créée (POST)
    case NO_CONTENT = 204;            // Suppression réussie sans retour de données

    // Codes d'erreur client (4xx)
    case BAD_REQUEST = 400;           // Données invalides
    case UNAUTHORIZED = 401;          // Non authentifié
    case FORBIDDEN = 403;             // Non autorisé
    case NOT_FOUND = 404;             // Ressource inexistante
    case CONFLICT = 409;              // Conflit (ex: compte déjà existant)
    case UNPROCESSABLE_ENTITY = 422;  // Erreur de validation métier
    case TOO_MANY_REQUESTS = 429;     // Limite de débit dépassée

    // Codes d'erreur serveur (5xx)
    case INTERNAL_SERVER_ERROR = 500; // Erreur serveur
    case SERVICE_UNAVAILABLE = 503;   // Service temporairement indisponible

    /**
     * Retourne le nom lisible du code de statut
     */
    public function getName(): string
    {
        return match($this) {
            self::OK => 'OK',
            self::CREATED => 'Created',
            self::NO_CONTENT => 'No Content',
            self::BAD_REQUEST => 'Bad Request',
            self::UNAUTHORIZED => 'Unauthorized',
            self::FORBIDDEN => 'Forbidden',
            self::NOT_FOUND => 'Not Found',
            self::CONFLICT => 'Conflict',
            self::UNPROCESSABLE_ENTITY => 'Unprocessable Entity',
            self::TOO_MANY_REQUESTS => 'Too Many Requests',
            self::INTERNAL_SERVER_ERROR => 'Internal Server Error',
            self::SERVICE_UNAVAILABLE => 'Service Unavailable',
        };
    }

    /**
     * Retourne une description du code de statut
     */
    public function getDescription(): string
    {
        return match($this) {
            self::OK => 'Requête réussie (GET, PATCH, DELETE)',
            self::CREATED => 'Ressource créée (POST)',
            self::NO_CONTENT => 'Suppression réussie sans retour de données',
            self::BAD_REQUEST => 'Données invalides',
            self::UNAUTHORIZED => 'Non authentifié',
            self::FORBIDDEN => 'Non autorisé',
            self::NOT_FOUND => 'Ressource inexistante',
            self::CONFLICT => 'Conflit (ex: compte déjà existant)',
            self::UNPROCESSABLE_ENTITY => 'Erreur de validation métier',
            self::TOO_MANY_REQUESTS => 'Limite de débit dépassée',
            self::INTERNAL_SERVER_ERROR => 'Erreur serveur',
            self::SERVICE_UNAVAILABLE => 'Service temporairement indisponible',
        };
    }

    /**
     * Vérifie si le code est un code de succès (2xx)
     */
    public function isSuccess(): bool
    {
        return $this->value >= 200 && $this->value < 300;
    }

    /**
     * Vérifie si le code est un code d'erreur client (4xx)
     */
    public function isClientError(): bool
    {
        return $this->value >= 400 && $this->value < 500;
    }

    /**
     * Vérifie si le code est un code d'erreur serveur (5xx)
     */
    public function isServerError(): bool
    {
        return $this->value >= 500 && $this->value < 600;
    }
}