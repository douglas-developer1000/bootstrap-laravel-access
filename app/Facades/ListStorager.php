<?php

declare(strict_types=1);

namespace App\Facades;

use App\Libraries\Helpers\ListStoragerHelper;
use Illuminate\Support\Facades\Facade;
use Override;

/**
 * @method static array getList(string $key)
 * @method static void store(string $key, string|int $value)
 * @method static void unstore(string $key, string|int $value)
 * @method static void clearList(string $key)
 *
 * @see ListStoragerHelper::class
 */
final class ListStorager extends Facade
{
    #[Override]
    protected static function getFacadeAccessor()
    {
        return 'ListStorager';
    }
}
