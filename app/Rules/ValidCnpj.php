<?php

declare(strict_types=1);

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidCnpj implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $cnpj = preg_replace('/\D/', '', (string) $value);

        if (! self::isValid($cnpj)) {
            $fail('O :attribute informado não é um CNPJ válido.');
        }
    }

    public static function isValid(string $cnpj): bool
    {
        if (strlen($cnpj) !== 14 || preg_match('/^(\d)\1{13}$/', $cnpj)) {
            return false;
        }

        $weights = [6, 5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];

        for ($position = 12; $position <= 13; $position++) {
            $sum = 0;
            $offset = $position === 12 ? 1 : 0;

            for ($i = 0; $i < $position; $i++) {
                $sum += (int) $cnpj[$i] * $weights[$i + $offset];
            }

            $remainder = $sum % 11;
            $digit = $remainder < 2 ? 0 : 11 - $remainder;

            if ((int) $cnpj[$position] !== $digit) {
                return false;
            }
        }

        return true;
    }
}
