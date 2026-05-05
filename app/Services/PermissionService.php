<?php

declare(strict_types=1);

namespace App\Services;

use App\Services\Abstracts\AbstractPaginatorIndex;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Permission\Models\Permission;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Override;

final class PermissionService
{
    public function prepareIndex(Request $request): LengthAwarePaginator
    {
        return (new class extends AbstractPaginatorIndex
        {
            #[Override]
            public function query(Request $request): Builder
            {
                return Permission::query();
            }

            #[Override]
            public function attachQuery(Request $request, Builder $query): Builder
            {
                $search = $this->paginator->buildSearch($request->only('q'));
                if ($search) {
                    $search = addcslashes($search, '%_');
                    return parent::attachQuery($request, $query)->whereLike(
                        'name',
                        "%{$search}%"
                    );
                }
                return parent::attachQuery($request, $query);
            }
        })->prepareIndex(
            $request,
            'created_at',
            'id',
            'name'
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
        collect($ids)->each(fn($id) => Permission::findById($id)->delete());
    }
}
