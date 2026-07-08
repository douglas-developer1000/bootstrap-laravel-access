<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Models\Role;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Number;

final class SettingsPlanController extends Controller
{
    public function index(Request $request): View
    {
        $plans = Plan::with('roles')->get();
        $roles = $plans->mapWithKeys(fn(Plan $plan) => [
            $plan->slug => [
                'core' => $plan->roles->filter(
                    fn(Role $role) => ! $role->pivot->additional
                ),
                'additionals' => $plan->roles->filter(
                    fn(Role $role) => $role->pivot->additional
                ),
            ]
        ]);

        return view('pages.settings.plans.index', [
            'title' => 'Planos disponíveis',
            'plans' => Plan::all(),
            'roles' => $roles,
            'parsePrice' => $this->parsePrice(...),
        ]);
    }

    public function show(Plan $plan): View
    {
        [$core, $additional] = $plan->roles->partition(
            fn(Role $role) => $role->pivot->additional === 0
        );

        return view('pages.settings.plans.show', [
            'plan' => $plan,
            'title' => "Visão geral",
            'roleDescriptions' => [
                'core' => $core->flatMap(
                    fn(Role $role) => $role->roleDescriptions()->pluck('description')
                ),
                'additional' => $additional->flatMap(
                    fn(Role $role) => $role->roleDescriptions()->pluck('description')
                ),
            ],
            'parsePrice' => $this->parsePrice(...),
        ]);
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
