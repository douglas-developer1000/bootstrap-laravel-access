<?php

declare(strict_types=1);

namespace App\Http\Requests\User;

use App\Http\Requests\Checker;
use App\Http\Requests\CustomFormRequest;
use App\Http\Requests\User\Strategies\FastPersistence;
use App\Http\Requests\User\Strategies\Persistence;
use Exception;

final class UserRequest extends CustomFormRequest
{
    protected function pickChecker(): Checker
    {
        $method = strtolower($this->method());
        $url = url()->current();
        switch ($url) {
            case route('users.store'):
                return match ($method) {
                    'post', 'put' => new FastPersistence(method: $method),
                    default => throw new Exception("Method Not Implemented", 1),
                };
            case route('guest.users.store'):
                return match ($method) {
                    'post' => new Persistence($this),
                    default => throw new Exception("Method Not Implemented", 1),
                };
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
