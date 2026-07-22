<?php

declare(strict_types=1);

namespace App\Libraries\Helpers;

final class ListStoragerHelper
{
    public static function getList(string $key): array
    {
        session()->put($key, session()->get($key, []));

        return session()->get($key);
    }

    public static function store(string $key, string|int $value): void
    {
        if (! collect(self::getList($key))->contains($value)) {
            session()->push($key, $value);
        }
    }

    public static function unstore(string $key, string|int $value): void
    {
        $oldList = collect(self::getList($key));
        $newList = $oldList->reject(fn (string|int $neddle) => $neddle === $value);
        session()->put($key, $newList->all());
    }

    public static function clearList(string $key): void
    {
        session()->forget($key);
    }
}
