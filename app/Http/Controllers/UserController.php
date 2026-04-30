<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\UserRequest;
use Illuminate\Http\Request;
use App\Libraries\Utils\Paginator;
use App\Libraries\Utils\PhoneFormatter;
use App\Models\User;
use App\Services\Registration\RegisterApprovalService;
use App\Services\UserService;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Auth\Events\Registered;

final class UserController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        private UserService $userSvc,
        private RegisterApprovalService $approvalSvc
    ) {
        // ...
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $group = Paginator::buildGroup($request->only('group'));
        $nameSearch = Paginator::buildSearch($request->only('name'), 'name');
        $sort = Paginator::buildSort($request->only('sort'), ['created_at', 'id', 'name']);
        $order = Paginator::buildOrder($request->only('order'));
        $trashed = $request->boolean('trashed');

        $query = $trashed ? User::onlyTrashed() : User::query();
        if ($nameSearch) {
            $nameSearch = addcslashes($nameSearch, '%_');
            $query = $query->whereLike('name', "%{$nameSearch}%");
        }
        $list = $query->orderBy($sort, $order)->paginate(
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
        $permissions = $user->roles->map(fn($role) => $role->permissions)->flatten();

        return view('pages.users.show', [
            'user' => $user,
            'roles' => $user->roles,
            'permissions' => $permissions,
            'dPermissions' => $user->getDirectPermissions()
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('pages.users.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UserRequest $request)
    {
        $this->userSvc->create([
            ...$request->only([
                'name',
                'email'
            ]),
            'email_verified_at' => Carbon::now(),
            'password' => Hash::make($request->input('password'))
        ]);

        return redirect()->route('users.create')->with([
            'toastShow' => true,
            'toastMsg' => 'Usuário criado com sucesso!'
        ]);
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
        $this->userSvc->update($id, [
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
    public function destroy(User $user)
    {
        $this->authorize('remove-user', $user);
        $user->delete();

        return redirect()->route(
            'users.index',
            request()->query() ?? []
        )->with([
            'toastShow' => true,
            'toastMsg' => 'Usuário removido com sucesso!'
        ]);
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

    public function attachRoleGroup(UserRequest $request, User $user)
    {
        $roles = Role::whereIn(
            'id',
            $request->validated('attachment')
        )->get('name')->pluck('name')->all();
        $user->assignRole(...$roles);

        return redirect()->route('users.attach.roles', [
            'user' => $user->id,
            ...(request()->query() ?? [])
        ])->with([
            'toastShow' => true,
            'toastMsg' => 'Vinculação executada com sucesso!'
        ]);
    }

    public function bindPermissionGroup(UserRequest $request, User $user)
    {
        $permissions = Permission::whereIn(
            'id',
            $request->validated('attachment')
        )->get('name')->pluck('name')->all();
        $user->givePermissionTo(...$permissions);

        return redirect()->route('users.attach.permissions', [
            'user' => $user->id,
            ...(request()->query() ?? [])
        ])->with([
            'toastShow' => true,
            'toastMsg' => 'Vinculações executadas com sucesso!'
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

    public function createSigned()
    {
        return view('pages.users.create-signed');
    }

    public function storeSigned(UserRequest $request)
    {
        $registerApproval = $this->approvalSvc->findByEmail($request->email);
        $this->approvalSvc->delete($registerApproval->id);

        $phone = PhoneFormatter::clear($registerApproval->phone ?? $request->phone);
        /** @var User $user */
        $user = $this->userSvc->create(attributes: [
            ...$request->only(['name', 'email', 'password']),
            'phone' => $phone,
        ]);
        if (Role::where('name', 'user')->exists()) {
            $user->assignRole('user');
        }
        event(new Registered($user));

        return redirect()->route('login')->with([
            'toastShow' => true,
            'toastMsg' => 'Conta criada com sucesso! Pode autenticar agora.'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroyTrashed(int $id)
    {
        $user = User::onlyTrashed()->findOrFail($id);
        $user->forceDelete();

        return redirect()->route(
            'users.index',
            request()->query() ?? []
        )->with([
            'toastShow' => true,
            'toastMsg' => 'Usuário removido com sucesso!'
        ]);
    }

    public function restore(int $id)
    {
        $user = User::onlyTrashed()->findOrFail($id);
        $user->restore();

        return redirect()->route(
            'users.index',
            request()->query() ?? []
        )->with([
            'toastShow' => true,
            'toastMsg' => 'Usuário restaurado com sucesso!'
        ]);
    }

    public function removeGroup(UserRequest $request)
    {
        $qs = collect(request()->query() ?? []);
        $forceDelete = $qs->contains(
            fn($value, $key) => $key === 'trashed' && $value === '1'
        );

        $remotions = collect($request->validated('remotion'))->map(
            fn($val) => \intval($val)
        )->all();
        $this->userSvc->removeList($remotions, $forceDelete);

        return redirect()->route(
            'users.index',
            $qs->all()
        )->with([
            'toastShow' => true,
            'toastMsg' => 'Usuários removidos com sucesso!'
        ]);
    }

    public function restoreGroup(UserRequest $request)
    {
        $restorations = collect($request->validated('restoration'))->map(
            fn($val) => \intval($val)
        )->all();
        $this->userSvc->restoreList($restorations);

        return redirect()->route(
            'users.index',
            ['trashed' => '1']
        )->with([
            'toastShow' => true,
            'toastMsg' => 'Usuários restaurados com sucesso!'
        ]);
    }
}
