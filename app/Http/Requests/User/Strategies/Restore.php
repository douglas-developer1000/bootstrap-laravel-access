<?php

declare(strict_types=1);

namespace App\Http\Requests\User\Strategies;

use App\Http\Requests\Checker;
use App\Rules\OnlySoftDelete;

final class Restore implements Checker
{

    public function rules(): array
    {
        return [
            'restoration' => [
                'required',
                'array',
                'min:1',
            ],
            'restoration.*' => [
                'integer',
                'exists:users,id',
                new OnlySoftDelete('Restauração')
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'restoration.required' => 'Restauração inválida',
            'restoration.array' => 'Restauração inválida',
            'restoration.min' => 'Restauração inválida',

            'restoration.*.integer' => 'Restauração inválida',
            'restoration.*.exists' => 'Restauração inválida',
        ];
    }
}
