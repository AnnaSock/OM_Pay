<?php

namespace App\Http\Requests;

use App\Enums\ValidationMessages;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class CreateCompteRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'adresse' => 'required|string|max:255',
            'nci' => 'required|string|max:255|unique:users,nci',
            'email' => 'required|email|max:255|unique:users,email',
            'numero_user' => 'required|string|max:255|unique:comptes,numero_user',
            'numero_compte' => 'required|string|max:255|unique:comptes,numero_compte',
            'login' => 'required|string|max:255|unique:comptes,login',
            'password' => 'required|string|min:8',
            'code_marchand' => 'nullable|string|max:255',
            'montant_initial' => 'required|numeric|min:0',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'nom.required' => ValidationMessages::REQUIRED_NOM->getMessage(),
            'nom.string' => ValidationMessages::STRING_INVALID->getMessage(),
            'nom.max' => ValidationMessages::MAX_255->getMessage(),
            'prenom.required' => ValidationMessages::REQUIRED_PRENOM->getMessage(),
            'prenom.string' => ValidationMessages::STRING_INVALID->getMessage(),
            'prenom.max' => ValidationMessages::MAX_255->getMessage(),
            'adresse.required' => ValidationMessages::ADRESSE_REQUIRED->getMessage(),
            'adresse.string' => ValidationMessages::STRING_INVALID->getMessage(),
            'adresse.max' => ValidationMessages::MAX_255->getMessage(),
            'nci.required' => ValidationMessages::REQUIRED_NCI->getMessage(),
            'nci.string' => ValidationMessages::STRING_INVALID->getMessage(),
            'nci.max' => ValidationMessages::MAX_255->getMessage(),
            'nci.unique' => 'Ce numéro CNI est déjà utilisé',
            'email.required' => ValidationMessages::REQUIRED_EMAIL->getMessage(),
            'email.email' => ValidationMessages::EMAIL_INVALID->getMessage(),
            'email.max' => ValidationMessages::MAX_255->getMessage(),
            'email.unique' => ValidationMessages::UNIQUE_EMAIL->getMessage(),
            'numero_user.required' => ValidationMessages::REQUIRED_NUMERO_USER->getMessage(),
            'numero_user.string' => ValidationMessages::STRING_NUMERO_USER->getMessage(),
            'numero_user.max' => ValidationMessages::MAX_NUMERO_USER->getMessage(),
            'numero_user.unique' => 'Ce numéro utilisateur existe déjà',
            'numero_compte.required' => ValidationMessages::REQUIRED_NUMERO_COMPTE->getMessage(),
            'numero_compte.string' => ValidationMessages::STRING_INVALID->getMessage(),
            'numero_compte.max' => ValidationMessages::MAX_255->getMessage(),
            'numero_compte.unique' => ValidationMessages::UNIQUE_NUMERO_COMPTE->getMessage(),
            'login.required' => ValidationMessages::REQUIRED_NOM->getMessage(),
            'login.string' => ValidationMessages::STRING_INVALID->getMessage(),
            'login.max' => ValidationMessages::MAX_255->getMessage(),
            'login.unique' => 'Ce login existe déjà',
            'password.required' => ValidationMessages::REQUIRED_MOT_DE_PASSE->getMessage(),
            'password.string' => ValidationMessages::STRING_INVALID->getMessage(),
            'password.min' => ValidationMessages::MIN_8->getMessage(),
            'code_marchand.string' => ValidationMessages::STRING_INVALID->getMessage(),
            'code_marchand.max' => ValidationMessages::MAX_255->getMessage(),
            'montant_initial.required' => ValidationMessages::REQUIRED_MONTANT->getMessage(),
            'montant_initial.numeric' => ValidationMessages::DECIMAL_INVALID->getMessage(),
            'montant_initial.min' => ValidationMessages::MONTANT_MIN->getMessage(),
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'nom' => 'nom',
            'prenom' => 'prénom',
            'adresse' => 'adresse',
            'nci' => 'numéro de carte d\'identité',
            'email' => 'adresse email',
            'numero_user' => 'numéro utilisateur',
            'numero_compte' => 'numéro de compte',
            'login' => 'nom d\'utilisateur',
            'password' => 'mot de passe',
            'code_marchand' => 'code marchand',
            'montant_initial' => 'montant initial',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'message' => 'Erreur de validation',
                'errors' => $validator->errors()
            ], 422)
        );
    }
}