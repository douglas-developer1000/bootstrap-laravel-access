<?php

declare(strict_types=1);

namespace App\Http\Requests\User\Strategies;

use App\Http\Requests\Checker;

final class DetachPermissions implements Checker
{

    public function rules(): array
    {
        return [
            'detachment' => [
                'required',
                'array',
                'min:1',
            ],
            'detachment.*' => 'integer|exists:permissions,id',
        ];
    }

    public function messages(): array
    {
        return [
            'detachment.required' => 'Desvinculação inválida',
            'detachment.array' => 'Desvinculação inválida',
            'detachment.min' => 'Desvinculação inválida',

            'detachment.*.integer' => 'Desvinculação inválida',
            'detachment.*.exists' => 'Desvinculação inválida',
        ];
    }
}
