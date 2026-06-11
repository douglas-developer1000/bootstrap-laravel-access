<?php

declare(strict_types=1);

namespace App\Http\Requests\StockExit\Strategies;

use App\Http\Requests\Checker;
use App\Rules\StockEntriesToExitRule;

final class StockExitPersistence implements Checker
{
    public function __construct() {}

    public function rules(): array
    {
        return [
            'entries' => [
                'bail',
                'array',
                new StockEntriesToExitRule()
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'entries.array' => 'Estoque inválido',
        ];
    }
}
