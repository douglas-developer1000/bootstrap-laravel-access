<?php

declare(strict_types=1);

namespace App\Http\Requests\Role\Strategies;

use App\Http\Requests\BeforeValidationInterface;
use App\Http\Requests\Checker;
use App\Services\ListSelectorService;
use Spatie\Permission\Models\Role;

final class Marking implements Checker
{
    public function __construct(
        protected Role $role,
        BeforeValidationInterface $before,
        bool $enter = false,
    ) {
        $list = collect(app(ListSelectorService::class)->getList('rolesToPlan'));
        $before->pushBeforeValidation(function ($formRequest) use (&$list, $role, $enter) {
            $invalid = $enter && $list->contains($role->name);
            if ($invalid) {
                abort(403);
            }
            $invalid = ! $enter && ! $list->contains($role->name);
            if ($invalid) {
                abort(403);
            }
        });
    }

    public function rules(): array
    {
        return [];
    }

    public function messages(): array
    {
        return [];
    }
}
