<?php

declare(strict_types=1);

namespace App\Services\Abstracts;

use App\Services\Contracts\PaginatorIndexInterface;
use Illuminate\Database\Eloquent\Builder;
use App\Services\PaginatorService;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

abstract class AbstractPaginatorIndex implements PaginatorIndexInterface
{
    protected PaginatorService $paginator;

    public function __construct()
    {
        $this->paginator = app(PaginatorService::class);
    }

    abstract public function query(Request $request): Builder;

    public function getSortColumns(): array
    {
        return [];
    }

    public function attachQuery(Request $request, Builder $query): Builder
    {
        return $query;
    }

    public function prepareIndex(Request $request, string ...$columns): LengthAwarePaginator
    {
        $sortColumns = $this->getSortColumns() ?: $columns;
        $group = $this->paginator->buildGroup($request->only('group'));
        $sort = $this->paginator->buildSort($request->only('sort'), $sortColumns);
        $order = $this->paginator->buildOrder($request->only('order'));

        return $this->attachQuery(
            $request,
            $this->query($request)
        )->orderBy($sort, $order)->paginate(
            perPage: $group,
            columns: $columns
        );
    }
}
