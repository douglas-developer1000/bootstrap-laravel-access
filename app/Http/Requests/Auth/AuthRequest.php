<?php

declare(strict_types=1);

namespace App\Http\Requests\Auth;

use App\Http\Requests\Auth\Strategies\Login;
use App\Http\Requests\Checker;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Exception;

final class AuthRequest extends FormRequest
{
    protected function pickChecker(): Checker
    {
        switch (strtolower($this->method())) {
            case 'post':
                return new Login();
            default:
                throw new Exception("Method Not Implemented", 1);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return $this->pickChecker()->rules();
    }

    public function messages(): array
    {
        return $this->pickChecker()->messages();
    }
}
