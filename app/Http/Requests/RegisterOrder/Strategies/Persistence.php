<?php

declare(strict_types=1);

namespace App\Http\Requests\RegisterOrder\Strategies;

use App\Http\Requests\Checker;
use App\Rules\PhoneValid;

final class Persistence implements Checker
{
    protected int $emailMaxSize;

    protected int $phoneMaxSize;

    public function __construct()
    {
        $this->emailMaxSize = \intval(
            config('database.schema.sizes.register-order.email')
        );
        $this->phoneMaxSize = \intval(
            config('database.schema.sizes.register-order.phone')
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
                new PhoneValid($this->phoneMaxSize)
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
