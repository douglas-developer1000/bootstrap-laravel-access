<?php

declare(strict_types=1);

namespace App\Services;

final class ListSelectorService
{
    public function getList(string $key): array
    {
        session()->put($key, session()->get($key, []));

        return session()->get($key);
    }

    public function store(string $key, string|int $value): void
    {
        if (! collect($this->getList($key))->contains($value)) {
            session()->push($key, $value);
        }
    }

    public function unstore(string $key, string|int $value): void
    {
        $oldList = collect($this->getList($key));
        $newList = $oldList->reject(fn (string|int $neddle) => $neddle === $value);
        session()->put($key, $newList->all());
    }

    public function clearList(string $key): void
    {
        session()->forget($key);
    }
}
