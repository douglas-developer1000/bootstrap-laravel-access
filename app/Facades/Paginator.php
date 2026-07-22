<?php

declare(strict_types=1);

namespace App\Facades;

use App\Libraries\Helpers\PaginatorHelper;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Facade;
use Override;

/**
 * @method static string|false buildSearch(array $params, $keyTerm = 'q')
 * @method static string buildSort(array $params, array $baseList)
 * @method static string buildOrder(array $params)
 * @method static int|string buildGroup(array $params)
 * @method static string makeHref(string $url, Collection $currentQueryString, $group = null)
 *
 * @see PaginatorHelper::class
 */
final class Paginator extends Facade
{
    #[Override]
    protected static function getFacadeAccessor()
    {
        return 'Paginator';
    }
}
