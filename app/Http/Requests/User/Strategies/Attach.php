<?php

declare(strict_types=1);

namespace App\Http\Requests\User\Strategies;

use App\Http\Requests\Checker;

final class Attach implements Checker
{

    public function rules(): array
    {
        return [
            'attachment' => [
                'required',
                'array',
                'min:1',
            ],
            'attachment.*' => 'integer|exists:roles,id',
        ];
    }

    public function messages(): array
    {
        return [
            'attachment.required' => 'Vinculação inválida',
            'attachment.array' => 'Vinculação inválida',
            'attachment.min' => 'Vinculação inválida',

            'attachment.*.integer' => 'Vinculação inválida',
            'attachment.*.exists' => 'Vinculação inválida',
        ];
    }
}
