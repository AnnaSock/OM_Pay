<?php

namespace App\Enums;

enum ResponseMessages: string
{
    // Messages de succès généraux
    case SUCCESS = 'Opération réalisée avec succès';
    case CREATED = 'Ressource créée avec succès';
    case UPDATED = 'Ressource mise à jour avec succès';
    case DELETED = 'Ressource supprimée avec succès';

    // Messages spécifiques aux comptes
    case COMPTES_RECUPERES = 'Comptes récupérés avec succès';
    case COMPTE_RECUPERE = 'Compte récupéré avec succès';
    case COMPTE_CREE = 'Compte créé avec succès';
    case COMPTE_MODIFIE = 'Compte modifié avec succès';
    case COMPTE_SUPPRIME = 'Compte supprimé avec succès';

    // Messages spécifiques aux transactions
    case TRANSACTIONS_RECUPEREES = 'Transactions récupérées avec succès';
    case TRANSACTION_RECUPEREE = 'Transaction récupérée avec succès';
    case TRANSACTION_CREE = 'Transaction créée avec succès';
    case TRANSACTION_MODIFIEE = 'Transaction modifiée avec succès';
    case TRANSACTION_SUPPRIMEE = 'Transaction supprimée avec succès';

    // Messages spécifiques aux utilisateurs
    case UTILISATEURS_RECUPERES = 'Utilisateurs récupérés avec succès';
    case UTILISATEUR_RECUPERE = 'Utilisateur récupéré avec succès';
    case UTILISATEUR_CREE = 'Utilisateur créé avec succès';
    case UTILISATEUR_MODIFIE = 'Utilisateur modifié avec succès';
    case UTILISATEUR_SUPPRIME = 'Utilisateur supprimé avec succès';

    // Messages d'erreur
    case NOT_FOUND = 'Ressource non trouvée';
    case UNAUTHORIZED = 'Accès non autorisé';
    case FORBIDDEN = 'Action interdite';
    case VALIDATION_ERROR = 'Erreur de validation';
    case SERVER_ERROR = 'Erreur interne du serveur';

    // Messages spécifiques aux comptes
    case COMPTE_NON_TROUVE = 'Compte non trouvé';
    case COMPTE_NON_ACTIF = 'Le compte n\'est pas actif';
    case TYPE_COMPTE_INVALIDE = 'Type de compte non valide pour cette opération';

    // Messages spécifiques aux transactions
    case TRANSACTION_NON_TROUVEE = 'Transaction non trouvée';
    case SOLDE_INSUFFISANT = 'Solde insuffisant pour cette opération';

    // Messages d'authentification
    case AUTHENTIFICATION_REUSSIE = 'Authentification réussie';
    case AUTHENTIFICATION_ECHOUEE = 'Échec de l\'authentification';
    case TOKEN_INVALIDE = 'Token d\'authentification invalide';
    case TOKEN_EXPIRE = 'Token d\'authentification expiré';
}