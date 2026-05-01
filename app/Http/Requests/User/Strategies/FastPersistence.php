<?php

declare(strict_types=1);

namespace App\Http\Requests\User\Strategies;

use App\Http\Requests\Checker;
use Illuminate\Validation\Rule;

final class FastPersistence implements Checker
{
    protected int $nameMinSize;
    protected int $nameMaxSize;

    public function __construct()
    {
        $this->nameMinSize = 2;
        $this->nameMaxSize = \intval(
            config('database.schema.sizes.user.name')
        );
    }

    public function rules(): array
    {
        return [
            'name' => [
                'required',
                "min:{$this->nameMinSize}",
                "max:{$this->nameMaxSize}"
            ],
            'email' => [
                'email',
                Rule::unique('users', 'email')
            ]
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Campo obrigatório',
            'name.min' => "Tamanho mínimo {$this->nameMinSize}",
            'name.max' => "Tamanho máximo excedido ($this->nameMaxSize)",

            'email.email' => 'Campo inválido',
            'email.unique' => 'Valor já utilizado'
        ];
    }
}
