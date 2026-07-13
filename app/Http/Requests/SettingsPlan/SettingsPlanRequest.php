<?php

declare(strict_types=1);

namespace App\Http\Requests\SettingsPlan;

use App\Http\Requests\Checker;
use App\Http\Requests\CustomFormRequest;
use App\Http\Requests\SettingsPlan\Strategies\PlanHandler;
use Exception;

final class SettingsPlanRequest extends CustomFormRequest
{
    protected function pickChecker(): Checker
    {
        $url = url()->current();
        switch ($url) {
            case route('plans.view.handle', $this->route('plan', 0)):
                return new PlanHandler($this->route('plan', 0));
            default:
                throw new Exception('Method Not Implemented', 1);
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
