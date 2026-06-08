<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Closure;
use Illuminate\Foundation\Http\FormRequest;
use Override;

abstract class CustomFormRequest extends FormRequest implements LateValidationInterface, BeforeValidationInterface
{
    protected $stopOnFirstFailure = true;

    /**
     * @var Closure[] $afterValidationClosures
     */
    protected $afterValidationClosures = [];

    /**
     * @var array<Closure(FormRequest $formRequest): void> $beforeValidationClosures
     */
    protected $beforeValidationClosures = [];

    abstract protected function pickChecker(): Checker;

    #[Override]
    public function pushAfterValidation(Closure $callback): void
    {
        $this->afterValidationClosures[] = $callback;
    }

    #[Override]
    public function getInput(string $key, $default = NULL): mixed
    {
        return $this->input($key, $default);
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
