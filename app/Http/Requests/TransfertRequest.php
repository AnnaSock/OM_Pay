<?php

namespace App\Http\Requests;

use App\Enums\ValidationMessages;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class TransfertRequest extends FormRequest
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
            'numero_user_destinataire' => 'required|string|max:255|exists:comptes,numero_user',
            'montant' => 'required|numeric|min:0.01',
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
            'numero_user_destinataire.required' => ValidationMessages::REQUIRED_NUMERO_USER->getMessage(),
            'numero_user_destinataire.string' => ValidationMessages::STRING_NUMERO_USER->getMessage(),
            'numero_user_destinataire.max' => ValidationMessages::MAX_NUMERO_USER->getMessage(),
            'numero_user_destinataire.exists' => 'Le destinataire n\'existe pas',
            'montant.required' => ValidationMessages::REQUIRED_MONTANT->getMessage(),
            'montant.numeric' => ValidationMessages::DECIMAL_INVALID->getMessage(),
            'montant.min' => ValidationMessages::MONTANT_MIN->getMessage(),
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
            'numero_user_destinataire' => 'numéro utilisateur destinataire',
            'montant' => 'montant',
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