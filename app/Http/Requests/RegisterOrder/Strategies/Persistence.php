<?php

declare(strict_types=1);

namespace App\Http\Requests\RegisterOrder\Strategies;

use App\Http\Requests\Checker;
use App\Libraries\Values\PhoneValue;

final class Persistence implements Checker
{
    protected int $emailMaxSize;

    public function __construct()
    {
        $this->emailMaxSize = \intval(
            config('database.schema.sizes.register-order.email')
        );
    }

    public function rules(): array
    {
        return [
            'email' => [
                'required',
                'email',
                "max:{$this->emailMaxSize}",
            ],
            'phone' => [
                'nullable',
                PhoneValue::rule()
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => 'Campo obrigatório',
            'email.email' => 'Campo inválido',
            'email.max' => "Tamanho máximo excedido ($this->emailMaxSize)"
        ];
    }
}
