<?php

declare(strict_types=1);

namespace App\Libraries\Utils;

final class TokenBuilder
{
    private function __construct()
    {
        // ...
    }

    public static function build(): string
    {
        return bin2hex(random_bytes(20));
    }
}
