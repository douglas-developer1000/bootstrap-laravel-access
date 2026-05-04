<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\User\UserRequest;
use App\Models\User;
use App\Services\PaginatorService;
use App\Services\RoleUserService;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class RoleUserController extends Controller
{
    public function __construct(protected RoleUserService $roleUserSvc)
    {
        // ...
    }

    protected function findUnlinkedRoles(User $user)
    {
        $ids = $user->roles->map(fn(Role $role) => $role->id)->all();
        return Role::whereNotIn('id', $ids);
    }

    public function getRoles(Request $request, PaginatorService $paginator, User $user)
    {
        $query = $this->findUnlinkedRoles($user);
        $search = $paginator->buildSearch($request->only('q'));
        $sort = $paginator->buildSort($request->only('sort'), ['created_at', 'id', 'name']);
        $order = $paginator->buildOrder($request->only('order'));

        if ($search) {
            $search = addcslashes($search, '%_');
            $query = $query->whereLike('name', "%{$search}%");
        }

        $group = $paginator->buildGroup($request->only('group'));
        $roles = $query->orderBy($sort, $order)->paginate(
            perPage: $group,
            columns: ['id', 'name', 'created_at']
        );

        return view('pages.users.attach-roles', ['user' => $user, 'roles' => $roles]);
    }

    public function bindRole(User $user, Role $role)
    {
        $this->roleUserSvc->bindRoleToUser($user, $role);

        return redirect()->route('users.attach.roles', [
            'user' => $user->id,
            ...(request()->query() ?? [])
        ])->with([
            'toastShow' => true,
            'toastMsg' => 'Vinculação executada com sucesso!'
        ]);
    }

    public function unbindRole(User $user, Role $role)
    {
        $this->roleUserSvc->unbindRoleFromUser($user, $role);

        return redirect()->route('users.show', [
            'user' => $user->id
        ])->with([
            'toastShow' => true,
            'toastMsg' => 'Desvinculação executada com sucesso!'
        ]);
    }

    public function bindRoleGroup(UserRequest $request, User $user)
    {
        $this->roleUserSvc->bindRoleGroupToUser($request, $user);

        return redirect()->route('users.attach.roles', [
            'user' => $user->id,
            ...(request()->query() ?? [])
        ])->with([
            'toastShow' => true,
            'toastMsg' => 'Vinculação executada com sucesso!'
        ]);
    }

    public function unbindRoleGroup(UserRequest $request, User $user)
    {
        $this->roleUserSvc->unbindRoleGroupFromUser($request, $user);

        return redirect()->route('users.show', [
            'user' => $user->id
        ])->with([
            'toastShow' => true,
            'toastMsg' => 'Desvinculações executadas com sucesso!'
        ]);
    }
}
