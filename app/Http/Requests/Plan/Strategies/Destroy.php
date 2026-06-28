<?php

declare(strict_types=1);

namespace App\Http\Requests\Plan\Strategies;

use App\Http\Requests\Checker;
use App\Http\Requests\LateValidationInterface;
use App\Models\Plan;
use Illuminate\Validation\Validator;

final class Destroy implements Checker
{
    public function __construct(LateValidationInterface $late)
    {
        $late->pushAfterValidation(
            function (Validator $validator) use (&$late) {
                $deletedAtColumn = (new Plan())->getDeletedAtColumn();
                $plan = $late->getRoute('plan');
                if ($plan->$deletedAtColumn !== null) {
                    $validator->errors()->add('destroy', 'Requisição inválida!');
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
