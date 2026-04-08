<?php

namespace App\Http\Controllers;

use App\Http\Requests\Role\RoleRequest;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function index()
    {
        $list = Role::orderBy('created_at')->paginate(3, ['id', 'name']);

        return view('pages.roles.index', ['list' => $list]);
    }

    public function show(Role $role)
    {
        return view('pages.roles.show', [
            'role' => $role,
            'permissions' => $role->permissions
        ]);
    }

    public function create()
    {
        return view('pages.roles.create');
    }

    public function store(RoleRequest  $request)
    {
        Role::create(['name' => $request->validated('name')]);
        return redirect()->route('roles.index')->with([
            'toastShow' => true,
            'toastMsg' => 'Papel criado com sucesso!'
        ]);
    }

    public function edit(Role $role)
    {
        return view('pages.roles.edit', ['role' => $role]);
    }
    public function update(RoleRequest $request)
    {
        $id = $request->route('role');
        $role = Role::findOrFail($id);
        $role->update([
            'name' => $request->validated('name')
        ]);
        return redirect()->route('roles.index')->with([
            'toastShow' => true,
            'toastMsg' => 'Papel editado com sucesso!'
        ]);
    }
    public function destroy(Role $role)
    {
        $role->delete();

        return redirect()->route(
            'roles.index',
            request()->query() ?? []
        )->with([
            'toastShow' => true,
            'toastMsg' => 'Papel removido com sucesso!'
        ]);
    }

    protected function findUnlinkedPermissions(Role $role)
    {
        return Permission::whereDoesntHave('roles', function ($query) use ($role) {
            $query->where('id', $role->id);
        });
    }

    public function attach(Role $role)
    {
        $permissions = $this->findUnlinkedPermissions($role)->paginate(3, ['id', 'name']);

        return view('pages.roles.attach', ['role' => $role, 'list' => $permissions]);
    }

    public function bind(Role $role, Permission $permission)
    {
        $role->givePermissionTo($permission->name);

        return redirect()->route('roles.attach', [
            'role' => $role->id,
            ...(request()->query() ?? [])
        ])->with([
            'toastShow' => true,
            'toastMsg' => 'Vinculação executada com sucesso!'
        ]);
    }

    public function unbind(Role $role, Permission $permission)
    {
        $role->revokePermissionTo($permission->name);
        return redirect()->route('roles.show', [
            'role' => $role->id
        ])->with([
            'toastShow' => true,
            'toastMsg' => 'Desvinculação executada com sucesso!'
        ]);
    }
}
