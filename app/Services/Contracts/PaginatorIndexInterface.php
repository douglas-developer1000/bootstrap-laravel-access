<?php

declare(strict_types=1);

namespace App\Services\Contracts;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;

interface PaginatorIndexInterface
{
    public function prepareIndex(Request $request, string ...$columns): LengthAwarePaginator;

    public function query(Request $request): Builder;

    public function attachQuery(Request $request, Builder $query): Builder;

    /**
     * @return array<int, string>
     */
    public function getSortColumns(): array;
}
