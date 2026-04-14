<?php

declare(strict_types=1);

namespace App\Rules\Password\Handlers;

use App\Rules\Password\Contracts\RuleHandler;
use Illuminate\Support\Stringable;

final class QtyUppercase extends RuleHandler
{
    public function __construct(int $base)
    {
        parent::__construct($base, "Quantidade mínima de maiúsculas: ($base)");
    }

    public function validate(Stringable $value): bool
    {
        return $value->replaceMatches('|[^A-ZÁÀÂÃÉÈÍÏÓÔÕÖÚÇÑ]|', '')->length() < $this->base;
    }
}
