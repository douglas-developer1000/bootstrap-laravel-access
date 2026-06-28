<?php

declare(strict_types=1);

namespace App\Services;

use App\Events\LicenseActivated;
use App\Events\LicenseCanceled;
use App\Events\LicenseChanged;
use App\Libraries\Enums\LicenseStatusEnum;
use App\Models\Contracts\HasLicenseHandling;
use App\Models\License;
use App\Models\Plan;
use App\Models\User;
use App\Services\Abstracts\AbstractPaginatorIndex;
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

    public function hydrateLicense(array $licenses): Collection
    {
        return License::hydrate($licenses);
    }

    public function bindPlan(Plan $plan, User $user, bool $isRecurring = false): License
    {
        $discount = $this->changeActiveLicense($user);

        return License::create([
            'plan_id' => $plan->id,
            'price_paid' => $plan->price - $discount,
            'licensable_id' => $user->id,
            'licensable_type' => $user->getMorphClass(),
            'starts_at' => now(),
            'expires_at' => $plan->billing_period->advance(now()),
            'status' => LicenseStatusEnum::PENDING,
            'is_recurring' => $isRecurring,
        ]);
    }

    /**
     * Summary of changeActiveLicense
     */
    protected function changeActiveLicense(User $user): float
    {
        /** @var null|License $activeLicense */
        $activeLicense = $user->activeLicense;

        if (! $activeLicense) {
            return 0.0;
        }
        $activeLicense->update([
            'status' => LicenseStatusEnum::CHANGED,
        ]);

        return $this->calcLicenseProrata($activeLicense);
    }

    protected function calcLicenseProrata(License $license): float
    {
        $remainDays = $this->calcRemainDays($license);

        return ($remainDays * (1000 * $this->calcPriceByDay($license))) / 1000;
    }

    protected function calcPriceByDay(License $license): float
    {
        $days = $this->calcCompleteUsableDays($license) ?: 1;

        return (($license->price_paid * 1000) / $days) / 1000;
    }

    protected function calcRemainDays(License $license): int
    {
        $usableDays = $this->calcCompleteUsableDays($license);
        $usedDays = \intval($license->starts_at->diffInDays(now()));

        return $usableDays - $usedDays;
    }

    protected function calcCompleteUsableDays(License $license): int
    {
        return \intval($license->starts_at->diffInDays($license->expires_at));
    }

    public function cancelLicense(License $license): void
    {
        DB::transaction(function () use ($license) {
            $license->update(['status' => LicenseStatusEnum::CANCELED]);

            LicenseCanceled::dispatch($license->licensable, $license->plan, $license);
        });
    }

    public function activateLicense(License $license): void
    {
        DB::transaction(function () use ($license) {
            $licensable = $license->licensable;
            $oldLicense = $this->deactivateLicense($licensable);
            $license->update([
                'status' => LicenseStatusEnum::ACTIVE,
            ]);

            if ($oldLicense) {
                LicenseChanged::dispatch($licensable, $oldLicense->plan, $oldLicense);
            }
            LicenseActivated::dispatch($licensable, $license->plan, $license);
        });
    }

    protected function deactivateLicense(HasLicenseHandling $licensable): ?License
    {
        $activeLicense = $licensable->activeLicense;

        if ($activeLicense) {
            $activeLicense->update([
                'status' => LicenseStatusEnum::CHANGED,
                'expires_at' => now(),
            ]);
        }

        return $activeLicense;
    }
}
