<?php

declare(strict_types=1);

namespace App\Libraries\Helpers;

use Carbon\Carbon;
use DateTimeZone;

final class DateFormatterHelper
{
    /**
     * Format the datetime to string format
     *
     * TO-DO -> on final of formatToDate method:
     *
     * ```php
     * $locale = App::getLocale();
     * $prefix = $locale === 'pt_BR' ? 'd/m/Y' : 'm/d/Y';
     *
     * return $datetime->format("{$prefix}{$timePart}");
     * ```
     *
     * @param  Carbon|null  $datetime  The datetime to format
     * @param  string|null  $timeZone  The timezone to be defined in the output
     * @param  bool  $timed  Define if time part must be put in the output
     * @return string|null The datetime formatted
     */
    public static function formatToDate(Carbon|string|null $datetime = null, ?string $timeZone = 'America/Sao_Paulo', bool $timed = false): ?string
    {
        if ($datetime === null) {
            return null;
        }
        if (\is_string($datetime)) {
            $datetime = Carbon::parse($datetime, $timeZone);
        }
        $datetime->setTimezone(new DateTimeZone($timeZone));
        $timePart = $timed ? ' H:i:s' : '';

        return $datetime->format("d/m/Y{$timePart}");
    }
}
