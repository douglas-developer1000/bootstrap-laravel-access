@use ('App\Models\Product')
@use ('App\Models\StockExit')
@use ('App\Models\Exchange')
@use ('App\Libraries\Utils\DatetimeFormatter')
@use ('App\Libraries\Enums\StockExitTypeEnum')
@push ('styling')
    @vite ([
        'resources/css/pages/generic/index.css',
        'resources/css/pages/generic/table.css'
    ])
@endpush
@push ('ecmascript-bottom')
    @vite ([
        'resources/js/pages/generic/multiselection.ts'
    ])
@endpush

<x-layout title="Trocas">
    <x-packs.header>
        <x-packs.page-heading-row
            heading="Trocas"
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
                    <li>
                        <x-atoms.button
                            class="dropdown-item"
                            format="anchor"
                            href="{{
                                route('stocks.exits.create', [
                                    'exitType' => StockExitTypeEnum::EXCHANGE->value
                                ])
                            }}"
                            title="Utilizar estoque como troca"
                            :disabled="!$hasAccess('createExit', [StockExit::class, StockExitTypeEnum::EXCHANGE])"
                        >
                            <i class="bi bi-plus-lg"></i>
                            <span>Troca</span>
                        </x-atoms.button>
                    </li>
                </ul></x-packs.page-heading-row
        >
    </x-packs.header>
    <main class="bg-secondary-subtle list-main">
        <section class="content bg-light">
            <div class="d-flex flex-wrap justify-content-between row-gap-2">
                <x-packs.term-search
                    label-text="Pessoa:"
                    placeholder="Insira o nome da pessoa"
                />
                <div
                    class="d-flex justify-content-end flex-grow-1 column-gap-2"
                >
                    <x-organisms.confirm-rm-group-btn
                        :routeParams="['key' => 'remotion', 'exchangeList' => 'list']"
                        route="exchanges.group.destroy"
                        heading="Remover estas trocas?"
                        positive-text="Remover trocas"
                        title="Remover trocas selecionadas"
                    >
                        Esta operação removerá cada troca selecionada
                        permanentemente, liberando todas as entradas de estoque
                        delas para uso no sistema.
                    </x-organisms.confirm-rm-group-btn>
                </div>
            </div>
            <x-molecules.table-index :qtyBtns="1.25">
                <x-slot:cols>
                    <col class="col-remain-product" />
                    <col class="col-remain-cost" />
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
                        <x-atoms.table-head sort="person">
                            Pessoa</x-atoms.table-head
                        >
                        <x-atoms.table-head
                            colRemain
                            sort="product"
                        >
                            Product</x-atoms.table-head
                        >
                        <x-atoms.table-head
                            colRemain
                            sort="cost"
                        >
                            Custo</x-atoms.table-head
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
                    @forelse ($models($list) as $exchange)
                        <tr>
                            <td>
                                <input
                                    type="checkbox"
                                    value="{{ $exchange->id }}"
                                    class="form-check-input cursor-pointer multiselection-item"
                                />
                            </td>
                            <td>
                                <div>{{$exchange->person}}</div>
                            </td>
                            <td>
                                <div>{{$exchange->product}}</div>
                            </td>
                            <td>
                                <div class="ellipsis">{{$exchange->cost}}</div>
                            </td>
                            <td>
                                {{ DatetimeFormatter::formatToDate($exchange->created_at) }}
                            </td>
                            <td>
                                <div
                                    class="w-100 d-flex justify-content-between gap-1"
                                >
                                    <x-organisms.confirm-rm-btn
                                        :routeParams="['exchange' => $exchange->id, 'exit' => $exchange->exit->id]"
                                        route="exchanges.destroy"
                                        heading="Remover esta troca?"
                                        positiveText="Remover troca"
                                        title="Remover troca"
                                        :disabled="!$hasAccess('delete', [Exchange::class, $exchange, $exchange->exit])"
                                    >
                                        Esta operação removerá esta troca
                                        permanentemente, liberando suas entradas
                                        de estoque para uso no sistema.
                                    </x-organisms.confirm-rm-btn>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td
                                colspan="6"
                                class="no-values"
                            >
                                Sem trocas para o filtro atual
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
