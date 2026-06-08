<?php

declare(strict_types=1);

namespace App\Http\Requests\Discount\Strategies;

use App\Http\Requests\Checker;
use App\Http\Requests\LateValidationInterface;
use Illuminate\Validation\Rule;
use App\Libraries\Enums\DiscountTypeEnum;
use App\Models\Discount;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Validator;

class Persistence implements Checker
{
    protected int $valueMinSize;
    protected int $valueMaxSize;

    public function __construct(LateValidationInterface $late, ?Discount $discount = NULL)
    {
        $this->valueMinSize = \intval(
            config('database.schema.sizes.generic.decimal.min')
        );
        $this->valueMaxSize = \intval(
            config('database.schema.sizes.generic.decimal.max')
        );

        $late->pushAfterValidation(
            function (Validator $validator) use (&$late, $discount) {
                $this->validateValue(
                    $validator,
                    $late->getInput('type'),
                    $late->getInput('value'),
                    $discount
                );
            }
        );
    }

    public function rules(): array
    {
        return [
            'type' => [
                'bail',
                'required',
                Rule::in(array_column(DiscountTypeEnum::cases(), 'value')),
            ],
            'value' => [
                'bail',
                'required',
                'decimal:0,2',
                "gt:{$this->valueMinSize}",
                "max:{$this->valueMaxSize}",
            ]
        ];
    }

    public function messages(): array
    {
        return [
            'type.required' => 'Campo obrigatório',
            'type.in' => 'Tipo inválido',

            'value.required' => 'Campo obrigatório',
            'value.decimal' => 'Valor inválido',
            'value.gt' => "Valor deve ser maior que {$this->valueMinSize}",
            'value.max' => "Valor máximo: {$this->valueMaxSize}",
        ];
    }

    protected function validateValue(Validator $validator, string $type, string $value, ?Discount $except = NULL): void
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $query = DB::table('discounts')->where([
            'type' => $type,
            'value' => $value,
            'native' => 1,
        ]);
        if ($except) {
            $query = $query->whereNot([
                'id' => $except->id
            ]);
        }

        if ($query->exists()) {
            $name = DiscountTypeEnum::tryFrom($type)->toString();

            $validator->errors()->add(
                'value',
                "{$name} já existente"
            );
        } else {
            $query = DB::table('discounts')->where([
                'type' => $type,
                'value' => $value,
                'native' => 0,
                'user_id' => $user->id,
            ]);

            if ($except) {
                $query = $query->whereNot([
                    'id' => $except->id
                ]);
            }

            if ($query->exists()) {
                $name = DiscountTypeEnum::tryFrom($type)->toString();

                $validator->errors()->add(
                    'value',
                    "{$name} já existente"
                );
            }
        }
    }
}
