<?php

declare(strict_types=1);

namespace App\Http\Requests\Plan\Strategies;

use App\Facades\ListStorager;
use App\Http\Requests\BeforeValidationInterface;
use App\Http\Requests\Checker;
use Illuminate\Foundation\Http\FormRequest;

final class CreateForm implements Checker
{
    public function __construct(BeforeValidationInterface $before)
    {
        $list = collect(ListStorager::getList('rolesToPlan'));
        $before->pushBeforeValidation(function (FormRequest $formRequest) use (&$list) {
            if ($list->isEmpty()) {
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
