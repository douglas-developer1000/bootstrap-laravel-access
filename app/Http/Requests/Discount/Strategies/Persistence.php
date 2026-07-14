<?php

declare(strict_types=1);

namespace App\Http\Requests\Discount\Strategies;

use App\Http\Requests\Checker;
use App\Http\Requests\LateValidationInterface;
use App\Libraries\Enums\DiscountTypeEnum;
use App\Models\Discount;
use App\Models\User;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

final class Persistence implements Checker
{
    protected int $valueMinSize;

    protected int $valueMaxSize;

    public function __construct(LateValidationInterface $late, ?Discount $discount = null)
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
            ],
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

    protected function isDiscountIntoDatabase(string $type, string $value, ?Discount $except, int $native = 0): bool
    {
        $query = Discount::where([
            'type' => $type,
            'value' => $value,
            'native' => $native,
        ])->getQuery();

        return $query
            ->when(
                $native === 0,
                function (Builder $query) {
                    /** @var User $user */
                    $user = Auth::user();

                    return $query->where([
                        'user_id' => $user->id,
                    ]);
                },
            )
            ->when(
                $except !== null,
                fn (Builder $query) => $query->whereNot([
                    'id' => $except->id,
                ])
            )
            ->exists();
    }

    protected function validateValue(Validator $validator, string $type, string $value, ?Discount $except = null): void
    {
        if ($this->isDiscountIntoDatabase($type, $value, $except, 1)) {
            $name = DiscountTypeEnum::tryFrom($type)->toString();

            $validator->errors()->add(
                'value',
                "{$name} já existente"
            );
        } elseif ($this->isDiscountIntoDatabase($type, $value, $except)) {
            $name = DiscountTypeEnum::tryFrom($type)->toString();

            $validator->errors()->add(
                'value',
                "{$name} já existente"
            );
        }
    }
}
