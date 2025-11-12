<?php

namespace App\Enums;

enum ValidationMessages: string
{
    // Messages pour les champs requis
    case REQUIRED_NOM = 'Le nom est obligatoire.';
    case REQUIRED_PRENOM = 'Le prénom est obligatoire.';
    case REQUIRED_TELEPHONE = 'Le numéro de téléphone est obligatoire.';
    case REQUIRED_EMAIL = 'L\'adresse email est obligatoire.';
    case REQUIRED_MOT_DE_PASSE = 'Le mot de passe est obligatoire.';
    case REQUIRED_ROLE = 'Le rôle est obligatoire.';
    case REQUIRED_NUMERO_COMPTE = 'Le numéro de compte est obligatoire.';
    case REQUIRED_DATE_CREATION = 'La date de création est obligatoire.';
    case REQUIRED_STATUT_COMPTE = 'Le statut du compte est obligatoire.';
    case REQUIRED_TYPE_COMPTE = 'Le type de compte est obligatoire.';
    case REQUIRED_USER_ID = 'L\'identifiant utilisateur est obligatoire.';
    case REQUIRED_MONTANT = 'Le montant est obligatoire.';
    case REQUIRED_DATE = 'La date est obligatoire.';
    case REQUIRED_TYPE_TRANSACTION = 'Le type de transaction est obligatoire.';
    case REQUIRED_STATUT_TRANSACTION = 'Le statut de la transaction est obligatoire.';
    case REQUIRED_COMPTE_ID = 'L\'identifiant du compte est obligatoire.';
    case REQUIRED_NCI = 'Le numéro CNI est obligatoire.';

    // Messages pour les formats
    case EMAIL_INVALID = 'L\'adresse email doit être valide.';
    case STRING_INVALID = 'Ce champ doit être une chaîne de caractères.';
    case DATE_INVALID = 'La date doit être au format valide.';
    case DECIMAL_INVALID = 'Le montant doit être un nombre décimal valide.';
    case INTEGER_INVALID = 'Ce champ doit être un nombre entier.';

    // Messages pour les longueurs
    case MAX_255 = 'Ce champ ne peut pas dépasser 255 caractères.';
    case MAX_500 = 'Ce champ ne peut pas dépasser 500 caractères.';
    case MAX_20 = 'Ce champ ne peut pas dépasser 20 caractères.';
    case MIN_8 = 'Ce champ doit contenir au moins 8 caractères.';

    // Messages pour les contraintes d'unicité
    case UNIQUE_TELEPHONE = 'Ce numéro de téléphone est déjà utilisé.';
    case UNIQUE_EMAIL = 'Cette adresse email est déjà utilisée.';
    case UNIQUE_NUMERO_COMPTE = 'Ce numéro de compte est déjà utilisé.';

    // Messages pour les valeurs autorisées
    case IN_ROLE = 'Le rôle doit être admin ou client.';
    case IN_STATUT_COMPTE = 'Le statut du compte n\'est pas valide.';
    case IN_TYPE_COMPTE = 'Le type de compte n\'est pas valide.';
    case IN_TYPE_TRANSACTION = 'Le type de transaction n\'est pas valide.';
    case IN_STATUT_TRANSACTION = 'Le statut de la transaction n\'est pas valide.';
    case IN_DEVISE = 'La devise n\'est pas valide.';

    // Messages pour les relations
    case EXISTS_USER = 'L\'utilisateur spécifié n\'existe pas.';
    case EXISTS_COMPTE = 'Le compte spécifié n\'existe pas.';

    // Messages pour les montants
    case MONTANT_MIN = 'Le montant doit être supérieur à 0.';
    case MONTANT_MAX = 'Le montant ne peut pas dépasser 999999999999.99.';

    // Messages spécifiques aux comptes
    case SOLDE_INITIAL_REQUIRED = 'Le solde initial est obligatoire.';
    case SOLDE_INITIAL_NUMERIC = 'Le solde initial doit être un nombre.';
    case SOLDE_INITIAL_MIN = 'Le solde initial doit être d\'au moins 10 000 FCFA.';
    case DEVISE_REQUIRED = 'La devise est obligatoire.';
    case CLIENT_REQUIRED = 'Les informations du client sont obligatoires.';
    case CLIENT_ARRAY = 'Les informations du client doivent être un objet.';
    case TITULAIRE_REQUIRED_IF = 'Le nom du titulaire est obligatoire pour un nouveau client.';
    case CNI_REGEX = 'Le numéro CNI doit être au format sénégalais valide (13 chiffres).';
    case TELEPHONE_SENEGALAIS_INVALID = 'Le numéro de téléphone doit être au format sénégalais valide (+221 7X XXX XX XX ou +221 76 XXX XX XX).';
    case ADRESSE_REQUIRED = 'L\'adresse est obligatoire.';

    // Messages pour l'OTP
    case REQUIRED_NUMERO_USER = 'Le numéro d\'utilisateur est obligatoire.';
    case STRING_NUMERO_USER = 'Le numéro d\'utilisateur doit être une chaîne de caractères.';
    case REGEX_NUMERO_USER = 'Le format du numéro d\'utilisateur est invalide.';
    case MAX_NUMERO_USER = 'Le numéro d\'utilisateur ne peut pas dépasser 20 caractères.';
    case REQUIRED_OTP = 'Le code OTP est obligatoire.';
    case STRING_OTP = 'Le code OTP doit être une chaîne de caractères.';
    case SIZE_OTP = 'Le code OTP doit contenir exactement 6 chiffres.';
    case REGEX_OTP = 'Le code OTP ne peut contenir que des chiffres.';

    public function getMessage(): string
    {
        return $this->value;
    }
}