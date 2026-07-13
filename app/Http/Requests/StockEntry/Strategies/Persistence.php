<?php

declare(strict_types=1);

namespace App\Http\Requests\StockEntry\Strategies;

use App\Http\Requests\Checker;
use App\Models\Discount;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

final class Persistence implements Checker
{
    protected User $user;

    protected int $costMinSize;

    protected int $costMaxSize;

    protected int $qtyMinSize;

    protected int $qtyMaxSize;

    public function __construct(protected Product $product)
    {
        $this->user = Auth::user();

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

    protected function getDiscountRules(): array
    {
        if ($this->user->cannot('viewAny', Discount::class)) {
            return [];
        }

        return [
            'discount' => [
                'bail',
                'nullable',
                'integer',
                Rule::exists('discounts', 'id')->where(function (Builder $query) {
                    $query
                        ->where('user_id', $this->user->id)
                        ->orWhere(['native' => 1]);
                }),
            ],
        ];
    }

    protected function getSupplierRules(): array
    {
        if ($this->user->cannot('viewAny', Supplier::class)) {
            return [];
        }

        return [
            'supplier' => [
                'required',
                'integer',
                Rule::exists('suppliers', 'id')->where(function (Builder $query) {
                    $query
                        ->where('user_id', $this->user->id)
                        ->orWhere(['native' => 1]);
                }),
            ],
        ];
    }

    public function rules(): array
    {
        return [
            'cost' => [
                'required',
                'decimal:0,2',
                "min:{$this->costMinSize}",
                "max:{$this->costMaxSize}",
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
                Rule::date()->after(now()),
            ],
            ...$this->getDiscountRules(),
            ...$this->getSupplierRules(),
        ];
    }

    protected function getDiscountMessages(): array
    {
        if ($this->user->cannot('viewAny', Discount::class)) {
            return [];
        }

        return [
            'discount.integer' => 'Campo inválido',
            'discount.exists' => 'Campo inválido',
        ];
    }

    protected function getSupplierMessages(): array
    {
        if ($this->user->cannot('viewAny', Supplier::class)) {
            return [];
        }

        return [
            'supplier.required' => 'Campo obrigatório',
            'supplier.integer' => 'Campo inválido',
            'supplier.exists' => 'Campo inválido',
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

            ...$this->getDiscountMessages(),
            ...$this->getSupplierMessages(),
        ];
    }
}
