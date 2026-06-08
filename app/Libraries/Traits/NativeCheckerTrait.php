<?php

declare(strict_types=1);

namespace App\Libraries\Traits;

use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

trait NativeCheckerTrait
{
    protected function pickNativeRules(): array
    {
        /**
         * @var \App\Models\User $user
         */
        $user = Auth::user();

        $rules = [
            'native' => ['required']
        ];
        if ($user->hasRole('super-admin')) {
            $rules['native'][] = Rule::in([0, 1]);
        } else {
            $rules['native'][] = Rule::in([0]);
        }
        return $rules;
    }

    protected function pickNativeMessages(): array
    {
        return [
            'native.required' => 'Requisição inválida',
            'native.in' => 'Requisição inválida',
        ];
    }
}
