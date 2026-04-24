<?php

declare(strict_types=1);

namespace App\Http\Requests\Customer;

use App\Http\Requests\Checker;
use App\Http\Requests\CustomFormRequest;
use App\Http\Requests\Customer\Strategies\Persistence;
use App\Http\Requests\Customer\Strategies\Update;

final class CustomerRequest extends CustomFormRequest
{

    protected function pickChecker(): Checker
    {
        $method = strtolower($this->method());
        switch ($method) {
            case 'post':
                return new Persistence();
            case 'put':
                $customer = $this->route('customer');
                return new Update(
                    id: $customer->id,
                );
            default:
                throw new \Exception("Method Not Implemented", 1);
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
