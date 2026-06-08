@use ('App\Models\Product')
@use ('App\Libraries\Utils\DatetimeFormatter')
@use ('App\Libraries\Enums\PaymentTypeEnum')
@push ('styling')
    @vite ([
        'resources/css/pages/generic/index.css',
        'resources/css/pages/generic/table.css',
        'resources/css/pages/sales/index.css',
    ])
@endpush
@push ('ecmascript-bottom')
    @vite ([
        'resources/js/pages/generic/multiselection.ts'
    ])
@endpush

<x-layout title="Vendas">
    <x-packs.header>
        <x-packs.page-heading-row
            heading="Vendas"
            class="page-heading-row-custom"
        >
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
                            class="dropdown-item"
                            format="anchor"
                            href="{{ route('stocks.index') }}"
                            :disabled="!$hasAccess('viewAny', Product::class)"
                        >
                            <i class="bi bi-boxes"></i>
                            <span>Estoques</span>
                        </x-atoms.button>
                    </li>
                </ul></x-packs.page-heading-row
        >
    </x-packs.header>
    <main class="bg-secondary-subtle list-main">
        <section class="content bg-light">
            <div class="d-flex flex-wrap justify-content-between row-gap-2">
                <x-packs.filter-form-checks
                    class="gap-3 w-100 mb-3"
                    :checkboxes="[
                        PaymentTypeEnum::CARD->value => PaymentTypeEnum::CARD->toString(),
                        PaymentTypeEnum::MONEY->value => PaymentTypeEnum::MONEY->toString(),
                        PaymentTypeEnum::PIX->value => PaymentTypeEnum::PIX->toString()
                    ]"
                />
                <x-packs.term-search
                    label-text="Cliente:"
                    placeholder="Insira o nome do cliente"
                />
                <div
                    class="d-flex justify-content-end flex-grow-1 column-gap-2"
                >
                    <x-organisms.confirm-rm-group-btn
                        :routeParams="['key' => 'remotion', 'saleList' => 'list']"
                        route="sales.group.destroy"
                        heading="Remover estas vendas?"
                        positive-text="Remover vendas"
                        title="Remover vendas selecionadas"
                    >
                        Cada venda selecionada, e todos os dados relacionados em
                        cada uma delas, serão removidos permanentemente.
                    </x-organisms.confirm-rm-group-btn>
                </div>
            </div>
            <x-molecules.table-index :qtyBtns="1">
                <x-slot:cols>
                    <col class="col-remain-value" />
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
                        <x-atoms.table-head sort="customer">
                            Cliente</x-atoms.table-head
                        >
                        <x-atoms.table-head
                            colRemain
                            sort="value"
                        >
                            Valor</x-atoms.table-head
                        >
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
                    @forelse ($models($list) as $sale)
                        <tr>
                            <td>
                                <input
                                    type="checkbox"
                                    value="{{ $sale->id }}"
                                    class="form-check-input cursor-pointer multiselection-item"
                                />
                            </td>
                            <td>
                                <div>{{$sale->customer}}</div>
                            </td>
                            <td>
                                <x-atoms.button
                                    class="text-truncate text-decoration-none text-info border-0 ps-0"
                                    format="anchor"
                                    href="{{ route('sales.show', ['sale' => $sale->id]) }}"
                                    title="Mostrar venda"
                                    :disabled="!$hasAccess('show', $sale)"
                                >
                                    {{ $sale->value }}
                                </x-atoms.button>
                            </td>
                            <td>
                                {{ DatetimeFormatter::formatToDate($sale->created_at) }}
                            </td>
                            <td>
                                <div
                                    class="w-100 d-flex justify-content-between gap-1"
                                >
                                    <x-organisms.confirm-rm-btn
                                        :routeParams="['sale' => $sale->id]"
                                        route="sales.destroy"
                                        heading="Remover esta venda?"
                                        positiveText="Remover venda"
                                        title="Remover venda"
                                        :disabled="!$hasAccess('delete', $sale)"
                                    >
                                        Esta venda e todos os seus dados
                                        relacionados serão removidos
                                        permanentemente.
                                    </x-organisms.confirm-rm-btn>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td
                                colspan="5"
                                class="no-values"
                            >
                                Sem vendas para o filtro atual
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
