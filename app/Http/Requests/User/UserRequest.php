<?php

declare(strict_types=1);

namespace App\Http\Requests\User;

use App\Http\Requests\Checker;
use App\Http\Requests\CustomFormRequest;
use App\Http\Requests\User\Strategies\Persistence;
use Exception;

final class UserRequest extends CustomFormRequest
{
    protected function pickChecker(): Checker
    {
        $method = strtolower($this->method());
        switch ($method) {
            case 'post':
            case 'put':
                return new Persistence(method: $method);
            default:
                throw new Exception("Method Not Implemented", 1);
        }
    }

    public function rules(): array
    {
        return $this->pickChecker()->rules();
    }

    public function messages(): array
    {
        return $this->pickChecker()->messages();
    }
}
