<?php

declare(strict_types=1);

namespace App\Http\Requests\Permission;

use App\Http\Requests\Checker;
use App\Http\Requests\CustomFormRequest;
use App\Http\Requests\Permission\Strategies\Destroy;
use App\Http\Requests\Permission\Strategies\Persistence;
use App\Http\Requests\Permission\Strategies\Update;
use Exception;

final class PermissionRequest extends CustomFormRequest
{
    protected function pickChecker(): Checker
    {
        $url = url()->current();
        switch ($url) {
            case route('permissions.store'):
                return new Persistence();
            case route('permissions.update', $this->route('permission', 0)):
                return new Update(id: $this->route('permission'));
            case route('permissions.group.destroy'):
                return new Destroy();
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
