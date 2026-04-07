<?php

namespace App\Http\Controllers;

use App\Http\Requests\Permission\PermissionRequest;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    public function index()
    {
        $list = Permission::orderBy('created_at')->paginate(3, ['id', 'name']);

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

        return redirect()->route('permissions.index')->with([
            'toastShow' => true,
            'toastMsg' => 'Permissão removida com sucesso!'
        ]);
    }
}
