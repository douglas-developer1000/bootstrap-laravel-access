<?php

declare(strict_types=1);

namespace App\Services;

use App\Events\LicenseActivated;
use App\Events\LicenseCanceled;
use App\Events\LicenseChanged;
use App\Events\LicensePending;
use App\Libraries\Enums\LicenseStatusEnum;
use App\Models\Contracts\Licensable;
use App\Models\Credit;
use App\Models\License;
use App\Models\Plan;
use App\Models\Role;
use App\Services\Abstracts\AbstractPaginatorIndex;
use Brick\Math\BigDecimal;
use Illuminate\Contracts\Database\Query\Expression;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Override;

final class LicenseService
{
    public function prepareIndex(Request $request)
    {
        return (new class() extends AbstractPaginatorIndex
        {
            #[Override]
            public function query(Request $request): Builder
            {
                return License::select([
                    'licenses.*',
                    $this->makeLicensableNameRowSelect(
                        $this->getTableReferences($this->getLicensableTypes())
                    ),
                ])->getQuery();
            }

            public function makeLicensableNameRowSelect(Collection $tables): Expression
            {
                return DB::raw(
                    Str::of('(CASE licenses.licensable_type ')->append(
                        $tables->map(
                            fn (string $table, string $class) => "WHEN \"{$class}\" THEN (SELECT `{$table}`.name FROM `{$table}` WHERE `{$table}`.id = licenses.licensable_id LIMIT 1)"
                        )->implode(' ')
                    )->append(' END) as licensableName')->toString()
                );
            }

            protected function getTableReferences(Collection $classes): Collection
            {
                return $classes->mapWithKeys(fn (string $class) => [
                    Str::of($class)->replace('\\', '\\\\')->toString() => new $class(),
                ])->map(fn (Model $instance) => $instance->getTable());
            }

            /**
             * @return Collection<int, string>
             */
            protected function getLicensableTypes(): Collection
            {
                return License::select(['licensable_type'])->distinct(['licensable_type'])->pluck('licensable_type');
            }

            protected function pickLicenseFilters(Request $request): Collection
            {
                $qs = collect($request->query());

                return collect(LicenseStatusEnum::cases())->map(
                    fn (LicenseStatusEnum $status) => $status->value
                )->mapWithKeys(fn (string $enumKey) => [
                    $enumKey => ! $qs->has($enumKey) || $request->boolean($enumKey),
                ]);
            }

            #[Override]
            public function attachQuery(Request $request, Builder $query): Builder
            {
                $lossTypes = $this->pickLicenseFilters($request)->filter(
                    fn (bool $presence) => $presence === true
                )->keys();

                return parent::attachQuery(
                    $request,
                    $query->whereIn(
                        'status',
                        $lossTypes
                    )
                );
            }

            #[Override]
            public function getSortColumns(): array
            {
                return ['created_at', 'status', 'licensableName'];
            }
        })->prepareIndex(
            $request,
            '*'
        );
    }

    /**
     * @return array<string, string>
     */
    public function defineLicenseStatusFilter(): array
    {
        return collect(LicenseStatusEnum::cases())->mapWithKeys(fn (LicenseStatusEnum $status) => [
            $status->value => $status->toString(),
        ])->all();
    }

    public function defineLicensableRoute(Licensable $licensable): string
    {
        return route('users.show', $licensable->getKey());
    }

    public function hydrateLicense(array $licenses): Collection
    {
        return License::hydrate($licenses);
    }

    protected function round(int|float $value, int $places = 3): float
    {
        $pow = 10 ** $places;

        return \intval($value * $pow) / $pow;
    }

