<?php

declare(strict_types=1);

namespace App\Rules;

use App\Libraries\Utils\PhoneFormatter;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;
use Illuminate\Support\Str;
use Closure;

final class PhoneValid implements ValidationRule
{
    public function __construct(protected readonly int $phoneMaxSize)
    {
        // ...
    }

    /**
     * Run the validation rule.
     *
     * @param  Closure(string, ?string=): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $phone = Str::of(PhoneFormatter::chopSeparators($value));
        if (!$phone->isMatch('|^\d+$|')) {
            $fail('Campo inválido');
        } else if ($phone->length() > $this->phoneMaxSize) {
            $fail("Tamanho máximo excedido ($this->phoneMaxSize)");
        }
    }
}
