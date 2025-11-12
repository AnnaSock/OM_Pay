<?php

namespace App\Http\Requests;

use App\Enums\ValidationMessages;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class SendOtpRequest extends FormRequest
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
            'numero_user' => 'required|string|regex:/^[0-9+\-\s]+$/|max:20',
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
            'numero_user.required' => ValidationMessages::REQUIRED_NUMERO_USER->getMessage(),
            'numero_user.string' => ValidationMessages::STRING_NUMERO_USER->getMessage(),
            'numero_user.regex' => ValidationMessages::REGEX_NUMERO_USER->getMessage(),
            'numero_user.max' => ValidationMessages::MAX_NUMERO_USER->getMessage(),
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
            'numero_user' => 'numéro d\'utilisateur',
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