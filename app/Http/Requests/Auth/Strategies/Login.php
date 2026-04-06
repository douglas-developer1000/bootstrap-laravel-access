<?php

declare(strict_types=1);

namespace App\Http\Requests\Auth\Strategies;

use App\Http\Requests\Checker;

final class Login implements Checker
{
	public function rules(): array 
    {
        return [
            'email' => 'required|email',
            'password' => 'min:1',
        ];
    }

	public function messages(): array 
    {
        return [
            'email.required' => 'Campo obrigatório',
            'email.email' =>  'Campo inválido',
            'password.min' => 'Campo obrigatório',
        ];
    }
}