    /**
     * Calculate all license prices and status. NOTE: It can be used to show informations at the view
     *
     * @return array{
     *      final_price: float|int,
     *      new_plan_price: float|int,
     *      prorata_discount: float,
     *      remaining_credit: float|int,
     *      total_discount: float,
     *      wallet_balance: float,
     *      new_license_status: LicenseStatusEnum
     * }
     */
    public function prepareCheckout(Plan $plan, Licensable $licensable, array $additionalRoles = []): array
    {
        $prorataDiscount = $licensable->activeLicense?->prorata ?? 0.0;

        /** @var float */
        $walletBalance = Credit::where('licensable_type', $licensable->getMorphClass())
            ->where('licensable_id', $licensable->getKey())
            ->sum('amount');

        $totalDiscount = BigDecimal::of("{$prorataDiscount}")
            ->plus("{$walletBalance}");

        $additionalQty = \count($additionalRoles);
        $additionalPrice = License::PRICE_ADDITIONAL;
        $newPlanPrice = BigDecimal::of("{$plan->price}")->plus(
            BigDecimal::of("{$additionalPrice}")->multipliedBy($additionalQty)
        );

        $data = [
            'new_plan_price' => $newPlanPrice->toFloat(),
            'prorata_discount' => $prorataDiscount,
            'wallet_balance' => $walletBalance,
            'total_discount' => $totalDiscount->toFloat(),
            'final_price' => max(
                0,
                $newPlanPrice
                    ->minus($totalDiscount)
                    ->toFloat()
            ),
        ];

        if ($totalDiscount->toFloat() >= $newPlanPrice->toFloat()) {
            return [
                ...$data,
                'remaining_credit' => $totalDiscount->minus($newPlanPrice)->toFloat(),
                'new_license_status' => LicenseStatusEnum::ACTIVE,
            ];
        }

        return [
            ...$data,
            'remaining_credit' => 0,
            'new_license_status' => LicenseStatusEnum::PENDING,
        ];
    }

    /**
     * @todo Enviar email com boleto/pix (quando houver) avisando também que créditos
     * foram utilizados, quando houver prorata.
     */
    public function bindPlan(Plan $plan, Licensable $licensable, bool $isRecurring = false, array $additionalRoles = []): License
    {
        $activeLicense = $licensable->activeLicense;
        $checkoutData = $this->prepareCheckout($plan, $licensable, $additionalRoles);

        if ($checkoutData['new_license_status'] === LicenseStatusEnum::ACTIVE) {
            $activeLicense?->changePlan();
        }

        $license = License::create([
            'plan_id' => $plan->id,
            'price_paid' => $checkoutData['final_price'],
            'licensable_id' => $licensable->getKey(),
            'licensable_type' => $licensable->getMorphClass(),
            'starts_at' => now(),
            'expires_at' => $plan->billing_period->advance(now()),
            'status' => $checkoutData['new_license_status'],
            'is_recurring' => $isRecurring,
        ]);
        $license->additionals()->sync(
            Role::whereIn('name', $additionalRoles)->pluck('id')->all()
        );

        if ($checkoutData['wallet_balance'] > 0) {
            Credit::create([
                'licensable_type' => $licensable->getMorphClass(),
                'licensable_id' => $licensable->getKey(),
                'license_id' => $license->id,
                'amount' => -$checkoutData['wallet_balance'],
                'description' => 'Saldo de crédito utilizado na troca de plano',
            ]);
        }
        if ($checkoutData['remaining_credit'] > 0) {
            Credit::create([
                'licensable_type' => $licensable->getMorphClass(),
                'licensable_id' => $licensable->getKey(),
                'license_id' => $license->id,
                'amount' => $checkoutData['remaining_credit'],
                'description' => 'Sobra de saldo/prorata após troca de plano',
            ]);
        }
        if ($checkoutData['new_license_status'] === LicenseStatusEnum::ACTIVE) {
            $license->releaseLicensable();
        }

        if ($activeLicense) {
            LicenseChanged::dispatch($licensable, $license, $activeLicense);
        } else {
            LicensePending::dispatch($licensable, $plan, $license);
        }

        return $license;
    }

    public function cancelLicense(License $license): void
    {
        DB::transaction(function () use ($license) {
            $license->cancelLicense();

            LicenseCanceled::dispatch($license->licensable, $license->plan, $license);
        });
    }

    /**
     * License possible status:
     * - Pending from:
     *     + first license
     *     + plan switch (with active license)
     * - active with previous cancelling
     */
    public function activateLicense(License $license): void
    {
        DB::transaction(function () use ($license) {
            $oldLicense = null;
            $licensable = $license->licensable;
            if ($license->status === LicenseStatusEnum::PENDING) {
                $oldLicense = $licensable->activeLicense;
                $oldLicense?->changePlan();
            }
            $license->activateLicense();
            $license->releaseLicensable();

            LicenseActivated::dispatch($licensable, $license->plan, $license);
        });
    }
}
