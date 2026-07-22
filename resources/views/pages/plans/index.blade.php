@use ('App\Models\Plan')
@use ('App\Facades\DateFormatter')
@push ('styling')
    @vite ([
        'resources/css/pages/generic/index.css',
        'resources/css/pages/generic/table.css',
    ])
@endpush
@push ('ecmascript-bottom')
    @vite ([
        'resources/js/pages/generic/multiselection.ts'
    ])
@endpush

<x-layout title="{{ $title }}">
    <x-packs.header>
        <x-packs.page-heading-row
            :heading="$title"
            class="page-heading-row-custom"
        >
            <div class="dropdown top-right-item d-flex gap-2">
                <form
                    action="{{ route('plans.flush') }}"
                    method="post"
                >
                    @csrf
                    <x-atoms.button
                        class="btn-secondary"
                        type="submit"
                    >
                        <i class="bi bi-database-fill-up"></i>
                    </x-atoms.button>
                </form>
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
                            title="Planos {{ $trashed ? 'ativos' : 'removidos' }}"
                            class="dropdown-item"
                            format="anchor"
                            href="{{
                                route(
                                    'plans.index',
                                    $trashed ? [] : ['trashed' => 1]
                                )
                            }}"
                        >
                            @if ($trashed)
                                <i class="bi bi-check-lg"></i>
                                Planos Ativos
                            @else
                                <i class="bi bi-trash"></i>
                                Planos Removidos
                            @endif
                        </x-atoms.button>
                    </li>
                    @if (!$trashed)
                        @can ('create', Plan::class)
                            <li>
                                <x-atoms.button
                                    class="dropdown-item"
                                    format="anchor"
                                    href="{{ route('plans.create') }}"
                                    :disabled="$roleToPlanEmpty"
                                >
                                    <i class="bi bi-plus-lg"></i>
                                    <span>Planos</span>
                                </x-atoms.button>
                            </li>
                        @endcan
                    @endif
                </ul>
            </div>
        </x-packs.page-heading-row>
    </x-packs.header>
    <main class="bg-secondary-subtle list-main">
        <section class="content bg-light">
            <x-molecules.block-error
                :keys="[
                    'destroy', 'remotion', 'remotion.*', 'restoration', 'restoration.*'
                ]"
            />
            <div class="d-flex flex-wrap justify-content-between row-gap-2">
                <x-packs.term-search
                    label-text="Nome:"
                    placeholder="Insira o nome do plano"
                />
                <div
                    class="d-flex justify-content-end flex-grow-1 column-gap-2"
                >
                    @if ($trashed)
                        <x-organisms.confirm-restore-group-btn
                            :routeParams="['key' => 'restoration', 'planList' => 'trashed']"
                            route="plans.group.restore"
                            heading="Restaurar estes planos?"
                            positive-text="Restaurar planos"
                            title="Restaurar planos selecionados"
                        >
                            Isso restaurará os planos selecionados e suas
                            utilizações relacionadas.
                        </x-organisms.confirm-restore-group-btn>
                    @else
                        <x-organisms.confirm-rm-group-btn
                            :routeParams="['key' => 'remotion', 'planList' => 'list']"
                            route="plans.group.destroy"
                            heading="Remover estes planos?"
                            positive-text="Remover planos"
                            title="Remover planos selecionados"
                        >
                            <div class="mb-3">Para cada plano selecionado:</div>
                            <div class="mb-1">
                                Se ele não possuir utilização, será removido
                                permanentemente.
                            </div>
                            <div>
                                Caso contrário, será removido apenas desta
                                listagem.
                            </div>
                        </x-organisms.confirm-rm-group-btn>
                    @endif
                </div>
            </div>
            <x-molecules.table-index
                :styleRows="[
                    'first' => 'width: 1.75em;',
                    'second' => 'width: 6.5em;',
                ]"
                :qtyBtns="$trashed ? 1 : 2"
            >
                <x-slot:cols>
                    <col
                        class="col-remain-billing_period"
                        style="visibility: visible; width: auto"
                    />
                    <col class="col-remain-created_at" />
                </x-slot:cols>
                <thead>
                    <tr>
                        <th scope="col">
                            <input
                                type="checkbox"
                                class="form-check-input cursor-pointer multiselection-all"
                            />
                        </th>
                        <x-atoms.table-head sort="name">
                            Nome</x-atoms.table-head
                        >
                        <x-atoms.table-head
                            colRemain
                            sort="billing_period"
                        >
                            Pagamento
                        </x-atoms.table-head>
                        <x-atoms.table-head
                            default
                            colRemain
                            sort="created_at"
                        >
                            Criação</x-atoms.table-head
                        >
                        <th
                            scope="col"
                            class="last-thdata"
                        >
                            Ações
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($models($list) as $plan)
                        <tr>
                            <td>
                                <input
                                    type="checkbox"
                                    value="{{ $plan->id }}"
                                    class="form-check-input cursor-pointer multiselection-item"
                                    @disabled ($trashed ? false : false)
                                />
                            </td>
                            <td>
                                <a
                                    href="{{
                                        route('plans.show', $plan->slug)
                                    }}"
                                    class="text-truncate text-decoration-none text-info border-0 ps-0"
                                >
                                    {{ $plan->name }}
                                </a>
                            </td>
                            <td>
                                <div class="text-truncate">
                                    {{ $plan->billing_period->toString() }}
                                </div>
                            </td>
                            <td>
                                {{ DateFormatter::formatToDate($plan->created_at) }}
                            </td>
                            <td>
                                <div
                                    class="w-100 d-flex justify-content-center gap-1"
                                >
                                    @if ($trashed)
                                        <x-organisms.confirm-restore-btn
                                            :routeParams="[
                                                'planDeleted' => $plan->id,
                                                ...($qs ?: [])
                                            ]"
                                            route="plans.restore"
                                            heading="Restaurar este plano?"
                                            positiveText="Restaurar plano"
                                            title="Restaurar plano"
                                        >
                                            Isso restaurará este plano e suas
                                            utilizações relacionadas.
                                        </x-organisms.confirm-restore-btn>
                                    @else
                                        <x-atoms.button
                                            format="anchor"
                                            class="btn-secondary"
                                            href="{{
                                                route('plans.edit', $plan->slug)
                                            }}"
                                        >
                                            <i class="bi bi-wrench"></i>
                                        </x-atoms.button>
                                        <x-organisms.confirm-rm-btn
                                            :routeParams="['plan' => $plan->slug]"
                                            route="plans.destroy"
                                            heading="Remover este plano?"
                                            positiveText="Remover plano"
                                            title="Remover plano"
                                        >
                                            <div class="mb-1">
                                                Se ele não possuir utilização,
                                                será removido permanentemente.
                                            </div>
                                            <div>
                                                Caso contrário, será removido
                                                apenas desta listagem.
                                            </div>
                                        </x-organisms.confirm-rm-btn>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td
                                colspan="5"
                                class="no-values"
                            >
                                Sem planos para o filtro atual
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </x-molecules.table-index>
            <x-molecules.root-pagination :paginator="$list" />
        </section>
        <x-packs.toast />
    </main>
</x-layout>
