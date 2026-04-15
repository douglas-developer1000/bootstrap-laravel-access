<?php

namespace App\Http\Requests\ForgotPassword\Strategies;

use App\Http\Requests\Checker;

final class Post implements Checker
{

    public function rules(): array
    {
        return [
            'email' => [
                'required',
                'email',
                'exists:users,email',
            ]
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => 'Campo obrigatório',
            'email.email' => 'Campo inválido',
            'email.exists' => 'Campo inválido',
        ];
    }
}
