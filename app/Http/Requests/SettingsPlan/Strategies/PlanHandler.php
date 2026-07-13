<?php

declare(strict_types=1);

namespace App\Http\Requests\SettingsPlan\Strategies;

use App\Http\Requests\Checker;
use App\Models\Plan;
use App\Models\Role;

final class PlanHandler implements Checker
{
    public function __construct(protected Plan $plan)
    {
        // ...
    }

    public function rules(): array
    {
        return [
            'additionals' => [
                'nullable',
                'bail',
                'array',
                function ($attribute, $value, $fail) {
                    $additionalNameList = collect($value);
                    $qty = $this->countValidRoles($additionalNameList->all());

                    if ($additionalNameList->count() !== $qty) {
                        $fail('Campo inválido');
                    }
                },
            ],
        ];
    }

    protected function countValidRoles(array $additionalNameList): int
    {
        return Role::whereIn('roles.name', $additionalNameList)
            ->join(
                'plan_role',
                'roles.id',
                '=',
                'plan_role.role_id'
            )->where([
                'plan_role.plan_id' => $this->plan->id,
                'plan_role.additional' => 1,
            ])
            ->count('roles.id');
    }

    public function messages(): array
    {
        return [
            'additionals.array' => 'Campo inválido',
        ];
    }
}
