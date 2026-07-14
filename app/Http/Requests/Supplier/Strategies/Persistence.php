<?php

declare(strict_types=1);

namespace App\Http\Requests\Supplier\Strategies;

use App\Http\Requests\Checker;
use App\Libraries\Enums\SupplierColorEnum;
use App\Libraries\Traits\ImgCheckerTrait;
use App\Libraries\Traits\NativeCheckerTrait;
use App\Libraries\Traits\OneOrManyMsgTrait;
use App\Libraries\Values\CnpjValue;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Database\Query\Builder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

final class Persistence implements Checker
{
    use ImgCheckerTrait, NativeCheckerTrait, OneOrManyMsgTrait;

    protected bool $isSuperAdmin;

    protected int|string $userId;

    protected int $nameMinSize;

    protected int $nameMaxSize;

    protected int $obsMaxSize;

    public function __construct(
        protected FormRequest $formRequest,
        protected bool $imgRequired = true,
        protected ?Supplier $supplier = null
    ) {
        /**
         * @var User $user
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
            ],
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
                Rule::unique('suppliers', 'name')->where(fn (Builder $query) => (
                    $query
                        ->where('user_id', $this->userId)
                        ->orWhere('native', 1)
                        ->when(
                            $this->supplier,
                            fn (Builder $query, Supplier $supplier) => $query->ignore(
                                $supplier->id,
                                'id'
                            )
                        )
                )),
                Str::of('different:')->append(Supplier::getAnonymousName())->toString(),
            ],
            'cnpj' => [
                'bail',
                'nullable',
                CnpjValue::rule(),
                CnpjValue::uniqueRule('suppliers', $this->supplier?->id),
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
            'obsMaxMsg' => $this->obsMaxSize,
        ])->map(
            fn (int $qty) => $this->makeSizeMsg($qty, 'caracter', 'caracteres')
        )->all();

        return [
            'name.required' => 'Campo obrigatório',
            'name.min' => "Tamanho mínimo: {$msgs['nameMinMsg']}",
            'name.max' => "Tamanho máximo excedido: {$msgs['nameMaxMsg']}",
            'name.unique' => 'Nome já utilizado',
            'name.different' => 'Valor reservado. Por favor escolha outro.',

            'cnpj.unique' => 'Cnpj já utilizado!',

            'obs.max' => "Tamanho máximo excedido: {$msgs['obsMaxMsg']}",

            ...$this->pickNativeMessages(),
            ...$this->pickColorOrImgMessages(),
        ];
    }
}
