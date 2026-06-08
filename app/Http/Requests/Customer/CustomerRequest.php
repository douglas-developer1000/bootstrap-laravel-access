<?php

declare(strict_types=1);

namespace App\Http\Requests\Customer;

use App\Http\Requests\Checker;
use App\Http\Requests\CustomFormRequest;
use App\Http\Requests\Customer\Strategies\Persistence;
use App\Http\Requests\Customer\Strategies\Update;
use App\Http\Requests\Customer\Strategies\DestroyGroup;
use App\Http\Requests\Customer\Strategies\RestoreGroup;

final class CustomerRequest extends CustomFormRequest
{

    protected function pickChecker(): Checker
    {
        $url = url()->current();
        switch ($url) {
            case route('customers.store'):
                return new Persistence();
            case route('customers.update', $this->route('customer', 0)):
                return new Update($this->route('customer'));
            case route('customers.group.destroy', [
                'key' => $this->route('key', 'key'),
                'customerList' => 'list'
            ]):
                return new DestroyGroup();
            case route('customers.group.restore', [
                'key' => $this->route('key', 'key'),
                'customerList' => 'trashed'
            ]):
                return new RestoreGroup();
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
