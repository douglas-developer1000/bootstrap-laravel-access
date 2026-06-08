<?php

declare(strict_types=1);

namespace App\Http\Requests\PaymentCard\Strategies;

use App\Http\Requests\Checker;

final class DestroyGroup implements Checker
{

    public function rules(): array
    {
        return [
            'remotion' => [
                'required',
                'array',
                'min:1',
            ],
            'remotion.*' => [
                'integer',
                'exists:payment_cards,id',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'remotion.required' => 'Remoção inválida',
            'remotion.array' => 'Remoção inválida',
            'remotion.min' => 'Remoção inválida',

            'remotion.*.integer' => 'Remoção inválida',
            'remotion.*.exists' => 'Remoção inválida',
        ];
    }
}
