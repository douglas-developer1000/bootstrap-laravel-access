<?php

declare(strict_types=1);

namespace App\Services;

use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use App\Services\Abstracts\AbstractPaginatorIndex;
use Illuminate\Database\Eloquent\Builder;
use Override;

final class RoleService
{
    public function prepareIndex(Request $request): LengthAwarePaginator
    {
        return (new class extends AbstractPaginatorIndex
        {
            #[Override]
            public function query(Request $request): Builder
            {
                return Role::query();
            }

            #[Override]
            public function attachQuery(Request $request, Builder $query): Builder
            {
                $search = $this->paginator->buildSearch($request->only('q'));
                if ($search) {
                    $search = addcslashes($search, '%_');
                    return parent::attachQuery(
                        $request,
                        $query
                    )->whereLike('name', "%{$search}%");
                }
                return parent::attachQuery($request, $query);
            }
        })->prepareIndex(
            $request,
            'id',
            'name',
            'created_at'
        );
    }

    public function prepareRemainPermissionsIndex(Request $request, Role $role): LengthAwarePaginator
    {
        return (new class($role) extends AbstractPaginatorIndex
        {
            public function __construct(protected Role $role)
            {
                parent::__construct();
            }

            #[Override]
            public function query(Request $request): Builder
            {
                $role = $this->role;
                /** @var Builder $query */
                $query = Permission::whereDoesntHave('roles', function ($query) use ($role) {
                    $query->where('id', $role->id);
                });
                return $query;
            }

            #[Override]
            public function attachQuery(Request $request, Builder $query): Builder
            {
                $search = $this->paginator->buildSearch($request->only('q'));
                if ($search) {
                    $search = addcslashes($search, '%_');
                    return parent::attachQuery(
                        $request,
                        $query
                    )->whereLike('name', "%{$search}%");
                }
                return parent::attachQuery($request, $query);
            }
        })->prepareIndex(
            $request,
            'id',
            'name',
            'created_at'
        );
    }

    public function createRole(string $name)
    {
        Role::create(['name' => $name]);
    }

    public function updateRole(string|int $id, string $name): void
    {
        /** @var Role $role */
        $role = Role::findOrFail($id);
        $role->update(['name' => $name]);
    }

    public function removeRole(Role $role): void
    {
        $role->delete();
    }

    public function bindPermissionToRole(Role $role, Permission $permission)
    {
        $role->givePermissionTo($permission);
    }

    public function unbindPermissionFromRole(Role $role, Permission $permission)
    {
        $role->revokePermissionTo($permission);
    }

    public function removeRoleList(array $ids): void
    {
        // This remotion occurs by each model, because
        // the spatie permissions package removes the roles
        // from the cache this way
        collect($ids)->each(fn($id) => Role::findById($id)->delete())->all();
    }

    public function bindPermissionGroupToRole(array $ids, Role $role): void
    {
        $permissions = Permission::whereIn('id', $ids)->get('name')->pluck('name')->all();
        $role->givePermissionTo(...$permissions);
    }

    public function unbindPermissionGroupFromRole(array $ids, Role $role)
    {
        $permissions = Permission::whereIn('id', $ids)->get('name')->pluck('name')->all();
        $role->revokePermissionTo($permissions);
    }
}
