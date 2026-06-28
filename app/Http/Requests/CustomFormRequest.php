<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Closure;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;
use Override;

abstract class CustomFormRequest extends FormRequest implements BeforeValidationInterface, LateValidationInterface
{
    protected $stopOnFirstFailure = true;

    /**
     * @var Closure[]
     */
    protected $afterValidationClosures = [];

    /**
     * @var array<Closure(FormRequest): void>
     */
    protected $beforeValidationClosures = [];

    abstract protected function pickChecker(): Checker;

    #[Override]
    public function pushAfterValidation(Closure $callback): void
    {
        $this->afterValidationClosures[] = function (Validator $validator, ...$remain) use (&$callback) {
            if ($validator->errors()->isEmpty()) {
                $callback($validator, ...$remain);
            }
        };
    }

    #[Override]
    public function getInput(string $key, $default = null): mixed
    {
        return $this->input($key, $default);
    }

    #[Override]
    public function getRoute(string $key): mixed
    {
        return $this->route($key);
    }

    public function after(): array
    {
        return $this->afterValidationClosures;
    }

    #[Override]
    public function pushBeforeValidation(Closure $callback): void
    {
        $this->beforeValidationClosures[] = $callback;
    }

    #[Override]
    protected function prepareForValidation()
    {
        foreach ($this->beforeValidationClosures as $callback) {
            $callback($this);
        }
    }
}
