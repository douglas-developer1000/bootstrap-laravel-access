<?php

namespace App\Http\Controllers;

use App\Http\Requests\Permission\PermissionRequest;
use App\Libraries\Utils\Paginator;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    public function index(Request $request)
    {
        $group = Paginator::buildGroup($request->only('group'));
        $search = Paginator::buildSearch($request->only('q'));
        $sort = Paginator::buildSort($request->only('sort'), ['created_at', 'id', 'name']);
        $order = Paginator::buildOrder($request->only('order'));

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
        Permission::create(['name' => $request->validated('name')]);
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
        $id = $request->route('permission');
        $permission = Permission::findOrFail($id);
        $permission->update([
            'name' => $request->validated('name')
        ]);
        return redirect()->route('permissions.index')->with([
            'toastShow' => true,
            'toastMsg' => 'Permissão editada com sucesso!'
        ]);
    }

    public function destroy(Permission $permission)
    {
        $permission->delete();

        return redirect()->route(
            'permissions.index',
            request()->query() ?? []
        )->with([
            'toastShow' => true,
            'toastMsg' => 'Permissão removida com sucesso!'
        ]);
    }
}
