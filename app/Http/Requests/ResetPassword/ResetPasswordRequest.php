<?php

declare(strict_types=1);

namespace App\Http\Requests\ResetPassword;

use App\Http\Requests\Checker;
use App\Http\Requests\CustomFormRequest;
use App\Http\Requests\ResetPassword\Strategies\Post;
use \Exception;

class ResetPasswordRequest extends CustomFormRequest
{

    protected function pickChecker(): Checker
    {
        $method = strtolower($this->method());
        switch ($method) {
            case 'post':
                return new Post();
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
