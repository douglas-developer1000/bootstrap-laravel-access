<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Facades\CheckList;
use App\Facades\ListStorager;
use App\Http\Requests\Plan\PlanRequest;
use App\Models\Plan;
use App\Models\User;
use App\Services\FeatureService;
use App\Services\PlanService;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;

final class PlanController extends Controller
{
    protected User $user;

    public function __construct(protected PlanService $svc)
    {
        $this->user = Auth::user();
    }

    public function index(Request $request): View
    {
        $trashed = $request->boolean('trashed');

        return view('pages.plans.index', [
            'list' => $this->svc->prepareIndex($request),
            'models' => fn (LengthAwarePaginator $pagination) => (
                $this->svc->hydratePlan($pagination->all())
            ),

            'trashed' => $trashed,
            'title' => $trashed ? 'Planos removidos' : 'Planos',
            'hasAccess' => $this->user->can(...),
            'roleToPlanEmpty' => collect(ListStorager::getList('rolesToPlan'))->isEmpty(),
            'qs' => collect($request->query->all()),
        ]);
    }

    public function create(PlanRequest $request): View
    {
        return view('pages.plans.create', [
            'roles' => Role::whereIn(
                'name',
                ListStorager::getList('rolesToPlan')
            )->get(),
            'hasAccess' => $this->user->can(...),
        ]);
    }

    public function show(Plan $plan): View
    {
        $roles = $plan->roles;
        $currentRoles = $roles->filter(fn (Role $role) => ! $role->pivot->additional);
        $additionalRoles = $roles->filter(fn (Role $role) => $role->pivot->additional);

        return view('pages.plans.show', [
            'plan' => tap($plan, function (Plan $plan) {
                $plan->price = $this->svc->parseCurrencyValue(
                    \floatval($plan->price)
                );
            }),
            'roles' => $currentRoles,
            'additionalRoles' => $additionalRoles,
            'permissions' => $currentRoles->map(
                fn (Role $role) => $role->permissions
            )->flatten(),
            'additionalPermissions' => $additionalRoles->map(
                fn (Role $role) => $role->permissions
            )->flatten(),
        ]);
    }

    protected function cleanStoredRoles(Collection $roles): array
    {
        return collect(ListStorager::getList('rolesToPlan'))->diff(
            $roles->pluck('name')
        )->all();
    }

    public function edit(Plan $plan): View
    {
        $planRoles = $plan->roles;

        return view('pages.plans.edit', [
            'plan' => $plan,
            'roles' => Role::whereIn(
                'name',
                $this->cleanStoredRoles($planRoles)
            )->get(),
            'planRoles' => $planRoles,
            'boxChecked' => CheckList::boxChecked(...),
        ]);
    }

    public function update(PlanRequest $request, Plan $plan): RedirectResponse
    {
        $this->svc->syncPlanRoles(
            $this->svc->updatePlan(
                $plan,
                $this->svc->extractPlanParams($request)
            ),
            $this->svc->extractRoleIds(
                collect($request->input('roles'))->merge(
                    $this->cleanStoredRoles($plan->roles),
                ),
                collect($request->input('additionals', []))
            )
        );

        return redirect()->route('plans.index')->with([
            'toastShow' => true,
            'toastMsg' => 'Plano editado com sucesso!',
        ]);
    }

    public function store(PlanRequest $request): RedirectResponse
    {
        $this->svc->syncPlanRoles(
            $this->svc->createPlan(
                $this->svc->extractPlanParams($request)
            ),
            $this->svc->extractRoleIds(
                ListStorager::getList('rolesToPlan'),
                collect($request->input('additionals', []))
            )
        );

        return redirect()->route('plans.index')->with([
            'toastShow' => true,
            'toastMsg' => 'Plano criado com sucesso!',
        ]);
    }

    public function destroy(PlanRequest $request, Plan $plan)
    {
        $this->svc->removePlan($plan);

        return redirect()->route('plans.index')->with([
            'toastShow' => true,
            'toastMsg' => 'Plano removido com sucesso!',
        ]);
    }

    /**
     * @param  Plan[]  $planList
     */
    public function removeGroup(PlanRequest $request, string $key, array $planList)
    {
        $this->svc->removePlanGroup($planList);

        return redirect()->route('plans.index')->with([
            'toastShow' => true,
            'toastMsg' => 'Planos removidos com sucesso!',
        ]);
    }

    public function restore(PlanRequest $request, Plan $planDeleted)
    {
        $this->svc->restorePlan($planDeleted);

        return redirect()->route(
            'plans.index',
            $request->query->all(),
        )->with([
            'toastShow' => true,
            'toastMsg' => 'Plano restaurado com sucesso!',
        ]);
    }

    public function restoreGroup(PlanRequest $request, string $key, array $planList)
    {
        $this->svc->restorePlanGroup($planList);

        return redirect()->route(
            'plans.index',
            $request->query->all(),
        )->with([
            'toastShow' => true,
            'toastMsg' => 'Planos restaurados com sucesso!',
        ]);
    }

    public function flush(FeatureService $featSvc): RedirectResponse
    {
        $featSvc->update();

        return redirect()->back()->with([
            'toastShow' => true,
            'toastMsg' => 'Planos atualizados com sucesso!',
        ]);
    }
}
