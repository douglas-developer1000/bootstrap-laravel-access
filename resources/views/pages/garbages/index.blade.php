@use ('App\Models\Product')
@use ('App\Libraries\Utils\DatetimeFormatter')
@use ('App\Libraries\Enums\StockExitTypeEnum')
@use ('App\Models\StockExit')
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

<x-layout title="Perdas">
    <x-packs.header>
        <x-packs.page-heading-row
            heading="Perdas"
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
                    @can ('showPersonalUse', StockExit::class)
                        <li>
                            <x-atoms.button
                                class="dropdown-item"
                                format="anchor"
                                href="{{
                                    route('stocks.exits.create', [
                                        'exitType' => StockExitTypeEnum::PERSONAL_USE->value
                                    ])
                                }}"
                                title="Utilizar estoque como {{ StockExitTypeEnum::PERSONAL_USE->toString() }}"
                                :disabled="!$hasAccess('createExit', [StockExit::class, StockExitTypeEnum::PERSONAL_USE])"
                            >
                                <i class="bi bi-plus-lg"></i>
                                <span
                                    >{{ StockExitTypeEnum::PERSONAL_USE->toString() }}</span
                                >
                            </x-atoms.button>
                        </li>
                    @endcan
                    @can ('showDemonstration', StockExit::class)
                        <li>
                            <x-atoms.button
                                class="dropdown-item"
                                format="anchor"
                                href="{{
                                    route('stocks.exits.create', [
                                        'exitType' => StockExitTypeEnum::DEMONSTRATION->value
                                    ])
                                }}"
                                title="Utilizar estoque como {{ StockExitTypeEnum::DEMONSTRATION->toString() }}"
                                :disabled="!$hasAccess('createExit', [StockExit::class, StockExitTypeEnum::DEMONSTRATION])"
                            >
                                <i class="bi bi-plus-lg"></i>
                                <span
                                    >{{ StockExitTypeEnum::DEMONSTRATION->toString() }}</span
                                >
                            </x-atoms.button>
                        </li>
                    @endcan
                    @can ('showLoss', StockExit::class)
                        <li>
                            <x-atoms.button
                                class="dropdown-item"
                                format="anchor"
                                href="{{
                                    route('stocks.exits.create', [
                                        'exitType' => StockExitTypeEnum::LOSS->value
                                    ])
                                }}"
                                title="Utilizar estoque como {{ StockExitTypeEnum::LOSS->toString() }}"
                                :disabled="!$hasAccess('createExit', [StockExit::class, StockExitTypeEnum::LOSS])"
                            >
                                <i class="bi bi-plus-lg"></i>
                                <span
                                    >{{ StockExitTypeEnum::LOSS->toString() }}</span
                                >
                            </x-atoms.button>
                        </li>
                    @endcan
                </ul>
        </x-packs.page-heading-row>
    </x-packs.header>
    <main class="bg-secondary-subtle list-main">
        <section class="content bg-light">
            <div class="d-flex flex-wrap justify-content-between row-gap-2">
                <x-packs.filter-form-checks
                    class="gap-3"
                    :checkboxes="$checkboxesData"
                />
            </div>
            <div class="d-flex justify-content-end flex-grow-1 column-gap-2">
                <x-organisms.confirm-rm-group-btn
                    :routeParams="['key' => 'remotion', 'stockExitList' => 'list']"
                    route="garbages.group.destroy"
                    heading="Remover estas perdas?"
                    positive-text="Remover perdas"
                    title="Remover perdas selecionadas"
                >
                    Esta operação removerá cada perda selecionada
                    permanentemente, liberando todas as entradas de estoque
                    delas para uso no sistema.
                </x-organisms.confirm-rm-group-btn>
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
                        <x-atoms.table-head
                            sort="type"
                            class="next-overflow"
                        >
                            Tipo</x-atoms.table-head
                        >
                        <x-atoms.table-head sort="product">
                            Produto</x-atoms.table-head
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
                    @forelse ($models($list) as $exit)
                        <tr>
                            <td>
                                <input
                                    type="checkbox"
                                    value="{{ $exit->id }}"
                                    class="form-check-input cursor-pointer multiselection-item"
                                    @disabled (!$hasAccess('deleteGarbage', $exit))
                                />
                            </td>
                            <td>
                                <div>{{$exit->type->toString()}}</div>
                            </td>
                            <td>
                                <div>{{$exit->product}}</div>
                            </td>
                            <td>
                                <div class="ellipsis">{{$exit->cost}}</div>
                            </td>
                            <td>
                                {{ DatetimeFormatter::formatToDate($exit->created_at) }}
                            </td>
                            <td>
                                <div
                                    class="w-100 d-flex justify-content-between gap-1"
                                >
                                    <x-organisms.confirm-rm-btn
                                        :routeParams="['exit' => $exit->id]"
                                        route="garbages.destroy"
                                        heading="Remover esta perda?"
                                        positiveText="Remover perda"
                                        title="Remover perda"
                                        :disabled="!$hasAccess('deleteGarbage', $exit)"
                                    >
                                        Esta operação removerá esta perda
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
                                Sem perdas para o filtro atual
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
