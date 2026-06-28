<?php

declare(strict_types=1);

namespace App\Http\Requests\Plan\Strategies;

use App\Http\Requests\Checker;
use App\Http\Requests\LateValidationInterface;
use App\Models\Plan;
use Illuminate\Support\Collection;
use Illuminate\Validation\Validator;

final class RestoreGroup implements Checker
{
    public function __construct(LateValidationInterface $late)
    {
        $late->pushAfterValidation(
            function (Validator $validator) use (&$late) {
                $deletedAtColumn = (new Plan())->getDeletedAtColumn();
                /** @var Collection<int, Plan> $plans */
                $plans = collect($late->getRoute('planList'));

                if ($plans->contains(fn (Plan $plan) => $plan->$deletedAtColumn === null)) {
                    $validator->errors()->add('restorarion', 'Requisição inválida!');
                }
            }
        );
    }

    public function rules(): array
    {
        return [];
    }

    public function messages(): array
    {
        return [];
    }
}
