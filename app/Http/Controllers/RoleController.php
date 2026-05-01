<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\Role\RoleRequest;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Http\Request;
use App\Libraries\Utils\Paginator;

final class RoleController extends Controller
{
    public function index(Request $request)
    {
        $group = Paginator::buildGroup($request->only('group'));
        $search = Paginator::buildSearch($request->only('q'));
        $sort = Paginator::buildSort($request->only('sort'), ['created_at', 'id', 'name']);
        $order = Paginator::buildOrder($request->only('order'));

        $query = Role::query();
        if ($search) {
            $search = addcslashes($search, '%_');
            $query = $query->whereLike('name', "%{$search}%");
        }
        $list = $query->orderBy($sort, $order)->paginate(
            perPage: $group,
            columns: ['id', 'name', 'created_at']
        );

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

    public function store(RoleRequest $request)
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

    public function attach(Request $request, Role $role)
    {
        $query = $this->findUnlinkedPermissions($role);
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

    public function removeGroup(RoleRequest $request)
    {
        $remotions = collect($request->validated('remotion'))->map(
            fn($val) => \intval($val)
        )->all();
        Role::whereIn('id', $remotions)->delete();

        return redirect()->route(
            'roles.index',
            request()->query() ?? []
        )->with([
            'toastShow' => true,
            'toastMsg' => 'Papéis removidos com sucesso!'
        ]);
    }

    public function bindGroup(RoleRequest $request, Role $role)
    {
        $permissions = Permission::whereIn(
            'id',
            $request->validated('attachment')
        )->get('name')->pluck('name')->all();
        $role->givePermissionTo(...$permissions);

        return redirect()->route('roles.attach', [
            'role' => $role->id
        ])->with([
            'toastShow' => true,
            'toastMsg' => 'Vinculação executada com sucesso!'
        ]);
    }

    public function unbindGroup(RoleRequest $request, Role $role)
    {
        $permissions = Permission::whereIn(
            'id',
            $request->validated('detachment')
        )->get('name')->pluck('name')->all();
        $role->revokePermissionTo($permissions);

        return redirect()->route('roles.show', [
            'role' => $role->id
        ])->with([
            'toastShow' => true,
            'toastMsg' => 'Desvinculação executada com sucesso!'
        ]);
    }
}
