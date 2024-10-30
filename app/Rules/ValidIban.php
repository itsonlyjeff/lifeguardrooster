<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Lang;

class ValidIban implements ValidationRule
{
    protected static $countryRules;

    protected $validator;
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!$this->checkIBAN($value))
            $this->error($fail);
    }

    public function setValidator($validator): void
    {
        $this->validator = $validator;
    }

    protected function checkIBAN($iban): bool
    {
        // Check if IBAN contains white space or special characters
        if (preg_match('/\s|[\'^£$%&*()}{@#~?<>,|=_+¬-]/', $iban))
            return false;

        $countryRules = $this->getCountryRules();

        $countryCode = substr($iban, 0, 2);
        $countryObj = $countryRules['sepa'][$countryCode] ?? $countryRules['not_sepa'][$countryCode] ?? null;

        if ($countryObj === null)
            return false;

        // Get validation rules
        $rules = array_map(fn($attr) => $attr[1], $countryObj);

        // Validate IBAN against rules
        $tempIban = $iban;
        $ibanLength = 0;

        foreach ($rules as $rule) {
            $numbers = intval(preg_replace('/[^0-9]/', '', $rule));
            $letter = preg_replace('/[^a-zA-Z]/', '', $rule);
            $checkString = substr($tempIban, 0, $numbers);
            $ibanLength += $numbers;

            // Check if the string part is of the correct type
            if (($letter === 'a' && !ctype_alpha($checkString)) || ($letter === 'n' && !ctype_digit($checkString)))
                return false;

            $tempIban = substr($tempIban, $numbers);
        }

        return $ibanLength == strlen($iban);
    }

    protected function getCountryRules(): array
    {
        if (self::$countryRules === null) {
            self::$countryRules = json_decode(
                file_get_contents(
                    resource_path('json/iban-countries.json')
                ),
                true
            );
        }

        return self::$countryRules;
    }

    protected function error(Closure $fail)
    {
        $this->validator && $this->validator->errors();

        return $fail(
            (!class_exists('Lang') || !Lang::has('validation.iban')) ?
                'Dit is geen geldige Iban.'
                : Lang::get('validation.iban')
        );
    }
}
