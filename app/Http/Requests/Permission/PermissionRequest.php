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
        $method = strtolower($this->method());
        switch ($method) {
            case 'post':
                return new Persistence();
            case 'put':
                return new Update(
                    id: $this->route('permission')
                );
            case 'delete':
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
