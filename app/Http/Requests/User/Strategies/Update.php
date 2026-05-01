<?php

declare(strict_types=1);

namespace App\Http\Requests\User\Strategies;

use App\Http\Requests\Checker;
use Illuminate\Foundation\Http\FormRequest;

final class Update implements Checker
{
    protected int $nameMinSize;
    protected int $nameMaxSize;

    public function __construct(FormRequest $request)
    {
        $request->merge(['id' => $request->route('user', 0)]);

        $this->nameMinSize = 2;
        $this->nameMaxSize = \intval(
            config('database.schema.sizes.user.name')
        );
    }

    public function rules(): array
    {
        return [
            'id' => 'required|integer|exists:users,id',
            'name' => [
                'required',
                "min:{$this->nameMinSize}",
                "max:{$this->nameMaxSize}"
            ]
        ];
    }

    public function messages(): array
    {
        return [
            'id.required' => 'Requisição inválida',
            'id.integer' => 'Requisição inválida',
            'id.exists' => 'Requisição inválida',

            'name.required' => 'Campo obrigatório',
            'name.min' => "Tamanho mínimo {$this->nameMinSize}",
            'name.max' => "Tamanho máximo excedido ($this->nameMaxSize)",
        ];
    }
}
