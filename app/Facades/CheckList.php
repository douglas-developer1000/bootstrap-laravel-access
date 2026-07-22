<?php

declare(strict_types=1);

namespace App\Facades;

use App\Libraries\Helpers\CheckListHelper;
use Closure;
use Illuminate\Support\Facades\Facade;
use Illuminate\Support\ViewErrorBag;
use Override;

/**
 * @method static bool boxChecked(ViewErrorBag $errors, array $values, Closure $closure)
 *
 * @see CheckListHelper::class
 */
final class CheckList extends Facade
{
    #[Override]
    protected static function getFacadeAccessor()
    {
        return 'CheckList';
    }
}
