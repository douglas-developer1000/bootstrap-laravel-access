<?php

declare(strict_types=1);

namespace App\Facades;

use App\Libraries\Helpers\DateFormatterHelper;
use Carbon\Carbon;
use Illuminate\Support\Facades\Facade;
use Override;

/**
 * @method static ?string formatToDate(Carbon|string|null $datetime = null, ?string $timeZone = 'America/Sao_Paulo', bool $timed = false)
 *
 * @see DateFormatterHelper::class
 */
final class DateFormatter extends Facade
{
    #[Override]
    protected static function getFacadeAccessor()
    {
        return 'DateFormatter';
    }
}
