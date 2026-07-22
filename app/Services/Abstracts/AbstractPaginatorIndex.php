<?php

declare(strict_types=1);

namespace App\Services\Abstracts;

use App\Facades\Paginator;
use App\Services\Contracts\PaginatorIndexInterface;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

abstract class AbstractPaginatorIndex implements PaginatorIndexInterface
{
    abstract public function query(Request $request): Builder;

    public function getSortColumns(): array
    {
        return [];
    }

    public function attachQuery(Request $request, Builder $query): Builder
    {
        return $query;
    }

    protected function filterSoftDelete(Builder $query, bool $trashed = false): Builder
    {
        return $query
            ->when(
                $trashed,
                fn (Builder $query) => $query->whereNotNull('deleted_at')
            )
            ->when(
                ! $trashed,
                fn (Builder $query) => $query->whereNull('deleted_at')
            );
    }

    protected function filterSearch(Builder $query, string|false $search, string $column): Builder
    {
        return $query
            ->when(
                $search,
                function (Builder $query, string $needle) use ($column) {
                    $needle = addcslashes($needle, '%_');

                    return $query->whereLike(
                        $column,
                        "%{$needle}%"
                    );
                }
            );
    }

    public function prepareIndex(Request $request, string ...$columns): LengthAwarePaginator
    {
        $sortColumns = $this->getSortColumns() ?: $columns;
        $group = Paginator::buildGroup($request->only('group'));
        $sort = Paginator::buildSort($request->only('sort'), $sortColumns);
        $order = Paginator::buildOrder($request->only('order'));

        return $this->attachQuery(
            $request,
            $this->query($request)
        )->orderBy($sort, $order)->paginate(
            perPage: $group,
            columns: $columns
        );
    }
}
