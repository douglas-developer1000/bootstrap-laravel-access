<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Facades\ListStorager;
use App\Http\Requests\Role\RoleRequest;
use App\Models\Role;
use App\Services\RoleService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Spatie\Permission\Models\Permission;

final class RoleController extends Controller
{
    public function __construct(protected RoleService $svc)
    {
        // ...
    }

    public function index(Request $request)
    {
        return view('pages.roles.index', [
            'list' => $this->svc->prepareIndex($request),
            'models' => fn (LengthAwarePaginator $pagination) => (
                $this->svc->hydrateRole($pagination->all())
            ),
            'filterVisibility' => [
                'for-plan' => (
                    ! $request->boolean('no-user') &&
                    ! $request->boolean('no-plan')
                ),
                'no-user' => (
                    ! $request->boolean('for-plan') &&
                    ! $request->boolean('no-plan')
                ),
                'no-plan' => (
                    ! $request->boolean('for-plan') &&
                    ! $request->boolean('no-user')
                ),
            ],
        ]);
    }

    public function show(Role $role)
    {
        return view('pages.roles.show', [
            'role' => $role,
            'permissions' => $role->permissions,
        ]);
    }

    public function create()
    {
        return view('pages.roles.persistence', [
            'descriptions' => [],
        ]);
    }

    public function edit(Role $role)
    {
        return view('pages.roles.persistence', [
            'title' => 'Editar papel',
            'action' => route('roles.update', $role->id),
            'method' => 'PUT',
            'old' => [
                'name' => $role->name,
                'summary' => $role->summary,
            ],
            'descriptions' => $role->roleDescriptions()->pluck('description')->all(),
        ]);
    }

    public function attach(Request $request, Role $role)
    {
        return view('pages.roles.attach', [
            'role' => $role,
            'list' => $this->svc->prepareRemainPermissionsIndex(
                $request,
                $role
            ),
        ]);
    }

    public function store(RoleRequest $request)
    {
        $this->svc->bindDescriptions(
            $this->svc->createRole(
                name: $request->input('name'),
                summary: $request->input('summary')
            ),
            $request->input('descriptions', []),
        );

        return redirect()->route('roles.index')->with([
            'toastShow' => true,
            'toastMsg' => 'Papel criado com sucesso!',
        ]);
    }

    public function update(RoleRequest $request, Role $role)
    {
        $this->svc->bindDescriptions(
            role: $this->svc->updateRole(
                role: $role,
                name: $request->input('name'),
                summary: $request->input('summary')
            ),
            descriptions: $request->input('descriptions', []),
            clear: true
        );

        return redirect()->route('roles.index')->with([
            'toastShow' => true,
            'toastMsg' => 'Papel editado com sucesso!',
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
            'toastMsg' => 'Papel removido com sucesso!',
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
            'toastMsg' => 'Papéis removidos com sucesso!',
        ]);
    }

    public function bind(Role $role, Permission $permission)
    {
        $this->svc->bindPermissionToRole($role, $permission);

        return redirect()->route('roles.attach', [
            'role' => $role->id,
            ...(request()->query() ?? []),
        ])->with([
            'toastShow' => true,
            'toastMsg' => 'Vinculação executada com sucesso!',
        ]);
    }

    public function unbind(Role $role, Permission $permission)
    {
        $this->svc->unbindPermissionFromRole($role, $permission);

        return redirect()->route('roles.show', [
            'role' => $role->id,
        ])->with([
            'toastShow' => true,
            'toastMsg' => 'Desvinculação executada com sucesso!',
        ]);
    }

    public function bindGroup(RoleRequest $request, Role $role)
    {
        $this->svc->bindPermissionGroupToRole(
            $request->validated('attachment'),
            $role
        );

        return redirect()->route('roles.attach', [
            'role' => $role->id,
        ])->with([
            'toastShow' => true,
            'toastMsg' => 'Vinculação executada com sucesso!',
        ]);
    }

    public function unbindGroup(RoleRequest $request, Role $role)
    {
        $this->svc->unbindPermissionGroupFromRole(
            $request->validated('detachment'),
            $role
        );

        return redirect()->route('roles.show', [
            'role' => $role->id,
        ])->with([
            'toastShow' => true,
            'toastMsg' => 'Desvinculação executada com sucesso!',
        ]);
    }

    public function markRole(RoleRequest $request, Role $role): RedirectResponse
    {
        ListStorager::store('rolesToPlan', $role->name);

        return redirect()->back()->with([
            'toastShow' => true,
            'toastMsg' => "Papel {$role->name} marcado com sucesso!",
        ]);
    }

    public function unmarkRole(RoleRequest $request, Role $role): RedirectResponse
    {
        ListStorager::unstore('rolesToPlan', $role->name);
        $list = collect(ListStorager::getList('rolesToPlan'));
        if ($list->isEmpty() && ! $request->boolean('keep')) {
            return redirect()->route('roles.index')->with([
                'toastShow' => true,
                'toastMsg' => 'Papéis desmarcados com sucesso!',
            ]);
        }

        return redirect()->back()->with([
            'toastShow' => true,
            'toastMsg' => "Papel {$role->name} desmarcado com sucesso!",
        ]);
    }
}
