<?php

declare(strict_types=1);

namespace App\Http\Requests\Product\Strategies;

use App\Http\Requests\Checker;
use App\Libraries\Traits\ImgCheckerTrait;
use App\Libraries\Traits\OneOrManyMsgTrait;
use App\Models\ProductCategory;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

final class Persistence implements Checker
{
    use ImgCheckerTrait, OneOrManyMsgTrait;

    protected int $nameMaxSize;

    protected int $nameMinSize;

    protected int $obsMaxSize;

    protected int $detailsMaxSize;

    protected User $user;

    public function __construct()
    {
        $this->nameMinSize = \intval(
            config('database.schema.sizes.product.name.min')
        );
        $this->nameMaxSize = \intval(
            config('database.schema.sizes.product.name.max')
        );
        $this->obsMaxSize = \intval(
            config('database.schema.sizes.generic.obs.max')
        );
        $this->detailsMaxSize = \intval(
            config('database.schema.sizes.product.details.max')
        );
        $this->user = Auth::user();
        $this->loadImgProps();
    }

    protected function getProductCategoryRules(): array
    {
        if ($this->user->cannot('viewAny', ProductCategory::class)) {
            return [];
        }

        return [
            'category' => [
                'required',
                'integer',
                'exists:product_categories,id',
            ],
        ];
    }

    public function rules(): array
    {
        return [
            'name' => [
                'bail',
                'required',
                "min:{$this->nameMinSize}",
                "max:{$this->nameMaxSize}",
            ],
            'obs' => [
                'nullable',
                "max:{$this->obsMaxSize}",
            ],
            'details' => [
                'nullable',
                'json',
                "max:{$this->detailsMaxSize}",
            ],
            ...$this->getProductCategoryRules(),
            ...$this->pickImgRules(required: false),
        ];
    }

    protected function getProductCategoryMessages(): array
    {
        if ($this->user->cannot('viewAny', ProductCategory::class)) {
            return [];
        }

        return [
            'category.required' => 'Campo obrigatório',
            'category.exists' => 'Opção inválida',
        ];
    }

    public function messages(): array
    {
        $nameMinMsg = $this->makeSizeMsg(
            $this->nameMinSize,
            'caracter',
            'caracteres'
        );
        $nameMaxMsg = $this->makeSizeMsg(
            $this->nameMaxSize,
            'caracter',
            'caracteres'
        );
        $obsMaxMsg = $this->makeSizeMsg(
            $this->obsMaxSize,
            'caracter',
            'caracteres'
        );

        return [
            'name.required' => 'Campo obrigatório',
            'name.min' => "Tamanho mínimo: {$nameMinMsg}",
            'name.max' => "Tamanho máximo excedido: {$nameMaxMsg}",

            'obs.max' => "Tamanho máximo excedido: {$obsMaxMsg}",

            'details.max' => 'Quantidade de detalhes excedido',
            'details.json' => 'Detalhes inválidos',

            ...$this->getProductCategoryMessages(),
            ...$this->pickImgMessages(required: false),
        ];
    }
}
