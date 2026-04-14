<?php

declare(strict_types=1);

namespace App\Rules\Password\Contracts;

use Illuminate\Support\Stringable;
use Closure;

abstract class RuleHandler implements RuleHandlerInterface
{
    protected ?RuleHandlerInterface $next;

    /**
     * Constructor of this class
     */
    public function __construct(
        protected readonly int $base,
        protected readonly string $msg = '',
    ) {}

    public function setNext(?RuleHandlerInterface $next): RuleHandlerInterface
    {
        $this->next = $next;
        return $this;
    }

    public function handle(Stringable $value, Closure $fail): void
    {
        if ($this->base > -1 && $this->validate($value)) {
            $fail($this->msg);
        } else if ($this->next !== NULL) {
            $this->next->handle($value, $fail);
        }
    }

    abstract public function validate(Stringable $value): bool;
}
