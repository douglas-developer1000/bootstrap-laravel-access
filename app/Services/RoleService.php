<?php

declare(strict_types=1);

namespace App\Services;

use App\Facades\ListStorager;
use App\Libraries\Enums\RoleNameEnum;
use App\Models\Role;
use App\Services\Abstracts\AbstractPaginatorIndex;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Override;
use Spatie\Permission\Models\Permission;
use stdClass;

final class RoleService
{
    public function prepareIndex(Request $request): LengthAwarePaginator
    {
        return (new class() extends AbstractPaginatorIndex
        {
            public function __construct()
            {
                parent::__construct();
            }

            #[Override]
            public function query(Request $request): Builder
            {
                return Role::getQuery();
            }

            #[Override]
            public function attachQuery(Request $request, Builder $query): Builder
            {
                return $this->filterNoPlan(
                    $request,
                    $this->filterNoUser(
                        $request,
                        $this->filterForPlan(
                            $request,
                            $this->filterSearch(
                                parent::attachQuery($request, $query),
                                $this->paginator->buildSearch($request->only('q')),
                                'name'
                            )
                        )
                    )
                );
            }

            protected function filterNoUser(Request $request, Builder $query): Builder
            {
                return $query->when(
                    $request->boolean('no-user'),
                    fn(Builder $query) => $query->joinSub(
                        Role::whereDoesntHave('users')->select('id'),
                        'roles_no_user',
                        'roles.id',
                        '=',
                        'roles_no_user.id'
                    )
                );
            }

            protected function filterNoPlan(Request $request, Builder $query): Builder
            {
                return $query->when(
                    $request->boolean('no-plan'),
                    fn(Builder $query) => $query->joinSub(
                        Role::whereDoesntHave('plans')
                            ->whereNot('name', RoleNameEnum::SUPER_ADMIN->value)
                            ->select('id'),
                        'roles_no_plan',
                        'roles.id',
                        '=',
                        'roles_no_plan.id'
                    )
                );
            }

            protected function filterForPlan(Request $request, Builder $query): Builder
            {
                return $query->when(
                    $request->boolean('for-plan'),
                    fn(Builder $query) => $query->whereIn(
                        'name',
                        ListStorager::getList('rolesToPlan')
                    )
                );
            }

            #[Override]
            public function getSortColumns(): array
            {
                return [
                    'roles.created_at',
                    'roles.id',
                    'roles.name',
                ];
            }
        })->prepareIndex(
            $request,
            '*'
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

                return Permission::whereDoesntHave('roles', function ($query) use ($role) {
                    $query->where('id', $role->id);
                })->getQuery();
            }

            #[Override]
            public function attachQuery(Request $request, Builder $query): Builder
            {
                return parent::attachQuery($request, $query)->when(
                    $this->paginator->buildSearch($request->only('q')),
                    function (Builder $query, string $search) {
                        $search = addcslashes($search, '%_');

                        return $query->whereLike('name', "%{$search}%");
                    }
                );
            }
        })->prepareIndex(
            $request,
            'id',
            'name',
            'created_at'
        );
    }

    public function createRole(string $name, string $summary): Role
    {
        return Role::create(['name' => $name, 'summary' => $summary]);
    }

    public function bindDescriptions(Role $role, array $descriptions, bool $clear = false): void
    {
        if ($clear) {
            $role->roleDescriptions()->delete();
        }
        $role->roleDescriptions()->createMany(
            collect($descriptions)->map(
                fn(string $description) => ['description' => $description]
            )->all()
        );
    }

    public function updateRole(Role $role, string $name, string $summary): Role
    {
        $role->update(['name' => $name, 'summary' => $summary]);

        return $role;
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

    public function hydrateRole(array $roles): Collection
    {
        $rolesToPlan = collect(ListStorager::getList('rolesToPlan'));

        return collect($roles)->map(function (stdClass $role) use (&$rolesToPlan) {
            return tap($role, function (stdClass $role) use (&$rolesToPlan) {
                $role->inRolesCart = $rolesToPlan->contains($role->name);
            });
        });
    }
}
