<?php

declare(strict_types=1);

namespace App\Http\Requests;

interface Checker
{
    /**
     * Define the rules used by this checker in request validation
     */
    public function rules(): array;

    /**
     * Define the messages used by this checker in request invalidation
     */
    public function messages(): array;
}
