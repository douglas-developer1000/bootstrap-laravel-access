<?php

namespace App\Http\Requests\ResetPassword\Strategies;

use App\Http\Requests\Checker;
use App\Rules\Password\PasswordValid;
use App\Rules\Password\Handlers\{
    MaxSize,
    MinSize,
    QtyDigits,
    QtyLetters,
    QtyLowercase,
    QtySpecialChars,
    QtyUppercase
};

final class Post implements Checker
{
    protected int $emailMaxSize;

    public function __construct()
    {
        $this->emailMaxSize = \intval(
            config('database.schema.sizes.user.email')
        );
    }

    public function rules(): array
    {
        return [
            'token' => 'required',
            'email' => [
                'required',
                'email',
                "max:{$this->emailMaxSize}",
            ],
            'password' => [
                'bail',
                'required',
                'confirmed',
                new PasswordValid(
                    new MinSize(8),
                    new QtyLetters(1),
                    new QtyUppercase(1),
                    new QtyLowercase(1),
                    new QtyDigits(1),
                    new QtySpecialChars(1),
                    new MaxSize(\intval(
                        config('database.schema.sizes.user.password')
                    )),
                )
            ]
        ];
    }

    public function messages(): array
    {
        return [
            'token.required' => 'Requisição inválida',

            'email.required' => 'Campo obrigatório',
            'email.email' => 'Campo inválido',
            'email.max' => "Tamanho máximo excedido ($this->emailMaxSize)",

            'password.required' => 'Campo obrigatório',
            'password.confirmed' => 'Senha e confirmação são diferentes',
        ];
    }
}
