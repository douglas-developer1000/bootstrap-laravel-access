<?php

declare(strict_types=1);

namespace App\Rules\Password\Handlers;

use App\Rules\Password\Contracts\RuleHandler;
use Illuminate\Support\Stringable;

final class MinSize extends RuleHandler
{
    public function __construct(int $base)
    {
        parent::__construct($base, 'Tamanho mínimo não fornecido');
    }


    public function validate(Stringable $value): bool
    {
        return $value->length() < $this->base;
    }
}
