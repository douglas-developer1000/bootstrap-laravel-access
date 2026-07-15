<?php

declare(strict_types=1);

namespace App\Libraries\Traits;

use BackedEnum;

/**
 * @method static BackedEnum[] cases()
 */
trait EnumExceptTrait
{
    /**
     * @return BackedEnum[]
     */
    public static function casesExcept(BackedEnum ...$enumList): array
    {
        $enumCollection = collect($enumList);

        return collect(self::cases())->filter(
            fn ($item) => ! $enumCollection->contains(fn ($neddle) => $neddle === $item)
        )->all();
    }
}
