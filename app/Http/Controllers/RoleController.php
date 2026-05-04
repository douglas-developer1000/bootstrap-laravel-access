<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\Role\RoleRequest;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Http\Request;
use App\Services\PaginatorService;
use App\Services\RoleService;

final class RoleController extends Controller
{
    public function __construct(
        protected RoleService $svc,
        protected PaginatorService $paginator
    ) {
        // ...
    }

    public function index(Request $request)
    {
        return view('pages.roles.index', [
            'list' => $this->svc->prepareIndex($request)
        ]);
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

    public function edit(Role $role)
    {
        return view('pages.roles.edit', ['role' => $role]);
    }

    public function attach(Request $request, Role $role)
    {
        return view('pages.roles.attach', [
            'role' => $role,
            'list' => $this->svc->prepareRemainPermissionsIndex(
                $request,
                $role
            )
        ]);
    }

    public function store(RoleRequest $request)
    {
        $this->svc->createRole($request->validated('name'));

        return redirect()->route('roles.index')->with([
            'toastShow' => true,
            'toastMsg' => 'Papel criado com sucesso!'
        ]);
    }

    public function update(RoleRequest $request)
    {
        $this->svc->updateRole($request->route('role'), $request->validated('name'));

        return redirect()->route('roles.index')->with([
            'toastShow' => true,
            'toastMsg' => 'Papel editado com sucesso!'
        ]);
    }
    public function destroy(Role $role)
    {
        $this->svc->removeRole($role);

        return redirect()->route(
            'roles.index',
            request()->query() ?? []
        )->with([
            'toastShow' => true,
            'toastMsg' => 'Papel removido com sucesso!'
        ]);
    }

    public function removeGroup(RoleRequest $request)
    {
        $this->svc->removeRoleList($request->validated('remotion'));

        return redirect()->route(
            'roles.index',
            request()->query() ?? []
        )->with([
            'toastShow' => true,
            'toastMsg' => 'Papéis removidos com sucesso!'
        ]);
    }

    public function bind(Role $role, Permission $permission)
    {
        $this->svc->bindPermissionToRole($role, $permission);

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
        $this->svc->unbindPermissionFromRole($role, $permission);

        return redirect()->route('roles.show', [
            'role' => $role->id
        ])->with([
            'toastShow' => true,
            'toastMsg' => 'Desvinculação executada com sucesso!'
        ]);
    }

    public function bindGroup(RoleRequest $request, Role $role)
    {
        $this->svc->bindPermissionGroupToRole(
            $request->validated('attachment'),
            $role
        );

        return redirect()->route('roles.attach', [
            'role' => $role->id
        ])->with([
            'toastShow' => true,
            'toastMsg' => 'Vinculação executada com sucesso!'
        ]);
    }

    public function unbindGroup(RoleRequest $request, Role $role)
    {
        $this->svc->unbindPermissionGroupFromRole(
            $request->validated('detachment'),
            $role
        );

        return redirect()->route('roles.show', [
            'role' => $role->id
        ])->with([
            'toastShow' => true,
            'toastMsg' => 'Desvinculação executada com sucesso!'
        ]);
    }
}
