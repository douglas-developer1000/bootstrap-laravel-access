<?php

namespace App\Rules;

use App\Models\RegisterApproval;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;
use Carbon\Carbon;
use Closure;

final class RegisterApprovalValid implements ValidationRule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct(
        protected ?RegisterApproval $allowed,
    ) {
        // ...
    }

    /**
     * Run the validation rule.
     *
     * @param  Closure(string, ?string=): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $token = $value;
        if ($this->allowed?->token !== $token) {
            $fail('Requisição inválida');
        } else if (Carbon::now()->greaterThan(Carbon::parse($this->allowed->expiration_data))) {
            $fail('Requisição expirada');
        }
    }
}
