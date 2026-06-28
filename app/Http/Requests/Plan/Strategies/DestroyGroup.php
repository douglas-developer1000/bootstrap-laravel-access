<?php

declare(strict_types=1);

namespace App\Http\Requests\Plan\Strategies;

use App\Http\Requests\Checker;
use App\Http\Requests\LateValidationInterface;
use App\Models\Plan;
use Illuminate\Validation\Validator;

final class DestroyGroup implements Checker
{
    public function __construct(LateValidationInterface $late)
    {
        $late->pushAfterValidation(
            function (Validator $validator) use (&$late) {
                $deletedAtColumn = (new Plan())->getDeletedAtColumn();
                $plans = collect($late->getRoute('planList'));
                if ($plans->contains(fn (Plan $plan) => $plan->$deletedAtColumn !== null)) {
                    $validator->errors()->add('roles', 'Requisição inválida!');
                }
            }
        );
    }

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
                'exists:plans,id',
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
