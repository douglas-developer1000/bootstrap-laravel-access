<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\Permission\PermissionRequest;
use App\Libraries\Enums\PermissionNameEnum;
use App\Services\FeatureService;
use App\Services\PermissionService;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Artisan;
use Spatie\Permission\Models\Permission;

final class PermissionController extends Controller
{
    public function __construct(
        protected PermissionService $svc,
        protected FeatureService $featSvc,
    ) {
        // ...
    }

    public function index(Request $request)
    {
        return view('pages.permissions.index', [
            'list' => $this->pullData($request),
            'qs' => collect($request->query->all()),
        ]);
    }

    protected function pullData(Request $request): LengthAwarePaginator
    {
        if ($request->boolean('lost')) {
            return $this->featSvc->prepareIdlePermissionIndex($request);
        }

        return $this->svc->prepareIndex($request);
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
            'toastMsg' => 'Permissão criada com sucesso!',
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
            'toastMsg' => 'Permissão editada com sucesso!',
        ]);
    }

    public function destroy(Permission $permission)
    {
        $this->svc->removePermission($permission);

        return redirect()->route(
            'permissions.index',
            request()->query() ?? []
        )
            ->with([
                'toastShow' => true,
                'toastMsg' => 'Permissão removida com sucesso!',
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
            'toastMsg' => 'Permissões removidas com sucesso!',
        ]);
    }

    public function flushPersistence()
    {
        $permissionNames = array_column(PermissionNameEnum::cases(), 'value');
        $deprecatedPermissions = Permission::whereNotIn('name', $permissionNames);

        $deprecatedPermissions->delete();
        Artisan::call('permissions:update');

        return redirect()->back()->with([
            'toastShow' => true,
            'toastMsg' => 'Permissões atualizadas com sucesso!',
        ]);
    }
}
