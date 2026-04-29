<?php

declare(strict_types=1);

namespace App\Http\Requests\Role\Strategies;

use App\Http\Requests\Checker;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

final class Destroy implements Checker
{

    public function rules(): array
    {
        return [
            'remotion' => [
                'required',
                'array',
                'min:1',
                Rule::doesntContain(
                    Role::where(
                        ['name' => 'super-admin']
                    )->orWhere(
                        ['name' => 'user']
                    )->get()->map(fn($item) => \strval($item->id))->all()
                )
            ],
            'remotion.*' => 'integer|exists:roles,id',
        ];
    }

    public function messages(): array
    {
        return [
            'remotion.required' => 'Remoção inválida',
            'remotion.array' => 'Remoção inválida',
            'remotion.min' => 'Remoção inválida',
            'remotion.doesnt_contain' => 'Remoção inválida',

            'remotion.*.integer' => 'Remoção inválida',
            'remotion.*.exists' => 'Remoção inválida',
        ];
    }
}
