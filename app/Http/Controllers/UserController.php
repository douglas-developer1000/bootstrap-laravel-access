<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\UserRequest;
use Illuminate\Http\Request;
use App\Libraries\Utils\Paginator;
use App\Models\User;
use Illuminate\Contracts\Auth\Guard;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $group = Paginator::buildGroup($request->only('group'));
        $nameSearch = Paginator::buildSearch($request->only('name'), 'name');
        $sort = Paginator::buildSort($request->only('sort'), ['created_at', 'id', 'name']);
        $order = Paginator::buildOrder($request->only('order'));

        $query = User::query();
        if ($nameSearch) {
            $nameSearch = addcslashes($nameSearch, '%_');
            $query = $query->whereLike('name', "%{$nameSearch}%");
        }
        /** @var Guard $auth */
        $auth = auth();

        $list = $query->whereKeyNot($auth->id())->orderBy($sort, $order)->paginate(
            perPage: $group,
            columns: ['id', 'name', 'email', 'created_at']
        );

        return view('pages.users.index', ['list' => $list]);
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        return view('pages.users.show', [
            'user' => $user,
            'roles' => $user->roles,
            'permissions' => $user->getDirectPermissions()
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        return view('pages.users.edit', ['user' => $user]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UserRequest $request)
    {
        $id = $request->route('user');
        $user = User::findOrFail($id);
        $user->update([
            'name' => $request->validated('name')
        ]);
        return redirect()->route('users.index')->with([
            'toastShow' => true,
            'toastMsg' => 'Usuário editado com sucesso!'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    protected function findUnlinkedRoles(User $user)
    {
        $ids = $user->roles->map(fn(Role $role) => $role->id)->all();
        return Role::whereNotIn('id', $ids);
    }

    public function attachRoles(Request $request, User $user)
    {
        $query = $this->findUnlinkedRoles($user);
        $search = Paginator::buildSearch($request->only('q'));
        $sort = Paginator::buildSort($request->only('sort'), ['created_at', 'id', 'name']);
        $order = Paginator::buildOrder($request->only('order'));

        if ($search) {
            $search = addcslashes($search, '%_');
            $query = $query->whereLike('name', "%{$search}%");
        }

        $group = Paginator::buildGroup($request->only('group'));
        $roles = $query->orderBy($sort, $order)->paginate(
            perPage: $group,
            columns: ['id', 'name', 'created_at']
        );

        return view('pages.users.attach-roles', ['user' => $user, 'roles' => $roles]);
    }

    public function bindRole(User $user, Role $role)
    {
        $user->assignRole($role);

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
        $user->removeRole($role);

        return redirect()->route('users.show', [
            'user' => $user->id
        ])->with([
            'toastShow' => true,
            'toastMsg' => 'Desvinculação executada com sucesso!'
        ]);
    }

    protected function findUnlinkedPermissions(User $user)
    {
        $ids = $user->getAllPermissions()->map(fn(Permission $perm) => $perm->id)->all();
        return Permission::whereNotIn('id', $ids);
    }

    public function attachDirectPermissions(Request $request, User $user)
    {
        $query = $this->findUnlinkedPermissions($user);
        $search = Paginator::buildSearch($request->only('q'));
        $sort = Paginator::buildSort($request->only('sort'), ['created_at', 'id', 'name']);
        $order = Paginator::buildOrder($request->only('order'));

        if ($search) {
            $search = addcslashes($search, '%_');
            $query = $query->whereLike('name', "%{$search}%");
        }

        $group = Paginator::buildGroup($request->only('group'));
        $permissions = $query->orderBy($sort, $order)->paginate(
            perPage: $group,
            columns: ['id', 'name', 'created_at']
        );

        return view('pages.users.attach-permissions', ['user' => $user, 'permissions' => $permissions]);
    }

    public function bindDirectPermission(User $user, Permission $permission)
    {
        $user->givePermissionTo($permission);

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
        $user->revokePermissionTo($permission);

        return redirect()->route('users.show', [
            'user' => $user->id
        ])->with([
            'toastShow' => true,
            'toastMsg' => 'Desvinculação executada com sucesso!'
        ]);
    }
}
