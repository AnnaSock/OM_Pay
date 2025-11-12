<?php

namespace App\Http\Requests;

use App\Enums\ValidationMessages;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class LoginRequest extends FormRequest
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
            'login' => 'required|string|max:255',
            'password' => 'required|string|min:8',
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
            'login.required' => ValidationMessages::REQUIRED_NOM->getMessage(),
            'login.string' => ValidationMessages::STRING_INVALID->getMessage(),
            'login.max' => ValidationMessages::MAX_255->getMessage(),
            'password.required' => ValidationMessages::REQUIRED_MOT_DE_PASSE->getMessage(),
            'password.string' => ValidationMessages::STRING_INVALID->getMessage(),
            'password.min' => ValidationMessages::MIN_8->getMessage(),
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
            'login' => 'nom d\'utilisateur',
            'password' => 'mot de passe',
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