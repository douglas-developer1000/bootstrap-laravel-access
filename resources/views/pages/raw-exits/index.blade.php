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

<x-layout title="Saídas">
    <x-packs.header>
        <x-packs.page-heading-row
            heading="Saídas"
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
                    @can ('showRaw', StockExit::class)
                        <li>
                            <x-atoms.button
                                class="dropdown-item"
                                format="anchor"
                                href="{{
                                    route('stocks.exits.create', [
                                        'exitType' => StockExitTypeEnum::RAW->value
                                    ])
                                }}"
                                title="Utilizar estoque como {{ StockExitTypeEnum::RAW->toString() }}"
                                :disabled="!$hasAccess('createExit', [StockExit::class, StockExitTypeEnum::RAW])"
                            >
                                <i class="bi bi-plus-lg"></i>
                                <span
                                    >{{ StockExitTypeEnum::RAW->toString() }}</span
                                >
                            </x-atoms.button>
                        </li>
                    @endcan
                </ul>
        </x-packs.page-heading-row>
    </x-packs.header>
    <main class="bg-secondary-subtle list-main">
        <section class="content bg-light">
            <div class="d-flex justify-content-end flex-grow-1 column-gap-2">
                <x-organisms.confirm-rm-group-btn
                    :routeParams="['key' => 'remotion', 'stockExitList' => 'list']"
                    route="raw.exits.group.destroy"
                    heading="Remover estas saídas?"
                    positive-text="Remover saídas"
                    title="Remover saídas selecionadas"
                >
                    Esta operação removerá cada saída selecionada
                    permanentemente, liberando todas as entradas de estoque
                    delas para uso no sistema.
                </x-organisms.confirm-rm-group-btn>
            </div>
            <x-molecules.table-index :qtyBtns="1.25">
                <x-slot:cols>
                    <col class="col-remain-product" />
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
                                    @disabled (!$hasAccess('delete', $exit))
                                />
                            </td>
                            <td>
                                <div>{{$exit->type->toString()}}</div>
                            </td>
                            <td>
                                <div>{{$exit->product}}</div>
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
                                        route="raw.exits.destroy"
                                        heading="Remover esta saída?"
                                        positiveText="Remover saída"
                                        title="Remover saída"
                                        :disabled="!$hasAccess('delete', $exit)"
                                    >
                                        Esta operação removerá esta saída
                                        permanentemente, liberando suas entradas
                                        de estoque para uso no sistema.
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
                                Sem saídas para o filtro atual
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
