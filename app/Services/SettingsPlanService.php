<?php

declare(strict_types=1);

namespace App\Services;

use App\Events\LicenseAbandoned;
use App\Events\LicenseCanceled;
use App\Events\LicenseReactivated;
use App\Libraries\Enums\InvoiceStatusEnum;
use App\Models\Contracts\Licensable;
use App\Models\License;
use App\Models\Plan;
use App\Models\Role;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Facades\DB;

final class SettingsPlanService
{
    public function __construct(protected LicenseService $licenseSvc)
    {
        // ...
    }

    /**
     * @param  EloquentCollection<Plan>  $plans
     */
    public function separatePlanRoles(EloquentCollection $plans)
    {
        return $plans->mapWithKeys(function (Plan $plan) {
            $partition = $this->partitionPlanRoles($plan);

            return [
                $plan->slug => [
                    'core' => $partition['core'],
                    'additionals' => $partition['additionals'],
                ],
            ];
        });
    }

    /**
     * @return array{
     *     core: EloquentCollection<Role>,
     *     additionals: EloquentCollection<Role>,
     * }
     */
    public function partitionPlanRoles(Plan $plan): array
    {
        [$core, $additional] = $plan->roles->partition(
            fn (Role $role) => $role->pivot->additional === 0
        );

        return [
            'core' => $core,
            'additionals' => $additional,
        ];
    }

    public function makeShowBtnContent(Plan $plan, ?License $pendingLicense, ?License $activeLicense): string
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

    public function samePlan(Plan $plan, ?License $license): bool
    {
        return $plan->slug === $license?->plan->slug;
    }

    public function handleLicensablePlan(Licensable $licensable, Plan $plan, bool $isRecurring, array $additionalRoles): void
    {
        /** @var null|License */
        $activeLicense = $licensable->activeLicense()->with('plan')->first();
        $licenseSvc = $this->licenseSvc;

        DB::transaction(function () use ($plan, $licensable, $activeLicense, $licenseSvc, $isRecurring, $additionalRoles) {
            if ($this->samePlan($plan, $activeLicense)) {
                if ($activeLicense->isPreCancellable) {
                    $activeLicense->cancelLicense();
                    LicenseCanceled::dispatch($licensable, $activeLicense->plan, $activeLicense);
                } else {
                    $activeLicense->activateLicense();
                    LicenseReactivated::dispatch($licensable, $activeLicense->plan, $activeLicense);
                }

                return;
            }
            $pendingLicense = $licensable->pendingLicense;

            if ($this->samePlan($plan, $pendingLicense)) {
                $pendingLicense?->abandonLicense(
                    invoiceStatus: InvoiceStatusEnum::VOIDED,
                    reason: 'Desistência do usuário',
                );
                LicenseAbandoned::dispatch($licensable, $pendingLicense->plan, $pendingLicense);

                return;
            } else {
                $pendingLicense?->abandonLicense(
                    invoiceStatus: InvoiceStatusEnum::VOIDED,
                    reason: "Substituição de checkout (Plano {$plan->name})",
                );
            }

            $licenseSvc->bindPlan(
                plan: $plan,
                licensable: $licensable,
                isRecurring: $isRecurring,
                additionalRoles: $additionalRoles,
            );
            if ($pendingLicense) {
                LicenseAbandoned::dispatch($licensable, $pendingLicense->plan, $pendingLicense);
            }
        });
    }
}
