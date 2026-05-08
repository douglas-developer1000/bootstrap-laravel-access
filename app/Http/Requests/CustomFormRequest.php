<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

abstract class CustomFormRequest extends FormRequest
{
    protected $stopOnFirstFailure = true;

    abstract protected function pickChecker(): Checker;
}
