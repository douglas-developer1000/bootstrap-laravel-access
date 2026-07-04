<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Plan;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Number;
use Spatie\Permission\Models\Role;

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
            'parsePrice' => fn(float|int $value) => (
                Number::currency(
                    number: $value,
                    in: 'BRL',
                    locale: 'pt_BR',
                    precision: 2
                )
            )
        ]);
    }
}
