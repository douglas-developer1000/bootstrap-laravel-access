@push ('styling')
    @vite ([
        'resources/css/pages/generic/default.css',
        'resources/css/pages/generic/show.css',
        'resources/css/pages/settings/plans/show.css',
    ])
@endpush
@pushIf(!$pendingOrActive, 'ecmascript-bottom')
    @vite ([
        'resources/js/pages/settings/plans/show.ts',
    ])
@endPushIf

<x-layout title="{{ $title }}">
    <x-packs.header>
        <x-packs.page-heading-row heading="{{ $title }}" class="page-heading-row-custom">
            <div class="top-right-item d-flex gap-2">
                <x-atoms.button
                    class="btn-secondary"
                    format="anchor"
                    href="{{ route('plans.view.index') }}"
                >
                    <i class="bi bi-arrow-return-left"></i>
                </x-atoms.button>
            </div>
        </x-packs.page-heading-row>
    </x-packs.header>
    <main class="bg-secondary-subtle main-default">
        <section class="content bg-light">
            <fieldset
                class="border border-1 border-dark rounded-1 pb-4 fieldset-tag"
            >
                <legend class="field-legend bg-light text-info-emphasis">{{ $plan->name }}</legend>
                <div class="text-center">{{ $plan->description }}</div>
                <div class="payment">
                    <div class="item left">
                        Pagamento
                    </div>
                    <div class="item right">
                        {{ $plan->billing_period->toString() }}
                    </div>
                </div>
                <form
                    method="post"
                    action="{{
                        route('plans.view.handle', [
                            'plan' => $plan->slug
                        ])
                    }}"
                >
                    @if (!$pendingOrActive)
                        <div class="card">
                            <div class="card-header text-center">Créditos restantes</div>
                            <div class="card-body">
                                <div class="checkout-data-item">
                                    <div class="checkout-data-item-label">em carteira:</div>
                                    <div class="checkout-data-item-value">{{ $parsePrice($checkoutData['core']['wallet_balance']) }}</div>
                                </div>
                                <div class="checkout-data-item">
                                    <div class="checkout-data-item-label">em seu plano:</div>
                                    <div class="checkout-data-item-value">{{ $parsePrice($checkoutData['core']['prorata_discount']) }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header text-center">Você pagará:</div>
                            <div class="card-body">
                                <div class="final-price" data-plan-price="{{ $checkoutData['core']['new_plan_price'] }}" data-additional-price="{{ $checkoutData['additionalPrice'] }}" data-discount="{{ $checkoutData['core']['internal_credits'] }}">
                                    @if ($checkoutData['core']['final_price']->isZero())
                                        <span>NADA!</span>
                                        <span>INICIO IMEDIATO!</span>
                                    @else
                                        <span>{{ $parsePrice($checkoutData['core']['final_price']) }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @elseif ($isSamePlan($plan, $activeLicense))
                        <div class="card">
                            <div class="card-header text-center">
                                Você pagou:
                            </div>
                            <div class="card-body">
                                <div class="final-price">{{ $parsePrice($activeLicense->paidInvoicesAmount) }}</div>
                            </div>
                        </div>
                    @elseif ($isSamePlan($plan, $pendingLicense))
                        <div class="card">
                            <div class="card-header text-center">
                                Você pagará:
                            </div>
                            <div class="card-body">
                                <div class="final-price">{{ $parsePrice($pendingLicense->pendingInvoicesAmount) }}</div>
                            </div>
                        </div>
                    @endif

                    <div class="fs-4 my-2 ps-3 fw-medium">Opções:</div>
                    <div class="ps-3">
                        <x-molecules.input-check
                            name="is_recurring"
                            :checked="old('is_recurring', false)"
                            :disabled="$pendingOrActive"
                        >
                            Plano recorrente
                        </x-molecules.input-check>
                    </div>
                    <div class="ps-3">
                        <div class="fs-5 mb-3 fw-medium">Recursos adicionais:</div>
                        @forelse ($additionals as $role)
                            <div>
                                <x-molecules.input-check
                                    name="additionals[]"
                                    :value="$role->name"
                                    :checked="collect(old('additionals', []))->contains($role->name)"
                                    :disabled="$pendingOrActive"
                                >
                                    {{ $role->summary }}
                                </x-molecules.input-check>
                            </div>
                        @empty
                            <ul class="list-group">
                                <li class="list-group-item text-danger">
                                    Sem adicionais
                                </li>
                            </ul>
                        @endforelse
                    </div>

                    <div>
                        <div class="fs-5 fw-medium ps-3 my-2">Você tem direito a:</div>
                        <ul class="list-group">
                            @foreach ($roleDescriptions['core'] as $description)
                                <li class="list-group-item">
                                    {{ $description }}
                                </li>
                            @endforeach
                        </ul>
                    </div>
                    @if ($roleDescriptions['additional']->isNotEmpty())
                        <div>
                            <div class="fs-5 fw-medium ps-3 mt-4 mb-2">
                                Opcionalmente você pode:
                            </div>
                            <ul class="list-group">
                                @foreach ($roleDescriptions['additional'] as $description)
                                    <li class="list-group-item">
                                        {{ $description }}
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @csrf
                    <x-atoms.button
                        format="btn"
                        type="submit"
                        class="btn-primary mx-auto d-block mt-3"
                    >
                        {{ $btnContent }}
                    </x-atoms.button>
                </form>
            </fieldset>
        </section>
    </main>
</x-layout>