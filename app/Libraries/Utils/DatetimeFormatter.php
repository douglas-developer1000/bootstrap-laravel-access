<?php

namespace App\Libraries\Utils;

use Carbon\Carbon;
use Illuminate\Support\Facades\App;

final class DatetimeFormatter
{
    private function __construct()
    {
        // ...
    }

    public static function formatToDate(?Carbon $datetime): ?string
    {
        if (is_null($datetime)) {
            return $datetime;
        }
        $locale = App::getLocale();
        // if ($locale === 'pt_BR') {
        return $datetime->format('d/m/Y');
        // }
        // return $datetime->format('m/d/Y');
    }
}
