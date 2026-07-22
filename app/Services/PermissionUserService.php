<?php

declare(strict_types=1);

namespace App\Services;

use App\Facades\Paginator;
use App\Models\User;
use App\Services\Abstracts\AbstractPaginatorIndex;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Override;
use Spatie\Permission\Models\Permission;

final class PermissionUserService
{
    public function prepareRemainPermissionIndex(Request $request, User $user): LengthAwarePaginator
    {
        return (new class($user) extends AbstractPaginatorIndex
        {
            public function __construct(protected User $user)
            {
                // ...
            }

            #[Override]
            public function query(Request $request): Builder
            {
                $ids = $this->user->getAllPermissions()->map(
                    fn (Permission $perm) => $perm->id
                )->all();

                return Permission::whereNotIn('id', $ids)->getQuery();
            }

            #[Override]
            public function attachQuery(Request $request, Builder $query): Builder
            {
                return parent::attachQuery($request, $query)
                    ->when(
                        Paginator::buildSearch($request->only('q')),
                        function (Builder $query, string $search) {
                            $search = addcslashes($search, '%_');

                            return $query->whereLike(
                                'name',
                                "%{$search}%"
                            );
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
