<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Events\LicenseAbandoned;
use App\Events\LicenseCanceled;
use App\Events\LicenseReactivated;
use App\Http\Requests\SettingsPlan\SettingsPlanRequest;
use App\Models\License;
use App\Models\Plan;
use App\Models\Role;
use App\Models\User;
use App\Services\LicenseService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Number;

final class SettingsPlanController extends Controller
{
    protected User $user;

    public function __construct(protected LicenseService $licenseSvc)
    {
        $this->user = Auth::user();
    }

    public function index(): View
    {
        $plans = Plan::with('roles')->get();
        $roles = $plans->mapWithKeys(fn (Plan $plan) => [
            $plan->slug => [
                'core' => $plan->roles->filter(
                    fn (Role $role) => ! $role->pivot->additional
                ),
                'additionals' => $plan->roles->filter(
                    fn (Role $role) => $role->pivot->additional
                ),
            ],
        ]);

        return view('pages.settings.plans.index', [
            'title' => 'Planos disponíveis',
            'plans' => Plan::all(),
            'roles' => $roles,
            'activeLicense' => $this->user->activeLicense,
            'pendingLicense' => $this->user->pendingLicense,
            'parsePrice' => $this->parsePrice(...),
        ]);
    }

    public function show(Plan $plan): View
    {
        [$core, $additional] = $plan->roles->partition(
            fn (Role $role) => $role->pivot->additional === 0
        );

        /** @var null|License */
        $activeLicense = $this->user->activeLicense()->with('plan')->first();
        /** @var null|License */
        $pendingLicense = $this->user->pendingLicense()->with('plan')->first();

        return view('pages.settings.plans.show', [
            'plan' => $plan,
            'title' => 'Visão geral',
            'roleDescriptions' => [
                'core' => $core->flatMap(
                    fn (Role $role) => $role->roleDescriptions()->pluck('description')
                ),
                'additional' => $additional->flatMap(
                    fn (Role $role) => $role->roleDescriptions()->pluck('description')
                ),
            ],
            'activeLicense' => $activeLicense,
            'pendingLicense' => $pendingLicense,
            'pendingOrActive' => (
                $this->samePlan($plan, $activeLicense) ||
                $this->samePlan($plan, $pendingLicense)
            ),
            'isSamePlan' => $this->samePlan(...),

            'additionals' => $additional,
            'parsePrice' => $this->parsePrice(...),
            'checkoutData' => [
                'core' => $this->licenseSvc->prepareCheckout($plan, $this->user, []),
                'additionalPrice' => License::PRICE_ADDITIONAL,
            ],
            'btnContent' => $this->makeShowBtnContent($plan, $pendingLicense, $activeLicense),
        ]);
    }

    protected function makeShowBtnContent(Plan $plan, ?License $pendingLicense, ?License $activeLicense): string
    {
        if ($this->samePlan($plan, $activeLicense)) {
            if ($activeLicense->cancelled_at) {
                return 'Reativar plano';
            }

            return 'Cancelar plano ativo';
        }
        if ($pendingLicense) {
            if ($this->samePlan($plan, $pendingLicense)) {
                return 'Cancelar solicitação';
            }

            return 'Trocar solicitação';
        }

        return $activeLicense ? 'Trocar plano' : 'Solicitar acesso';
    }

    public function handlePlan(SettingsPlanRequest $request, Plan $plan): RedirectResponse
    {
        /** @var null|License */
        $activeLicense = $this->user->activeLicense()->with('plan')->first();
        $licenseSvc = $this->licenseSvc;

        return DB::transaction(function () use ($request, $plan, $activeLicense, $licenseSvc) {
            if ($this->samePlan($plan, $activeLicense)) {
                if ($activeLicense->isPreCancellable) {
                    $activeLicense->cancelLicense();
                    LicenseCanceled::dispatch($this->user, $activeLicense->plan, $activeLicense);
                } else {
                    $activeLicense->activateLicense();
                    LicenseReactivated::dispatch($this->user, $activeLicense->plan, $activeLicense);
                }

                return redirect()->back();
            }
            $pendingLicense = $this->user->pendingLicense;

            if ($this->samePlan($plan, $pendingLicense)) {
                $pendingLicense?->abandonLicense('Desistência do usuário');
                LicenseAbandoned::dispatch($this->user, $pendingLicense->plan, $pendingLicense);

                return redirect()->back();
            } else {
                $pendingLicense?->abandonLicense("Substituição de checkout (Plano {$plan->name})");
            }

            $licenseSvc->bindPlan(
                plan: $plan,
                licensable: $this->user,
                isRecurring: $request->boolean('is_recurring'),
                additionalRoles: $request->input('additionals', []),
            );
            if ($pendingLicense) {
                LicenseAbandoned::dispatch($this->user, $pendingLicense->plan, $pendingLicense);
            }

            return redirect()->back();
        });
    }

    protected function samePlan(Plan $plan, ?License $license): bool
    {
        return $plan->slug === $license?->plan->slug;
    }

    protected function parsePrice(float|int $value): string
    {
        return Number::currency(
            number: $value,
            in: 'BRL',
            locale: 'pt_BR',
            precision: 2
        );
    }
}
