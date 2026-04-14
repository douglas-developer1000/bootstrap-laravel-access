<?php

namespace App\Rules\Password;

use App\Rules\Password\Contracts\RuleHandlerInterface;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Closure;

final class PasswordValid implements ValidationRule
{
    /** @var Collection<RuleHandlerInterface> $ruleHandlers */
    protected Collection $ruleHandlers;

    public function __construct(RuleHandlerInterface ...$ruleHandlers)
    {
        $this->ruleHandlers = collect($ruleHandlers);
        $this->ruleHandlers->each(function (RuleHandlerInterface $rule, int $key) {
            $rule->setNext($this->ruleHandlers->get($key + 1));
        });
    }

    /**
     * {@inheritDoc}
     * @param  Closure(string, ?string=): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $value = Str::of($value)->trim();
        $this->ruleHandlers->first()->handle($value, $fail);
    }
}
