<?php

declare(strict_types=1);

namespace App\View\Components\Packs;

use App\Facades\CheckList;
use App\Models\Plan;
use App\Models\Role;
use Illuminate\Support\Collection;
use Illuminate\View\Component;

final class PlanLicenseFields extends Component
{
    /** @var Collection<Plan> */
    public Collection $plans;

    /** @var Collection<string, Role> */
    public Collection $additionalRoles;

    public string $label;

    public string $phrase;

    public function __construct(
        public bool $required = false,

        ?Collection $plans = null,
        ?Collection $additionalRoles = null,
        ?string $label = null,
        ?string $phrase = null,
    ) {
        $this->plans = $plans ?? collect();
        $this->additionalRoles = $additionalRoles ?? collect();
        $this->label = $label ?? 'Planos:';
        $this->phrase = $phrase ?? 'Selecione um plano...';
    }

    public function render()
    {
        return view('components.packs.plan-license-fields', [
            'boxChecked' => CheckList::boxChecked(...),
            'planId' => '',
            'additionals' => [],
            'recurring' => false,
        ]);
    }
}
