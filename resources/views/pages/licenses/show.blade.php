@use ('App\Libraries\Utils\DatetimeFormatter')
@push ('styling')
    @vite ([
        'resources/css/pages/generic/default.css',
        'resources/css/pages/generic/table.css',
        'resources/css/pages/generic/show.css',
    ])
@endpush

<x-layout title="Detalhes da licença">
    <x-packs.header>
        <x-packs.page-heading-row heading="Detalhes da licença">
            <div class="dropdown top-right-item">
                <x-atoms.button
                    class="btn-secondary dropdown-toggle"
                    data-bs-toggle="dropdown"
                    aria-expanded="false"
                >
                    <i class="bi bi-menu-button-wide"></i>
                </x-atoms.button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        <x-atoms.button
                            class="dropdown-item d-flex gap-2"
                            format="anchor"
                            href="{{ route('licenses.index') }}"
                        >
                            <i class="bi bi-box-arrow-in-down-left"></i>
                            <span>Licenças</span>
                        </x-atoms.button>
                    </li>
                </ul>
            </div>
        </x-packs.page-heading-row>
    </x-packs.header>
    <main class="bg-secondary-subtle create-main main-default">
        <section class="content bg-light d-flex flex-column row-gap-3">
            <fieldset
                class="border border-1 border-dark rounded-1 fieldset-tag"
            >
                <legend class="field-legend bg-light">Licensa</legend>
                <div class="data-box">
                    <div class="label">Preço pago:</div>
                    <div>{{ $parsePrice($license->price_paid->toFloat()) }}</div>
                    <div class="label">Início:</div>
                    <div>{{ DatetimeFormatter::formatToDate($license->starts_at) }}</div>
                    <div class="label">Expiração:</div>
                    <div>{{ DatetimeFormatter::formatToDate($license->expires_at) }}</div>
                    <div class="label">Status:</div>
                    <div>{{ $license->status->toString() }}</div>
                    <div class="label">Recorrente:</div>
                    <div>{{ $license->is_recurring ? 'Sim' : 'Não' }}</div>
                </div>
            </fieldset>
            <fieldset
                class="border border-1 border-dark rounded-1 fieldset-tag"
            >
                <legend class="field-legend bg-light">Adicionais</legend>
                <ul class="list-group">
                    @forelse ($license->additionals as $additional)
                        <li class="list-group-item">
                            {{ $loop->iteration }}.
                            {{ $additional->summary }}
                        </li>
                    @empty
                        <li class="list-group-item text-danger">Sem Adicionais</li>
                    @endforelse
                </ul>
            </fieldset>
            <fieldset
                class="border border-1 border-dark rounded-1 fieldset-tag"
            >
                <legend class="field-legend bg-light">Plano</legend>
                <div class="data-box">
                    <div class="label">Nome:</div>
                    <div>{{ $license->plan->name }}</div>
                    <div class="label">Faturamento:</div>
                    <div>{{ $license->plan->billing_period->toString() }}</div>
                    <div class="label">Preço base:</div>
                    <div>{{ $parsePrice($license->plan->price) }}</div>
                </div>
            </fieldset>
            <fieldset
                class="border border-1 border-dark rounded-1 fieldset-tag"
            >
                <legend class="field-legend bg-light">Proprietário</legend>
                <div class="data-box">
                    <div class="label">Nome:</div>
                    <div>
                        <a
                            href="{{
                                $licensableRoute($license->licensable)
                            }}"
                            class="text-truncate text-decoration-none text-info border-0 ps-0"
                        >
                            {{ $license->licensable->name }}
                        </a>
                    </div>
                    <div class="label">E-mail:</div>
                    <div>{{ $license->licensable?->getBillingEmail() ?? 'N/A' }}</div>
                </div>
            </fieldset>
        </section>
    </main>
</x-layout>
