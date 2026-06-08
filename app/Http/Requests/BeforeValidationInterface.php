<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Closure;

interface BeforeValidationInterface
{
    /**
     * @param Closure(\Illuminate\Foundation\Http\FormRequest $formRequest): void $callback
     */
    public function pushBeforeValidation(Closure $callback): void;
}
