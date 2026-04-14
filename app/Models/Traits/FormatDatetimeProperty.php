<?php

declare(strict_types=1);

namespace App\Models\Traits;

use App\Libraries\Utils\DatetimeFormatter;

trait FormatDatetimeProperty
{
    /**
     * Format datetime properties based on current locale
     *
     * @return string
     */
    protected function getPropertyFormatted($prop)
    {
        return DatetimeFormatter::formatToDate($this->$prop);
    }
}
