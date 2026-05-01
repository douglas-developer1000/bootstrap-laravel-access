<?php

declare(strict_types=1);

namespace App\Http\Requests\RegisterOrder\Strategies;

use App\Http\Requests\Checker;

final class Approve implements Checker
{

    public function rules(): array
    {
        return [
            'approvement' => [
                'required',
                'array',
                'min:1',
            ],
            'approvement.*' => [
                'integer',
                'exists:register_orders,id'
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'approvement.required' => 'Aprovação inválida',
            'approvement.array' => 'Aprovação inválida',
            'approvement.min' => 'Aprovação inválida',

            'approvement.*.integer' => 'Aprovação inválida',
            'approvement.*.exists' => 'Aprovação inválida',
        ];
    }
}
