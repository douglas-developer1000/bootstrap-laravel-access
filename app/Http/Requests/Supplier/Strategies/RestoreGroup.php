<?php

declare(strict_types=1);

namespace App\Http\Requests\Supplier\Strategies;

use App\Http\Requests\Checker;

final class RestoreGroup implements Checker
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
                'exists:product_categories,id',
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
