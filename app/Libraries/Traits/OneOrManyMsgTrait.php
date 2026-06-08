<?php

declare(strict_types=1);

namespace App\Libraries\Traits;

trait OneOrManyMsgTrait
{
    protected function makeSizeMsg(int $value, string $singular, string $plural)
    {
        $noun = ngettext($singular, $plural, $value);
        return "{$value} {$noun}";
    }
}
