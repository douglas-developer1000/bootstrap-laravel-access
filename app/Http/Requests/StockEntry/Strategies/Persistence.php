<?php

declare(strict_types=1);

namespace App\Http\Requests\StockEntry\Strategies;

use App\Http\Requests\Checker;
use App\Models\Product;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

final class Persistence implements Checker
{
    protected int|string $userId;
    protected int $costMinSize;
    protected int $costMaxSize;
    protected int $qtyMinSize;
    protected int $qtyMaxSize;

    public function __construct(protected Product $product)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $this->userId = $user->id;

        $this->costMinSize = \intval(
            config('database.schema.sizes.generic.decimal.min')
        );
        $this->costMaxSize = \intval(
            config('database.schema.sizes.generic.decimal.max')
        );
        $this->qtyMinSize = \intval(
            config('database.schema.sizes.generic.integer.min')
        );
        $this->qtyMaxSize = \intval(
            config('database.schema.sizes.generic.integer.max')
        );
    }

    public function rules(): array
    {
        return [
            'cost' => [
                'required',
                'decimal:0,2',
                "min:{$this->costMinSize}",
                "max:{$this->costMaxSize}"
            ],
            'qty' => [
                'required',
                'integer',
                "min:{$this->qtyMinSize}",
                "max:{$this->qtyMaxSize}",
            ],
            'validity' => [
                'bail',
                'nullable',
                'date',
                Rule::date()->format('Y-m-d'),
                Rule::date()->after(now())
            ],
            'discount' => [
                'bail',
                'nullable',
                'integer',
                Rule::exists('discounts', 'id')->where(function (Builder $query) {
                    $query
                        ->where('user_id', $this->userId)
                        ->orWhere(['native' => 1]);
                }),
            ],
            'supplier' => [
                'required',
                'integer',
                Rule::exists('suppliers', 'id')->where(function (Builder $query) {
                    $query
                        ->where('user_id', $this->userId)
                        ->orWhere(['native' => 1]);
                }),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'cost.required' => 'Campo obrigatório',
            'cost.decimal' => 'Campo inválido',
            'cost.min' => "Valor mínimo: {$this->costMinSize}",
            'cost.max' => "Valor máximo: {$this->costMaxSize}",

            'qty.required' => 'Campo obrigatório',
            'qty.integer' => 'Quantidade inválida',
            'qty.min' => "Quantidade mínima: {$this->qtyMinSize}",
            'qty.max' => "Quantidade máxima: {$this->qtyMaxSize}",

            'validity.date' => 'Campo inválido',
            'validity.date_format' => 'Formato inválido',
            'validity.after' => 'Validade inválida',

            'discount.integer' => 'Campo inválido',
            'discount.exists' => 'Campo inválido',

            'supplier.required' => 'Campo obrigatório',
            'supplier.integer' => 'Campo inválido',
            'supplier.exists' => 'Campo inválido',
        ];
    }
}
