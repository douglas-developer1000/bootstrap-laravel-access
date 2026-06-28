<?php

declare(strict_types=1);

namespace App\Services;

use App\Services\Abstracts\AbstractPaginatorIndex;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Override;
use Spatie\Permission\Models\Permission;

final class PermissionService
{
    public function prepareIndex(Request $request): LengthAwarePaginator
    {
        return (new class() extends AbstractPaginatorIndex
        {
            #[Override]
            public function query(Request $request): Builder
            {
                return Permission::getQuery();
            }

            #[Override]
            public function attachQuery(Request $request, Builder $query): Builder
            {
                return $this->filterSearch(
                    parent::attachQuery($request, $query),
                    $this->paginator->buildSearch($request->only('q')),
                    'name'
                );
            }

            #[Override]
            public function getSortColumns(): array
            {
                return [
                    'created_at',
                    'id',
                    'name',
                ];
            }
        })->prepareIndex(
            $request,
            '*'
        );
    }

    public function createPermission(string $name)
    {
        Permission::create(['name' => $name]);
    }

    public function updatePermission(Permission $permission, string $name): void
    {
        $permission->update(['name' => $name]);
    }

    public function removePermission(Permission $permission): void
    {
        $permission->delete();
    }

    public function removePermissionList(array $ids): void
    {
        // This remotion occurs by each model, because
        // the spatie permissions package removes the permissions
        // from the cache this way
        collect($ids)->each(fn ($id) => Permission::findById($id)->delete());
    }
}
