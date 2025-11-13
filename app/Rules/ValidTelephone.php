<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidTelephone implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     * @return void
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $pattern = '/^\+221\s(77|78|76|70|75|33)\s\d{3}\s\d{2}\s\d{2}$/';

        if (!preg_match($pattern, $value)) {
            $fail("Le numéro de téléphone :attribute n'est pas un numéro sénégalais valide.");
        }
    }
}
