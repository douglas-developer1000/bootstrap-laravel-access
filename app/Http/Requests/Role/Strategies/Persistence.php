<?php

namespace App\Http\Requests\Role\Strategies;

use App\Http\Requests\Checker;
use Illuminate\Validation\Rule;

class Persistence implements Checker
{
    public function __construct(
        /** @var string $method **/
        protected string $method,
        protected string|null $id = NULL
    ) {
        // ...
    }

    public function rules(): array
    {
        if ($this->method === 'put') {
            return [
                'name' => [
                    'required',
                    'min:3',
                    Rule::unique('roles', 'name')->ignore($this->id ?? 0, 'id')
                ]
            ];
        }
        return [
            'name' => [
                'required',
                'min:3',
                Rule::unique('roles', 'name')
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
