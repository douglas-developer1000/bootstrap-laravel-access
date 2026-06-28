<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Events\PlanAssigned;
use App\Http\Requests\User\UserRequest;
use App\Models\Plan;
use App\Models\User;
use App\Services\ChecklistService;
use App\Services\LicenseService;
use App\Services\PlanService;
use App\Services\UserService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Number;

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
        return view('pages.users.index', [
            'list' => $this->userSvc->prepareIndex($request),
        ]);
    }

    /**
     * Show the view for user visualization (used by super-admin)
     */
    public function show(User $user)
    {
        $permissions = $user->roles->map(fn ($role) => $role->permissions)->flatten();

        return view('pages.users.show', [
            'user' => $user,
            'roles' => $user->roles,
            'permissions' => $permissions,
            'dPermissions' => $user->getDirectPermissions(),
            'parsePrice' => fn (float|int $value) => (
                Number::currency(
                    number: $value,
                    in: 'BRL',
                    locale: 'pt_BR',
                    precision: 2
                )
            ),
        ]);
    }

    /**
     * Show the view for user creation (used by super-admin)
     */
    public function create(ChecklistService $checkSvc)
    {
        $plans = Plan::all();
        $additionalRoles = $plans->mapWithKeys(fn (Plan $plan) => [
            $plan->slug => $plan->roles()->wherePivot('additional', 1)->get(),
        ]);

        return view('pages.users.create', [
            'plans' => $plans,
            'additionalRoles' => $additionalRoles,
            'boxChecked' => $checkSvc->boxChecked(...),
        ]);
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
            'toastMsg' => 'Usuário editado com sucesso!',
        ]);
    }

    /**
     * Store an user during an internal creation
     */
    public function store(UserRequest $request, PlanService $planSvc, LicenseService $licenseSvc)
    {
        /** @var array{name: string, email: string, password: string, recurring: bool } $inputs */
        $inputs = $this->userSvc->extractParams(
            $request,
            ['name', 'email', 'password'],
            ['recurring']
        );
        $plan = $planSvc->parsePlan($request->input('plan'));

        ['user' => $user, 'license' => $license] = DB::transaction(function () use ($plan, $inputs, $licenseSvc) {
            $user = $this->userSvc->createInternalUser(
                $inputs['name'],
                $inputs['email'],
                $inputs['password']
            );

            return [
                'user' => $user,
                'license' => $licenseSvc->bindPlan(
                    $plan,
                    $user,
                    $inputs['recurring']
                ),
            ];
        });
        PlanAssigned::dispatch($user, $plan, $license);

        return redirect()->route('users.create')->with([
            'toastShow' => true,
            'toastMsg' => 'Usuário criado com sucesso!',
        ]);
    }

    /**
     * Store an user by the newly authenticated user themselves
     */
    public function storeSigned(UserRequest $request)
    {
        /** @var array{ name: string, email: string, password: string, phone: string|null } $inputs */
        $inputs = $this->userSvc->extractParams(
            $request,
            ['name', 'email', 'password', 'phone']
        );
        $this->userSvc->createExternalUser(
            $inputs['name'],
            $inputs['email'],
            $inputs['password'],
            $inputs['phone'],
        );

        return redirect()->route('login')->with([
            'toastShow' => true,
            'toastMsg' => 'Conta criada com sucesso! Autenticação liberada!',
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
            'toastMsg' => 'Usuário removido com sucesso!',
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
            'toastMsg' => 'Usuário removido com sucesso!',
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
            'toastMsg' => 'Usuários removidos com sucesso!',
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
            'toastMsg' => 'Usuário restaurado com sucesso!',
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
            'toastMsg' => 'Usuários restaurados com sucesso!',
        ]);
    }
}
