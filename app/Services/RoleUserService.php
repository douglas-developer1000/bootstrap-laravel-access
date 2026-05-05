<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\User;
use App\Services\Abstracts\AbstractPaginatorIndex;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Illuminate\Pagination\LengthAwarePaginator;
use Override;

final class RoleUserService
{
    public function prepareRemainRoleIndex(Request $request, User $user): LengthAwarePaginator
    {
        return (new class($user) extends AbstractPaginatorIndex
        {
            public function __construct(protected User $user)
            {
                return parent::__construct();
            }

            #[Override]
            public function query(Request $request): Builder
            {
                $ids = $this->user->roles->map(fn(Role $role) => $role->id)->all();
                /** @var Builder $query */
                $query = Role::whereNotIn('id', $ids);
                return $query;
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
            'id',
            'name',
            'created_at'
        );
    }
    public function bindRoleToUser(User $user, Role $role): void
    {
        $user->assignRole($role);
    }

    public function unbindRoleFromUser(User $user, Role $role): void
    {
        $user->removeRole($role);
    }

    public function bindRoleGroupToUser(Request $request, User $user): void
    {
        $roles = Role::whereIn(
            'id',
            $request->validated('attachment')
        )->get('name')->pluck('name')->all();

        $user->assignRole(...$roles);
    }

    public function unbindRoleGroupFromUser(Request $request, User $user): void
    {
        $roles = Role::whereIn(
            'id',
            $request->validated('detachment')
        )->get('name')->pluck('name')->all();

        $user->removeRole(...$roles);
    }
}
