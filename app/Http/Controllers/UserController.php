<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\User\UserRequest;
use Illuminate\Http\Request;
use App\Libraries\Utils\Paginator;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

final class UserController extends Controller
{
    use AuthorizesRequests;

    public function __construct(protected UserService $userSvc)
    {
        // ...
    }

    /**
     * Display a listing of the users (used by super-admin).
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
     * Show the view for user visualization (used by super-admin)
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
     * Show the view for user creation (used by super-admin)
     */
    public function create()
    {
        return view('pages.users.create');
    }

    /**
     * Show the view for user edition (used by super-admin)
     */
    public function edit(User $user)
    {
        return view('pages.users.edit', ['user' => $user]);
    }

    /**
     * Show the view for external user creation
     */
    public function createSigned()
    {
        return view('pages.users.create-signed');
    }

    /**
     * Update the user by super-admin access
     */
    public function update(UserRequest $request, int $id)
    {
        $this->userSvc->updateUser($id, $request->validated('name'));

        return redirect()->route('users.index')->with([
            'toastShow' => true,
            'toastMsg' => 'Usuário editado com sucesso!'
        ]);
    }

    /**
     * Store an user during an internal creation
     */
    public function store(UserRequest $request)
    {
        $this->userSvc->createInternalUser($request);

        return redirect()->route('users.create')->with([
            'toastShow' => true,
            'toastMsg' => 'Usuário criado com sucesso!'
        ]);
    }

    /**
     * Store an user by the newly authenticated user themselves
     */
    public function storeSigned(UserRequest $request)
    {
        $this->userSvc->createExternalUser($request);

        return redirect()->route('login')->with([
            'toastShow' => true,
            'toastMsg' => 'Conta criada com sucesso! Autenticação liberada!'
        ]);
    }

    /**
     * Remove the user (for a soft-deletion)
     */
    public function destroy(User $user)
    {
        $this->authorize('remove-user', $user);
        $this->userSvc->removeUser(id: \intval($user->id));

        return redirect()->route(
            'users.index',
            request()->query() ?? []
        )->with([
            'toastShow' => true,
            'toastMsg' => 'Usuário removido com sucesso!'
        ]);
    }

    /**
     * Remove the user (after a soft-deletion)
     */
    public function destroyTrashed(int $id)
    {
        $this->userSvc->removeUser(id: $id, trashed: true);

        return redirect()->route(
            'users.index',
            request()->query() ?? []
        )->with([
            'toastShow' => true,
            'toastMsg' => 'Usuário removido com sucesso!'
        ]);
    }

    /**
     * Remove the user list (on both soft-deletion and force-deletion)
     */
    public function removeGroup(UserRequest $request)
    {
        $qs = collect(request()->query() ?? []);
        $this->userSvc->removeUserList($request, $qs);

        return redirect()->route(
            'users.index',
            $qs->all()
        )->with([
            'toastShow' => true,
            'toastMsg' => 'Usuários removidos com sucesso!'
        ]);
    }

    /**
     * Restore an user soft-deleted
     */
    public function restore(int $id)
    {
        $this->userSvc->restore($id);

        return redirect()->route(
            'users.index',
            request()->query() ?? []
        )->with([
            'toastShow' => true,
            'toastMsg' => 'Usuário restaurado com sucesso!'
        ]);
    }

    /**
     * Restore a soft-deleted user list
     */
    public function restoreGroup(UserRequest $request)
    {
        $this->userSvc->restoreGroup($request);

        return redirect()->route(
            'users.index',
            ['trashed' => '1']
        )->with([
            'toastShow' => true,
            'toastMsg' => 'Usuários restaurados com sucesso!'
        ]);
    }
}
