<?php

declare(strict_types=1);

namespace App\Http\Requests\User;

use App\Http\Requests\Checker;
use App\Http\Requests\CustomFormRequest;
use App\Http\Requests\User\Strategies\Destroy;
use App\Http\Requests\User\Strategies\FastPersistence;
use App\Http\Requests\User\Strategies\Persistence;
use App\Http\Requests\User\Strategies\Restore;
use App\Http\Requests\User\Strategies\Update;
use App\Http\Requests\User\Strategies\AttachRoles;
use App\Http\Requests\User\Strategies\AttachPermissions;
use Exception;

final class UserRequest extends CustomFormRequest
{
    protected function pickChecker(): Checker
    {
        $method = strtolower($this->method());
        $url = url()->current();
        switch ($url) {
            case route('users.update', $this->route('user', 0)):
                return match ($method) {
                    'put' => new Update(),
                    default => throw new Exception("Method Not Implemented", 1),
                };
            case route('users.store'):
                return match ($method) {
                    'post' => new FastPersistence(),
                    default => throw new Exception("Method Not Implemented", 1),
                };
            case route('guest.users.store'):
                return match ($method) {
                    'post' => new Persistence($this),
                    default => throw new Exception("Method Not Implemented", 1),
                };
            case route('users.group.destroy'):
                return new Destroy($this);
            case route('users.trashed.group.restore'):
                return new Restore();
            case route('users.bind.roles.group', $this->route('user', 0)):
                return new AttachRoles();
            case route('users.bind.permissions.group', $this->route('user', 0)):
                return new AttachPermissions();
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
