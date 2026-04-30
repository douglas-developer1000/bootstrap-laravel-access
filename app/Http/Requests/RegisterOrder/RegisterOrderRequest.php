<?php

declare(strict_types=1);

namespace App\Http\Requests\RegisterOrder;

use App\Http\Requests\Checker;
use App\Http\Requests\CustomFormRequest;
use App\Http\Requests\RegisterOrder\Strategies\Approve;
use App\Http\Requests\RegisterOrder\Strategies\Destroy;
use App\Http\Requests\RegisterOrder\Strategies\Persistence;
use Exception;

final class RegisterOrderRequest extends CustomFormRequest
{

    protected function pickChecker(): Checker
    {
        $url = url()->current();
        switch ($url) {
            case route('register.orders.store'):
                return new Persistence();
            case route('register.orders.group.destroy'):
                return new Destroy();
            case route('register.orders.group.approve'):
                return new Approve();
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
