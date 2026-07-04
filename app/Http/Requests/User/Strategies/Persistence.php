<?php

declare(strict_types=1);

namespace App\Http\Requests\User\Strategies;

use App\Http\Requests\Checker;
use App\Rules\Password\Handlers\{
    MaxSize,
    MinSize,
    QtyDigits,
    QtyLetters,
    QtyLowercase,
    QtySpecialChars,
    QtyUppercase
};
use App\Rules\Password\PasswordValid;
use Illuminate\Foundation\Http\FormRequest;
use App\Libraries\Values\PhoneValue;

final class Persistence implements Checker
{
    protected int $nameMaxSize;
    protected int $emailMaxSize;

    public function __construct(protected FormRequest $formRequest)
    {
        $this->nameMaxSize = \intval(
            config('database.schema.sizes.user.name')
        );
        $this->emailMaxSize = \intval(
            config('database.schema.sizes.user.email')
        );
    }


    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'min:2',
                "max:{$this->nameMaxSize}"
            ],
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
            'name.required' => 'Campo obrigatório',
            'name.min' => 'Tamanho mínimo (2)',
            'name.max' => "Tamanho máximo excedido ($this->nameMaxSize)",

            'email.required' => 'Campo obrigatório',
            'email.email' => 'Campo inválido',
            'email.max' => "Tamanho máximo excedido ($this->emailMaxSize)",

            'password.required' => 'Campo obrigatório',
            'password.confirmed' => 'Senha e confirmação são diferentes',
        ];
    }
}
