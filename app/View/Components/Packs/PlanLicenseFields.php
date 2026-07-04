<?php

declare(strict_types=1);

namespace App\View\Components\Packs;

use App\Models\Plan;
use App\Models\Role;
use App\Services\ChecklistService;
use Illuminate\Support\Collection;
use Illuminate\View\Component;

final class PlanLicenseFields extends Component
{
    /** @var Collection<Plan> $plans */
    public Collection $plans;
    /** @var Collection<string, Role> $additionalRoles */
    public Collection $additionalRoles;
    public string $label;
    public string $phrase;

    public function __construct(
        protected ChecklistService $checkSvc,
        public bool $required = false,

        Collection|null $plans = NULL,
        Collection|null $additionalRoles = NULL,
        ?string $label = NULL,
        ?string $phrase = NULL,
    ) {
        $this->plans = $plans ?? collect();
        $this->additionalRoles = $additionalRoles ?? collect();
        $this->label = $label ?? 'Planos:';
        $this->phrase = $phrase ?? 'Selecione um plano...';
    }

    public function render()
    {
        return view('components.packs.plan-license-fields', [
            'boxChecked' => $this->checkSvc->boxChecked(...),
            'planId' => '',
            'additionals' => [],
            'recurring' => false,
        ]);
    }
}
