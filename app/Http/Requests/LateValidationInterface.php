<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Closure;

interface LateValidationInterface
{
    public function getInput(string $key): mixed;

    public function pushAfterValidation(Closure $callback): void;

    public function getRoute(string $key): mixed;
}
