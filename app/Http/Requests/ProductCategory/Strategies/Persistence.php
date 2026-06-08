<?php

declare(strict_types=1);

namespace App\Http\Requests\ProductCategory\Strategies;

use App\Http\Requests\Checker;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class Persistence implements Checker
{
    protected int $nameMaxSize;
    protected int $nameMinSize;
    protected int|string $userId;

    public function __construct()
    {
        $this->nameMinSize = \intval(
            config('database.schema.sizes.product-category.name.min')
        );
        $this->nameMaxSize = \intval(
            config('database.schema.sizes.product-category.name.max')
        );

        /** @var int|string $userId */
        $userId = Auth::id();
        $this->userId = $userId;
    }

    public function rules(): array
    {
        return [
            'name' => [
                'bail',
                'required',
                "min:{$this->nameMinSize}",
                "max:{$this->nameMaxSize}",
                Rule::unique('product_categories', 'name')->where(function (Builder $query) {
                    $query->where('user_id', $this->userId);
                }),
            ],
            'inheritance' => [
                'bail',
                'nullable',
                Rule::exists('product_categories', 'id'),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Campo obrigatório',
            'name.min' => "Tamanho mínimo ($this->nameMinSize)",
            'name.max' => "Tamanho máximo excedido ($this->nameMaxSize)",
            'name.unique' => "Nome já utilizado",

            'inheritance.exists' => 'Opção inválida',
        ];
    }
}
