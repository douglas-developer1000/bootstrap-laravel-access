<?php

declare(strict_types=1);

namespace App\Libraries\Utils;

// use Illuminate\Support\Facades\App;
use Carbon\Carbon;
use \DateTimeZone;

final class DatetimeFormatter
{
    private function __construct()
    {
        // ...
    }

    /**
     * Format the datetime to string format
     *
     * @param Carbon|null $datetime The datetime to format
     * @param string|null $timeZone The timezone to be defined in the output
     * @param bool $timed Define if time part must be put in the output
     * @return string|null The datetime formatted
     */
    public static function formatToDate(?Carbon $datetime = NULL, ?string $timeZone = NULL, bool $timed = false): ?string
    {
        if ($datetime === NULL) {
            return NULL;
        }
        if ($timeZone !== NULL) {
            $datetime->setTimezone(new DateTimeZone($timeZone));
        }
        $timePart = $timed ? ' H:i:s' : '';

        // $locale = App::getLocale();
        // if ($locale === 'pt_BR') {
        return $datetime->format('d/m/Y' . $timePart);
        // }
        // return $datetime->format('m/d/Y' . $timePart);
    }
}
