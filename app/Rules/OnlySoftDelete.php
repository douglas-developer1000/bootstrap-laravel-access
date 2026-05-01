<?php

declare(strict_types=1);

namespace App\Rules;

use App\Models\User;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

final class OnlySoftDelete implements ValidationRule
{
    public function __construct(protected string $target)
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
        $item = User::onlyTrashed()->find($value);

        if ($item && empty($item->deleted_at)) {
            $fail("{$this->target} inválida");
        }
    }
}
