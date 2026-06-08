<?php

declare(strict_types=1);

namespace App\Http\Requests\Supplier\Strategies;

use App\Http\Requests\Supplier\Strategies\Persistence;
use App\Libraries\Values\CnpjValue;
use App\Models\Supplier;
use Illuminate\Database\Query\Builder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class Update extends Persistence
{
    public function __construct(FormRequest $formRequest, protected Supplier $supplier)
    {
        parent::__construct(
            formRequest: $formRequest,
            imgRequired: false
        );
    }

    public function rules(): array
    {
        return [
            ...parent::rules(),
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
                })->ignore(
                    $this->supplier->id,
                    'id'
                )
            ],
            'cnpj' => [
                'bail',
                'nullable',
                CnpjValue::rule(),
                CnpjValue::uniqueRule('suppliers', $this->supplier->id)
            ]
        ];
    }
}
