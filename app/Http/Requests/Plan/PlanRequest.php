<?php

declare(strict_types=1);

namespace App\Http\Requests\Plan;

use App\Http\Requests\Checker;
use App\Http\Requests\CustomFormRequest;
use App\Http\Requests\Plan\Strategies\CreateForm;
use App\Http\Requests\Plan\Strategies\Destroy;
use App\Http\Requests\Plan\Strategies\DestroyGroup;
use App\Http\Requests\Plan\Strategies\Persistence;
use App\Http\Requests\Plan\Strategies\Restore;
use App\Http\Requests\Plan\Strategies\RestoreGroup;
use Exception;

final class PlanRequest extends CustomFormRequest
{
    protected function pickChecker(): Checker
    {
        $url = url()->current();
        switch ($url) {
            case route('plans.restore', $this->route('planDeleted', 0)):
                return new Restore($this);
            case route('plans.group.restore', [
                'key' => $this->route('key', 'key'),
                'planList' => 'trashed',
            ]):
                return new RestoreGroup($this);
            case route('plans.destroy', $this->route('plan', 0)):
                return new Destroy($this);
            case route('plans.group.destroy', [
                'key' => $this->route('key', 'key'),
                'planList' => 'list',
            ]):
                return new DestroyGroup($this);
            case route('plans.create'):
                return new CreateForm($this);
            case route('plans.store'):
                return (new Persistence($this))
                    ->trimFields($this)
                    ->validateRolesToPlan($this);
            case route('plans.update', $this->route('plan', 0)):
                return (new Persistence($this, $this->route('plan', 0)))
                    ->trimFields($this)
                    ->applyAfterValidation($this);
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
