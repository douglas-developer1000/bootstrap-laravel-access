@use ('App\Libraries\Utils\DatetimeFormatter')
@push ('styling')
    @vite ([
        'resources/css/pages/generic/default.css',
        'resources/css/pages/generic/table.css',
        'resources/css/pages/generic/show.css',
    ])
@endpush

<x-layout title="Visualizar Plano">
    <x-packs.header>
        <x-packs.page-heading-row>
            <x-slot:heading>
                Visualizar Plano:
                <span class="text-primary ms-2">{{ $plan->name }}</span>
            </x-slot:heading>
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
                            href="{{ route('plans.index') }}"
                        >
                            <i class="bi bi-credit-card"></i>
                            <span>Planos</span>
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
                <legend class="field-legend bg-light">Dados do Plano</legend>
                <div class="fieldset-top-btn">
                    <x-atoms.button
                        format="anchor"
                        class="btn-secondary"
                        href="{{
                            route('plans.edit', $plan->slug)
                        }}"
                    >
                        <i class="bi bi-wrench"></i>
                    </x-atoms.button>
                </div>
                <div class="data-box">
                    <div class="label">Nome:</div>
                    <div>{{ $plan->name }}</div>
                    <div class="label">Descrição:</div>
                    <div>{{ $plan->description ?? 'N/A' }}</div>
                    <div class="label">Price:</div>
                    <div>{{ $plan->price }}</div>
                    <div class="label">Faturamento:</div>
                    <div>{{ $plan->billing_period->toString() }}</div>
                    <div class="label">Criação:</div>
                    <div>
                        {{ DatetimeFormatter::formatToDate($plan->created_at) }}
                    </div>
                </div>
            </fieldset>
            <fieldset
                class="border border-1 border-dark rounded-1 fieldset-tag"
            >
                <legend class="field-legend bg-light">Papéis (vigentes)</legend>
                <ul class="list-group list-group-flush">
                    @foreach ($roles as $role)
                        <li class="list-group-item">{{ $role->name }}</li>
                    @endforeach
                </ul>
            </fieldset>
            <fieldset
                class="border border-1 border-dark rounded-1 fieldset-tag"
            >
                <legend class="field-legend bg-light">
                    Permissões (vigentes)
                </legend>
                <ul class="list-group list-group-flush">
                    @forelse ($permissions as $perm)
                        <li class="list-group-item">{{ $perm->name }}</li>
                    @empty
                        <li class="list-group-item text-danger">
                            Sem permissões vigentes
                        </li>
                    @endforelse
                </ul>
            </fieldset>
            <fieldset
                class="border border-1 border-dark rounded-1 fieldset-tag"
            >
                <legend class="field-legend bg-light">
                    Papéis (adicionais)
                </legend>
                <ul class="list-group list-group-flush">
                    @forelse ($additionalRoles as $role)
                        <li class="list-group-item">{{ $role->name }}</li>
                    @empty
                        <li class="list-group-item text-danger">
                            Sem adicionais
                        </li>
                    @endforelse
                </ul>
            </fieldset>
            <fieldset
                class="border border-1 border-dark rounded-1 fieldset-tag"
            >
                <legend class="field-legend bg-light">
                    Permissões (adicionais)
                </legend>
                <ul class="list-group list-group-flush">
                    @forelse ($additionalPermissions as $perm)
                        <li class="list-group-item">{{ $perm->name }}</li>
                    @empty
                        <li class="list-group-item text-danger">
                            Sem permissões adicionais
                        </li>
                    @endforelse
                </ul>
            </fieldset>
        </section>
    </main>
</x-layout>
