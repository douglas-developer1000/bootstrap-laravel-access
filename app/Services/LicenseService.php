<?php

declare(strict_types=1);

namespace App\Services;

use App\Events\LicenseActivated;
use App\Events\LicenseCanceled;
use App\Events\LicenseChanged;
use App\Libraries\Enums\LicenseStatusEnum;
use App\Models\Contracts\HasLicenseHandling;
use App\Models\Contracts\Licensable;
use App\Models\Credit;
use App\Models\License;
use App\Models\Plan;
use App\Models\Role;
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
                            fn(string $table, string $class) => "WHEN \"{$class}\" THEN (SELECT `{$table}`.name FROM `{$table}` WHERE `{$table}`.id = licenses.licensable_id LIMIT 1)"
                        )->implode(' ')
                    )->append(' END) as licensableName')->toString()
                );
            }

            protected function getTableReferences(Collection $classes): Collection
            {
                return $classes->mapWithKeys(fn(string $class) => [
                    Str::of($class)->replace('\\', '\\\\')->toString() => new $class(),
                ])->map(fn(Model $instance) => $instance->getTable());
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
                    fn(LicenseStatusEnum $status) => $status->value
                )->mapWithKeys(fn(string $enumKey) => [
                    $enumKey => ! $qs->has($enumKey) || $request->boolean($enumKey),
                ]);
            }

            #[Override]
            public function attachQuery(Request $request, Builder $query): Builder
            {
                $lossTypes = $this->pickLicenseFilters($request)->filter(
                    fn(bool $presence) => $presence === true
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
        return collect(LicenseStatusEnum::cases())->mapWithKeys(fn(LicenseStatusEnum $status) => [
            $status->value => $status->toString(),
        ])->all();
    }

    public function hydrateLicense(array $licenses): Collection
    {
        return License::hydrate($licenses);
    }

    /**
     * @todo Enviar e-mail quando o preço da license for zerada ("Sua licença
     * foi renovada utilizando seus créditos!") ou no envio do boleto/pix avisando
     * que créditos foram utilizados, quando houver prorata.
     */
    public function bindPlan(Plan $plan, Licensable $licensable, bool $isRecurring = false, array $additionalRoles = []): License
    {
        $activeLicense = $licensable->activeLicense;
        /** @var float */
        $prorata = $activeLicense?->prorata ?? 0.0;
        $licensable->pendingLicense?->abandonLicense();

        if ($prorata >= $plan->price) {
            $activeLicense?->changePlan();

            $license = License::create([
                'plan_id' => $plan->id,
                'price_paid' => 0,
                'licensable_id' => $licensable->getKey(),
                'licensable_type' => $licensable->getMorphClass(),
                'starts_at' => now(),
                'expires_at' => $plan->billing_period->advance(now()),
                'status' => LicenseStatusEnum::ACTIVE,
                'is_recurring' => $isRecurring,
            ]);

            Credit::create([
                'licensable_type' => get_class($licensable),
                'licensable_id' => $licensable->getKey(),
                'amount' => $prorata - $plan->price,
                'description' => 'Sobra de prorata na troca de plano'
            ]);
            // TO-DO: Enviar um e-mail dizendo: 
            // "Sua licença foi renovada utilizando seus créditos!"

        } else {
            $license = License::create([
                'plan_id' => $plan->id,
                'price_paid' => $plan->price - $prorata,
                'licensable_id' => $licensable->getKey(),
                'licensable_type' => $licensable->getMorphClass(),
                'starts_at' => now(),
                'expires_at' => $plan->billing_period->advance(now()),
                'status' => LicenseStatusEnum::PENDING,
                'is_recurring' => $isRecurring,
            ]);
            if ($prorata > 0) {
                Credit::create([
                    'licensable_type' => get_class($licensable),
                    'licensable_id' => $licensable->getKey(),
                    'amount' => -$prorata,
                    'description' => 'Desconto final de prorata finalizando os créditos'
                ]);
            }
        }

        $additionals = Role::whereIn('name', $additionalRoles)->pluck('id')->all();

        $license->additionals()->sync($additionals);

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
            $oldLicense = NULL;
            $licensable = $license->licensable;
            if ($license->status === LicenseStatusEnum::PENDING) {
                $oldLicense = $licensable->activeLicense;
                $oldLicense?->changePlan();
            }
            $license->activateLicense();

            if ($oldLicense) {
                LicenseChanged::dispatch($licensable, $oldLicense->plan, $oldLicense);
            }
            LicenseActivated::dispatch($licensable, $license->plan, $license);
        });
    }
}
