<?php

declare(strict_types=1);

namespace App\Http\Requests\User\Strategies;

use App\Http\Requests\Checker;
use Illuminate\Validation\Rule;

class Persistence implements Checker
{
    public function __construct(
        /** @var string $method **/
        protected string $method,
    ) {
        // ...
    }

    public function rules(): array
    {
        if ($this->method === 'put') {
            return [
                'name' => [
                    'required',
                    'min:3'
                ]
            ];
        }
        return [
            'name' => [
                'required',
                'min:3',
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
            'name.min' => 'Tamanho inválido',

            'email.email' => 'Campo inválido',
            'email.unique' => 'Valor já utilizado'
        ];
    }
}
