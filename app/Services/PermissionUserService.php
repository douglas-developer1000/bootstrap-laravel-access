<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\User;
use App\Services\Abstracts\AbstractPaginatorIndex;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Illuminate\Pagination\LengthAwarePaginator;
use Override;

final class PermissionUserService
{
    public function prepareRemainPermissionIndex(Request $request, User $user): LengthAwarePaginator
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
                $ids = $this->user->getAllPermissions()->map(
                    fn(Permission $perm) => $perm->id
                )->all();
                $eloquentQuery = Permission::whereNotIn('id', $ids);

                return $eloquentQuery->getQuery();
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

    public function bindDirectPermissionToUser(User $user, Permission $permission)
    {
        $user->givePermissionTo($permission);
    }

    public function unbindDirectPermissionToUser(User $user, Permission $permission)
    {
        $user->revokePermissionTo($permission);
    }

    public function bindDirectPermissionGroupToUser(Request $request, User $user)
    {
        $permissions = Permission::whereIn(
            'id',
            $request->validated('attachment')
        )->get('name')->pluck('name')->all();

        $user->givePermissionTo(...$permissions);
    }

    public function unbindDirectPermissionGroupToUser(Request $request, User $user)
    {
        $permissions = Permission::whereIn(
            'id',
            $request->validated('detachment')
        )->get('name')->pluck('name')->all();

        $user->revokePermissionTo($permissions);
    }
}
