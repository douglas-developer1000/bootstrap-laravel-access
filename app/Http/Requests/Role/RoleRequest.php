<?php

declare(strict_types=1);

namespace App\Http\Requests\Role;

use App\Http\Requests\Checker;
use App\Http\Requests\CustomFormRequest;
use App\Http\Requests\Role\Strategies\Destroy;
use App\Http\Requests\Role\Strategies\Persistence;
use App\Http\Requests\Role\Strategies\Update;
use App\Http\Requests\Role\Strategies\Attach;
use App\Http\Requests\Role\Strategies\Detach;
use Exception;

final class RoleRequest extends CustomFormRequest
{
    protected function pickChecker(): Checker
    {
        $url = url()->current();
        switch ($url) {
            case route('roles.store'):
                return new Persistence();
            case route('roles.group.bind', $this->route('role', 0)):
                return new Attach();
            case route('roles.update', $this->route('role', 0)):
                return new Update(id: $this->route('role', 0));
            case route('roles.group.destroy'):
                return new Destroy();
            case route('roles.group.unbind', $this->route('role', 0)):
                return new Detach();
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
