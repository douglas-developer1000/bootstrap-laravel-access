<?php

declare(strict_types=1);

namespace App\Http\Requests\Supplier\Strategies;

use App\Http\Requests\Checker;
use App\Libraries\Enums\SupplierColorEnum;
use App\Libraries\Traits\ImgCheckerTrait;
use App\Libraries\Traits\NativeCheckerTrait;
use App\Libraries\Traits\OneOrManyMsgTrait;
use App\Libraries\Values\CnpjValue;
use Illuminate\Database\Query\Builder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class Persistence implements Checker
{
    use OneOrManyMsgTrait, NativeCheckerTrait, ImgCheckerTrait;
    protected bool $isSuperAdmin;
    protected int|string $userId;
    protected int $nameMinSize;
    protected int $nameMaxSize;
    protected int $obsMaxSize;

    public function __construct(protected FormRequest $formRequest, protected bool $imgRequired = TRUE)
    {
        /**
         * @var \App\Models\User $user
         */
        $user = Auth::user();
        $this->isSuperAdmin = $user->hasRole('super-admin');
        $this->userId = $user->id;

        $this->obsMaxSize = \intval(
            config('database.schema.sizes.generic.obs.max')
        );
        $this->nameMinSize = \intval(
            config('database.schema.sizes.supplier.name.min')
        );
        $this->nameMaxSize = \intval(
            config('database.schema.sizes.supplier.name.max')
        );
        $this->loadImgProps();
    }

    protected function pickColorOrImgRules(): array
    {
        if ($this->isSuperAdmin) {
            return $this->pickImgRules(required: $this->imgRequired);
        }
        return [
            'color' => [
                'required',
                Rule::enum(SupplierColorEnum::class),
            ]
        ];
    }

    protected function pickColorOrImgMessages(): array
    {
        if ($this->isSuperAdmin) {
            return $this->pickImgMessages(required: $this->imgRequired);
        }
        return [
            'color.required' => 'Selecione um cor válida',
            'color.in' => 'Selecione um cor válida',
        ];
    }

    public function rules(): array
    {
        return [
            'name' => [
                'required',
                "min:{$this->nameMinSize}",
                "max:{$this->nameMaxSize}",
                Rule::unique('suppliers', 'name')->where(function (Builder $query) {
                    $query->where([
                        'user_id' => $this->userId
                    ])->orWhere([
                        'native' => 1
                    ]);
                })
            ],
            'cnpj' => [
                'bail',
                'nullable',
                CnpjValue::rule(),
                CnpjValue::uniqueRule('suppliers'),
            ],
            'obs' => [
                'nullable',
                "max:{$this->obsMaxSize}",
            ],
            ...$this->pickNativeRules(),
            ...$this->pickColorOrImgRules(),
        ];
    }

    public function messages(): array
    {
        $msgs = collect([
            'nameMinMsg' => $this->nameMinSize,
            'nameMaxMsg' => $this->nameMaxSize,
            'obsMaxMsg' => $this->obsMaxSize
        ])->map(
            fn(int $qty) => $this->makeSizeMsg($qty, 'caracter', 'caracteres')
        )->all();

        return [
            'name.required' => 'Campo obrigatório',
            'name.min' => "Tamanho mínimo: {$msgs['nameMinMsg']}",
            'name.max' => "Tamanho máximo excedido: {$msgs['nameMaxMsg']}",
            'name.unique' => 'Nome já utilizado',

            'cnpj.unique' => 'Cnpj já utilizado!',

            'obs.max' => "Tamanho máximo excedido: {$msgs['obsMaxMsg']}",

            ...$this->pickNativeMessages(),
            ...$this->pickColorOrImgMessages(),
        ];
    }
}
