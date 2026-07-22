<?php

declare(strict_types=1);

namespace App\Libraries\Helpers;

use Closure;
use Illuminate\Support\ViewErrorBag;

final class CheckListHelper
{
    /**
     * @param  array{initial: bool, old: array}  $values
     * @param  Closure(mixed $value): bool  $closure
     */
    public static function boxChecked(ViewErrorBag $errors, array $values, Closure $closure): bool
    {
        if ($errors->isEmpty()) {
            return $values['initial'];
        }

        return collect($values['old'])->contains($closure);
    }
}
