<?php

declare(strict_types=1);

namespace App\Services;

use App\Facades\ListStorager;
use App\Libraries\Enums\BillingPeriodEnum;
use App\Models\Plan;
use App\Services\Abstracts\AbstractPaginatorIndex;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Number;
use Illuminate\Support\Str;
use Override;
use Spatie\Permission\Models\Role;

final class PlanService
{
    public function prepareIndex(Request $request)
    {
        return (new class() extends AbstractPaginatorIndex
        {
            #[Override]
            public function query(Request $request): Builder
            {
                return Plan::getQuery();
            }

            #[Override]
            public function attachQuery(Request $request, Builder $query): Builder
            {
                return $this->filterSearch(
                    $this->filterSoftDelete(
                        parent::attachQuery($request, $query),
                        $request->boolean('trashed'),
                    ),
                    $this->paginator->buildSearch($request->only('name'), 'name'),
                    'name'
                )->select(
                    'id',
                    'name',
                    'created_at',
                    'billing_period',
                    'slug'
                );
            }

            #[Override]
            public function getSortColumns(): array
            {
                return ['created_at', 'name', 'billing_period'];
            }
        })->prepareIndex(
            $request,
            '*'
        );
    }

    public function createPlan(array $params): Plan
    {
        return Plan::create($params);
    }

    public function updatePlan(Plan $plan, array $params): Plan
    {
        $plan->update($params);

        return $plan;
    }

    public function extractPlanParams(Request $request): array
    {
        $name = $request->input('name');
        $billingPeriod = BillingPeriodEnum::from($request->input('billing_period'));

        return [
            'name' => $name,
            'slug' => $this->mountSlug($name, $billingPeriod),
            'billing_period' => $billingPeriod,
            'price' => $request->input('price'),
            'description' => $request->input('description'),
        ];
    }

    /**
     * @param  array<int, array{additional: 0|1}>  $roleIds
     */
    public function syncPlanRoles(Plan $plan, array $roleIds = []): void
    {
        $plan->roles()->sync($roleIds);
        ListStorager::clearList('rolesToPlan');
    }

    /**
     * @param  string[]  $roleNames
     * @return int[]
     */
    public function extractRoleIds(array|Collection $roleNames, Collection $additionals): array
    {
        /** @var int[] $roleNames */
        $roleNames = \is_array($roleNames) ? $roleNames : $roleNames->all();

        return Role::whereIn(
            'name',
            $roleNames
        )->get('id')->pluck('id')->mapWithKeys(fn (int $id) => [
            $id => ['additional' => $additionals->contains($id)],
        ])->all();
    }

    public function mountSlug(string $name, BillingPeriodEnum|string $billing): string
    {
        if (\is_string($billing)) {
            $billing = BillingPeriodEnum::from($billing);
        }

        return Str::of($name)->slug()->append("-{$billing->value}")->toString();
    }

    public function parseCurrencyValue(float|int $value): string
    {
        return Number::currency(
            number: $value,
            in: 'BRL',
            locale: 'pt_BR',
            precision: 2
        );
    }

    /**
     * @return Collection<Plan>
     */
    public function hydratePlan(array $plans): Collection
    {
        return Plan::hydrate($plans)->each(function (Plan $plan, $i) {
            $plan->hasLicense = $plan->licenses()->exists();

            return $plan;
        });
    }

    public function removePlan(Plan $plan): void
    {
        if ($plan->licenses()->exists()) {
            $plan->delete();
        } else {
            $this->syncPlanRoles($plan);
            $plan->forceDelete();
        }
    }

    /**
     * @param  Plan[]  $plans
     */
    public function removePlanGroup(array $plans): void
    {
        collect($plans)->each($this->removePlan(...));
    }

    public function restorePlan(Plan $plan): void
    {
        $plan->restore();
    }

    /**
     * @param  Plan[]  $plans
     */
    public function restorePlanGroup(array $plans): void
    {
        collect($plans)->each($this->restorePlan(...));
    }

    public function parsePlan(string $slug): Plan
    {
        return Plan::whereSlug($slug)->first();
    }
}
