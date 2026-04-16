<?php

declare(strict_types=1);

namespace App\Libraries\Utils;

use Illuminate\Support\Str;

final class PhoneFormatter
{
    private function __construct()
    {
        // ...
    }

    /**
     * Keep only digits from phone
     */
    public static function clear(?string $phone): ?string
    {
        if (!$phone) {
            return $phone;
        }
        return Str::of($phone)->replaceMatches('|[^\d]|', '')->toString();
    }

    /**
     * Remove all separators (whitespaces and hyphens) from phone
     */
    public static function chopSeparators(?string $phone): ?string
    {
        if (!$phone) {
            return $phone;
        }
        return Str::of($phone)->replaceMatches('|[\(\-\s\)]|', '')->toString();
    }

    public static function toView(?string $value): string
    {
        if ($value === NULL) {
            return 'N/A';
        }
        $phone = Str::of($value);
        if ($phone->length() === 9) {
            return $phone->replaceMatches('|^(\d{5})(\d+)$|', '$1 $2')->toString();
        }
        return $phone->replaceMatches('|^(\d{2})(\d{5})(\d+)$|', '($1) $2-$3')->toString();
    }
}
