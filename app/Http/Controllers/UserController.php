<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Events\LicenseAbandoned;
use App\Events\PlanAssigned;
use App\Http\Requests\User\UserRequest;
use App\Libraries\Enums\InvoiceStatusEnum;
use App\Models\Plan;
use App\Models\User;
use App\Services\LicenseService;
use App\Services\PlanService;
use App\Services\UserService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

final class UserController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        protected UserService $userSvc,
        protected PlanService $planSvc,
        protected LicenseService $licenseSvc,
    ) {
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
            'licenses' => $user->licenses()->orderBy('created_at', 'desc')->get(),
            'permissions' => $permissions,
            'dPermissions' => $user->getDirectPermissions(),
        ]);
    }

    /**
     * @return array{
     *      plans: EloquentCollection<Plan>,
     *      additionalRoles: Collection<string, EloquentCollection>,
     * }
     */
    protected function pullPlanAndAdditionals(?Plan $plan = null): array
    {
        $plans = Plan::when(
            $plan,
            fn (Builder $query, Plan $plan) => $query->whereNot('slug', $plan->slug)
        )->get();

        return [
            'plans' => $plans,
            'additionalRoles' => $plans->mapWithKeys(fn (Plan $plan) => [
                $plan->slug => $plan->roles()->wherePivot('additional', 1)->get(),
            ]),
        ];
    }

    /**
     * Show the view for user creation (used by super-admin)
     */
    public function create()
    {
        return view('pages.users.create', [
            ...$this->pullPlanAndAdditionals(),
        ]);
    }

    /**
     * Show the view for user edition (used by super-admin)
     */
    public function edit(User $user)
    {
        $activeLicense = $user->activeLicense;

        return view('pages.users.edit', [
            'user' => $user,
            'activeLicense' => $activeLicense,
            ...$this->pullPlanAndAdditionals($activeLicense?->plan),
        ]);
    }

    /**
     * Show the view for external user creation
     * TO-DO: remover
     */
    public function createByUser()
    {
        return view('pages.signup');
    }

    /**
     * Update the user by super-admin access
     */
    public function update(UserRequest $request, User $user)
    {
        DB::transaction(function () use ($request, $user) {
            $this->userSvc->updateUser($user, $request->validated('name'));
            if ($request->has('plan')) {
                $plan = $this->planSvc->parsePlan($request->input('plan'));

                $pendingLicense = $user->pendingLicense;
                $pendingLicense?->abandonLicense(
                    invoiceStatus: InvoiceStatusEnum::VOIDED,
                    reason: "Substituição de checkout (Plano {$plan->name})",
                );

                $license = $this->licenseSvc->bindPlan(
                    $plan,
                    $user,
                    $request->boolean('recurring'),
                    $request->input('additionals', []),
                );

                PlanAssigned::dispatch($user, $plan, $license);
                if ($pendingLicense) {
                    LicenseAbandoned::dispatch($user, $pendingLicense->plan, $pendingLicense);
                }
            }
        });

        return redirect()->route('users.index')->with([
            'toastShow' => true,
            'toastMsg' => 'Usuário editado com sucesso!',
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
            'toastMsg' => 'Usuário criado com sucesso!',
        ]);
    }

    /**
     * Store a new account empty by guest
     */
    public function storeByUser(UserRequest $request)
    {
        $this->userSvc->createExternalUser($request);

        return redirect()->route('login')->with([
            'toastShow' => true,
            'toastMsg' => 'Verifique seu e-mail para confirmar o cadastro!',
        ]);
    }

    /**
     * Remove the user (for a soft-deletion)
     */
    public function destroy(User $user)
    {
        $this->authorize('remove-user', $user);
        $this->userSvc->removeUser($user);

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
        $this->userSvc->removeUser(User::onlyTrashed()->findOrFail($id), trashed: true);

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
