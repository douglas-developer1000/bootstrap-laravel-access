<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\User\UserRequest;
use App\Models\User;
use App\Services\RoleUserService;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class RoleUserController extends Controller
{
    public function __construct(protected RoleUserService $roleUserSvc)
    {
        // ...
    }

    public function getRoles(Request $request, User $user)
    {
        return view('pages.users.attach-roles', [
            'user' => $user,
            'roles' => $this->roleUserSvc->prepareRemainRoleIndex(
                $request,
                $user
            )
        ]);
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

        return redirect()->back()->with([
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
