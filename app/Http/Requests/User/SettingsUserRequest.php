<?php

declare(strict_types=1);

namespace App\Http\Requests\User;

use App\Http\Requests\Checker;
use App\Http\Requests\CustomFormRequest;
use App\Http\Requests\User\Strategies\SettingsUpdate;
use \Exception;

final class SettingsUserRequest extends CustomFormRequest
{
    protected function pickChecker(): Checker
    {
        $url = url()->current();
        switch ($url) {
            case route('settings.user.update', $this->route('user', 0)):
                return new SettingsUpdate();
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
