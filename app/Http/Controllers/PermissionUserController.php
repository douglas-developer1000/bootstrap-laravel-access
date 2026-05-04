<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\User\UserRequest;
use Illuminate\Http\Request;
use App\Models\User;
use App\Services\PaginatorService;
use App\Services\PermissionUserService;
use Spatie\Permission\Models\Permission;

final class PermissionUserController extends Controller
{
    public function __construct(protected PermissionUserService $svc)
    {
        // ...
    }

    protected function findUnlinkedPermissions(User $user)
    {
        $ids = $user->getAllPermissions()->map(fn(Permission $perm) => $perm->id)->all();
        return Permission::whereNotIn('id', $ids);
    }

    public function getDirectPermissions(Request $request, PaginatorService $paginator, User $user)
    {
        $query = $this->findUnlinkedPermissions($user);
        $search = $paginator->buildSearch($request->only('q'));
        $sort = $paginator->buildSort($request->only('sort'), ['created_at', 'id', 'name']);
        $order = $paginator->buildOrder($request->only('order'));

        if ($search) {
            $search = addcslashes($search, '%_');
            $query = $query->whereLike('name', "%{$search}%");
        }

        $group = $paginator->buildGroup($request->only('group'));
        $permissions = $query->orderBy($sort, $order)->paginate(
            perPage: $group,
            columns: ['id', 'name', 'created_at']
        );

        return view('pages.users.attach-permissions', ['user' => $user, 'permissions' => $permissions]);
    }

    public function bindDirectPermission(User $user, Permission $permission)
    {
        $this->svc->bindDirectPermissionToUser($user, $permission);

        return redirect()->route('users.attach.permissions', [
            'user' => $user->id,
            ...(request()->query() ?? [])
        ])->with([
            'toastShow' => true,
            'toastMsg' => 'Vinculação executada com sucesso!'
        ]);
    }

    public function unbindDirectPermission(User $user, Permission $permission)
    {
        $this->svc->unbindDirectPermissionToUser($user, $permission);
        $user->revokePermissionTo($permission);

        return redirect()->route('users.show', [
            'user' => $user->id
        ])->with([
            'toastShow' => true,
            'toastMsg' => 'Desvinculação executada com sucesso!'
        ]);
    }

    public function bindDirectPermissionGroup(UserRequest $request, User $user)
    {
        $this->svc->bindDirectPermissionGroupToUser($request, $user);

        return redirect()->route('users.attach.permissions', [
            'user' => $user->id,
            ...(request()->query() ?? [])
        ])->with([
            'toastShow' => true,
            'toastMsg' => 'Vinculações executadas com sucesso!'
        ]);
    }

    public function unbindDirectPermissionGroup(UserRequest $request, User $user)
    {
        $this->svc->unbindDirectPermissionGroupToUser($request, $user);

        return redirect()->route('users.show', [
            'user' => $user->id
        ])->with([
            'toastShow' => true,
            'toastMsg' => 'Desvinculações executadas com sucesso!'
        ]);
    }
}
