<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\SettingsPlan\SettingsPlanRequest;
use App\Models\License;
use App\Models\Plan;
use App\Models\Role;
use App\Models\User;
use App\Services\LicenseService;
use App\Services\SettingsPlanService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

final class SettingsPlanController extends Controller
{
    protected User $user;

    public function __construct(
        protected SettingsPlanService $svc,
        protected LicenseService $licenseSvc,
    ) {
        $this->user = Auth::user();
    }

    public function index(): View
    {
        $plans = Plan::with('roles')->get();

        return view('pages.settings.plans.index', [
            'title' => 'Planos disponíveis',
            'plans' => $plans,
            'roles' => $this->svc->separatePlanRoles($plans),
            'activeLicense' => $this->user->activeLicense,
            'pendingLicense' => $this->user->pendingLicense,
        ]);
    }

    public function show(Plan $plan): View
    {
        $roles = $this->svc->partitionPlanRoles($plan);

        /** @var null|License */
        $activeLicense = $this->user->activeLicense()->with('plan')->first();
        /** @var null|License */
        $pendingLicense = $this->user->pendingLicense()->with('plan')->first();

        return view('pages.settings.plans.show', [
            'plan' => $plan,
            'title' => 'Visão geral',
            'roleDescriptions' => [
                'core' => $roles['core']->flatMap(
                    fn (Role $role) => $role->roleDescriptions()->pluck('description')
                ),
                'additional' => $roles['additionals']->flatMap(
                    fn (Role $role) => $role->roleDescriptions()->pluck('description')
                ),
            ],
            'activeLicense' => $activeLicense,
            'pendingLicense' => $pendingLicense,
            'pendingOrActive' => (
                $this->svc->samePlan($plan, $activeLicense) ||
                $this->svc->samePlan($plan, $pendingLicense)
            ),
            'isSamePlan' => $this->svc->samePlan(...),

            'additionals' => $roles['additionals'],
            'checkoutData' => [
                'core' => $this->licenseSvc->prepareCheckout($plan, $this->user, []),
                'additionalPrice' => License::PRICE_ADDITIONAL,
            ],
            'btnContent' => $this->svc->makeShowBtnContent($plan, $pendingLicense, $activeLicense),
        ]);
    }

    public function handlePlan(SettingsPlanRequest $request, Plan $plan): RedirectResponse
    {
        $this->svc->handleLicensablePlan(
            $this->user,
            $plan,
            $request->boolean('is_recurring'),
            $request->input('additionals', [])
        );

        return redirect()->back();
    }
}
