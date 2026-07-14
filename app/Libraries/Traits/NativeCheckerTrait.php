<?php

declare(strict_types=1);

namespace App\Libraries\Traits;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

trait NativeCheckerTrait
{
    protected function pickNativeRules(): array
    {
        /** @var User */
        $user = Auth::user();

        if ($user->can('beSuperAdmin', User::class)) {
            return [
                'native' => [
                    'required',
                    Rule::in([0, 1]),
                ],
            ];
        }

        return [
            'native' => [
                'required',
                Rule::in([0]),
            ],
        ];
    }

    protected function pickNativeMessages(): array
    {
        return [
            'native.required' => 'Requisição inválida',
            'native.in' => 'Requisição inválida',
        ];
    }
}
