<?php

declare(strict_types=1);

namespace App\Libraries\Traits;

trait BuildTokenTrait
{
    public static function buildToken(): string
    {
        return bin2hex(random_bytes(20));
    }
}
