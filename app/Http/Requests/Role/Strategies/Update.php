<?php

declare(strict_types=1);

namespace App\Http\Requests\Role\Strategies;

use App\Http\Requests\Checker;
use Illuminate\Validation\Rule;

class Update implements Checker
{
    public function __construct(
        protected string|null $id = NULL
    ) {
        // ...
    }

    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'min:3',
                Rule::unique('roles', 'name')->ignore($this->id ?? 0, 'id')
            ]
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Campo obrigatório',
            'name.min' => 'Tamanho inválido',
            'name.unique' => 'Valor já utilizado'
        ];
    }
}
