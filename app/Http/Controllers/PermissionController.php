<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\Permission\PermissionRequest;
use App\Services\PaginatorService;
use App\Services\PermissionService;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;

final class PermissionController extends Controller
{
    public function __construct(protected PermissionService $svc)
    {
        // ...
    }
    public function index(Request $request, PaginatorService $paginator)
    {
        $group = $paginator->buildGroup($request->only('group'));
        $search = $paginator->buildSearch($request->only('q'));
        $sort = $paginator->buildSort($request->only('sort'), ['created_at', 'id', 'name']);
        $order = $paginator->buildOrder($request->only('order'));

        $query = Permission::query();
        if ($search) {
            $search = addcslashes($search, '%_');
            $query = $query->whereLike('name', "%{$search}%");
        }
        $list = $query->orderBy($sort, $order)->paginate(
            perPage: $group,
            columns: ['id', 'name', 'created_at']
        );

        return view('pages.permissions.index', ['list' => $list]);
    }

    public function create()
    {
        return view('pages.permissions.create');
    }

    public function store(PermissionRequest $request)
    {
        $this->svc->createPermission($request->validated('name'));
        return redirect()->route('permissions.index')->with([
            'toastShow' => true,
            'toastMsg' => 'Permissão criada com sucesso!'
        ]);
    }

    public function edit(Permission $permission)
    {
        return view('pages.permissions.edit', ['permission' => $permission]);
    }

    public function update(PermissionRequest $request)
    {
        $permission = Permission::findById($request->route('permission'));
        $this->svc->updatePermission(permission: $permission, name: $request->validated('name'));

        return redirect()->route('permissions.index')->with([
            'toastShow' => true,
            'toastMsg' => 'Permissão editada com sucesso!'
        ]);
    }

    public function destroy(Permission $permission)
    {
        $this->svc->removePermission($permission);

        return redirect()->route(
            'permissions.index',
            request()->query() ?? []
        )->with([
            'toastShow' => true,
            'toastMsg' => 'Permissão removida com sucesso!'
        ]);
    }

    public function removeGroup(PermissionRequest $request)
    {
        $this->svc->removePermissionList($request->validated('remotion'));

        return redirect()->route(
            'permissions.index',
            request()->query() ?? []
        )->with([
            'toastShow' => true,
            'toastMsg' => 'Permissões removidas com sucesso!'
        ]);
    }
}
