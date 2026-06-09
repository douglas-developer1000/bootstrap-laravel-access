@use ('App\Models\ProductCategory')
@use ('App\Models\Supplier')
@use ('App\Models\Product')
@push ('styling')
    @vite ([
        'resources/css/pages/generic/index.css',
        'resources/css/pages/generic/table.css'
    ])
@endpush

@php
    $trashed = request()->boolean('trashed');
    $subject = $trashed ? 'Produtos removidos' : 'Estoque';
@endphp

@push ('ecmascript-bottom')
    @vite ([
        'resources/js/pages/generic/multiselection.ts'
    ])
@endpush

<x-layout title="{{ $subject }}">
    <x-packs.header>
        <x-packs.page-heading-row
            heading="{{ $subject }}"
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
                            title="Produtos {{ $trashed ? 'ativos' : 'removidos' }}"
                            class="dropdown-item"
                            format="anchor"
                            href="{{
                                route(
                                    'stocks.index',
                                    $trashed ? [] : ['trashed' => 1]
                                )
                            }}"
                        >
                            @if ($trashed)
                                <i class="bi bi-box-seam"></i>
                                Produtos Ativos
                            @else
                                <i class="bi bi-trash"></i>
                                Produtos Removidos
                            @endif
                        </x-atoms.button>
                    </li>
                    @if (!$trashed)
                        <li>
                            <x-atoms.button
                                class="dropdown-item d-flex gap-2"
                                format="anchor"
                                href="{{ route('product-categories.index') }}"
                                :disabled="!$hasAccess('viewAny', ProductCategory::class)"
                            >
                                <i class="bi bi-ui-checks-grid"></i>
                                <span>Categorias</span>
                            </x-atoms.button>
                        </li>
                        <li>
                            <x-atoms.button
                                class="dropdown-item"
                                format="anchor"
                                href="{{ route('suppliers.index') }}"
                                :disabled="!$hasAccess('viewAny', Supplier::class)"
                            >
                                <i class="bi bi-box-seam"></i>
                                <span>Fornecedores</span>
                            </x-atoms.button>
                        </li>
                        <li>
                            <x-atoms.button
                                class="dropdown-item"
                                format="anchor"
                                href="{{ route('products.create') }}"
                                :disabled="!$hasAccess('create', Product::class)"
                            >
                                <i class="bi bi-plus-lg"></i>
                                <span>Produto</span>
                            </x-atoms.button>
                        </li>
                    @endif
                </ul>
            </div>
        </x-packs.page-heading-row>
    </x-packs.header>
    <main class="bg-secondary-subtle list-main">
        <section class="content bg-light">
            <x-molecules.block-error
                :keys="[
                    'remotion', 'remotion.*', 'restoration', 'restoration.*'
                ]"
            />
            <div class="d-flex flex-wrap justify-content-between row-gap-2">
                <x-packs.term-search
                    label-text="Nome:"
                    placeholder="Insira o nome do produto"
                />
                <div
                    class="d-flex justify-content-end flex-grow-1 column-gap-2"
                >
                    @if ($trashed)
                        <x-organisms.confirm-restore-group-btn
                            :routeParams="['key' => 'restoration', 'productList' => 'trashed']"
                            route="products.group.restore"
                            heading="Restaurar estes produtos?"
                            positive-text="Restaurar produtos"
                            title="Restaurar produtos selecionados"
                        >
                            Isso restaurará os produtos selecionados e seus
                            estoques relacionados.
                        </x-organisms.confirm-restore-group-btn>
                    @else
                        <x-organisms.confirm-rm-group-btn
                            :routeParams="['key' => 'remotion', 'productList' => 'list']"
                            route="products.group.destroy"
                            heading="Remover estes produtos?"
                            positive-text="Remover produtos"
                            title="Remover vários produtos"
                        >
                            <div class="mb-3">
                                Para cada produto selecionado:
                            </div>
                            <div class="mb-1">
                                Se ele não possuir histórico de estoque, será
                                removido permanentemente.
                            </div>
                            <div>
                                Caso contrário, será removido apenas desta
                                listagem.
                            </div>
                        </x-organisms.confirm-rm-group-btn>
                    @endif
                </div>
                @if (!$trashed)
                    <x-organisms.filter-form-check
                        key="exits"
                        :checked="request()->boolean('exits')"
                        class="py-2 w-100"
                    >
                        Reservados para saída</x-organisms.filter-form-check
                    >
                @endif
            </div>
            <x-molecules.table-index
                :styleRows="['first' => 'width: 1.75em', 'second' => 'max-width: 10em']"
                :qtyBtns="1.25"
            >
                <x-slot:cols>
                    <col
                        class="col-remain-qty"
                        style="visibility: visible; width: auto"
                    />
                    <col class="col-remain-category" />
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
                            sort="qtyRemain"
                        >
                            Qtd</x-atoms.table-head
                        >
                        <x-atoms.table-head
                            default
                            colRemain
                            sort="catName"
                        >
                            Categoria</x-atoms.table-head
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
                    @forelse ($models($list) as $prod)
                        <tr>
                            <td class="position-relative overflow-visible">
                                <input
                                    type="checkbox"
                                    value="{{ $prod->id }}"
                                    class="form-check-input cursor-pointer multiselection-item"
                                    @disabled ($trashed ? !$hasAccess('restore', $prod) : !$hasAccess('delete', $prod))
                                />
                            </td>
                            <td>
                                @php
                                    $routeName = $trashed ? 'stocks.show.removed' : 'stocks.show';
                                    $routeKey = $trashed ? 'productDeleted' : 'product';
                                @endphp
                                <x-atoms.button
                                    class="text-truncate text-decoration-none text-info border-0 ps-0"
                                    format="anchor"
                                    href="{{ route($routeName, [$routeKey => $prod->id]) }}"
                                    title="Mostrar estoque"
                                    :disabled="!$hasAccess('show', $prod)"
                                >
                                    {{ $prod->name }}
                                </x-atoms.button>
                            </td>
                            <td>{{ $prod->qtyRemain }}</td>
                            <td>
                                <a
                                    class="text-truncate text-decoration-none text-info"
                                    href="{{ route('product-categories.show', ['category' => $prod->catId]) }}"
                                    title="Visualizar categoria"
                                >
                                    {{ $prod->catName }}
                                </a>
                            </td>
                            <td class="dropdown dropstart">
                                <x-atoms.button
                                    class="btn-secondary dropdown-toggle"
                                    data-bs-toggle="dropdown"
                                    aria-expanded="false"
                                >
                                    <i class="bi bi-menu-button-wide"></i>
                                </x-atoms.button>
                                <ul
                                    class="dropdown-menu dropdown-menu-start action-btns"
                                >
                                    @if ($trashed)
                                        <li>
                                            <x-organisms.confirm-restore-btn
                                                :routeParams="['productDeleted' => $prod->id]"
                                                route="products.restore"
                                                heading="Restaurar este produto?"
                                                positiveText="Restaurar produto"
                                                title="Restaurar Produto"
                                                :disabled="!$hasAccess('restore', $prod)"
                                            >
                                                Isso restaurará este produto e o
                                                estoque relacionado.
                                            </x-organisms.confirm-restore-btn>
                                        </li>
                                    @else
                                        <li>
                                            <div class="position-relative">
                                                @if ($prod->inSalesCart)
                                                    <span
                                                        class="position-absolute end-0 top-0 badge rounded-pill bg-danger p-0 z-1"
                                                        ><i
                                                            class="bi bi-plus"
                                                        ></i
                                                    ></span>
                                                @endif
                                                <form
                                                    action="{{
                                                        route(
                                                            $prod->inSalesCart ? 'stocks.sales.unmark' : 'stocks.sales.mark',
                                                            ['product' => $prod->id]
                                                        )
                                                    }}"
                                                    method="post"
                                                >
                                                    @csrf
                                                    <x-atoms.button
                                                        class="btn-secondary position-relative"
                                                        type="submit"
                                                        title="Adicionar venda"
                                                        :disabled="\intval($prod->qtyRemain) === 0"
                                                    >
                                                        <i
                                                            class="bi bi-cart-plus"
                                                        ></i>
                                                    </x-atoms.button>
                                                </form>
                                            </div>
                                        </li>
                                        <li>
                                            <x-atoms.button
                                                class="btn-secondary position-relative"
                                                format="anchor"
                                                href="{{ route('stocks.entries.create', ['product' => $prod->id]) }}"
                                                title="Adicionar estoque"
                                            >
                                                <span class="absolute-center"
                                                    >+</span
                                                >
                                                <i class="bi bi-box2"></i>
                                            </x-atoms.button>
                                        </li>
                                        <li>
                                            <x-atoms.button
                                                format="anchor"
                                                class="btn-secondary"
                                                href="{{ route('products.edit', ['product' => $prod->id]) }}"
                                                title="Editar"
                                                :disabled="!$hasAccess('edit', $prod)"
                                            >
                                                <i class="bi bi-wrench"></i>
                                            </x-atoms.button>
                                        </li>
                                        <li>
                                            <x-organisms.confirm-rm-btn
                                                :routeParams="['product' => $prod->id]"
                                                route="products.destroy"
                                                heading="Remover este produto?"
                                                positive-text="Remover produto"
                                                title="Remover produto"
                                                :disabled="!$hasAccess('delete', $prod)"
                                            >
                                                <div class="mb-1">
                                                    Se ele não possuir histórico
                                                    de estoque, será removido
                                                    permanentemente.
                                                </div>
                                                <div>
                                                    Caso contrário, será
                                                    removido apenas desta
                                                    listagem.
                                                </div>
                                            </x-organisms.confirm-rm-btn>
                                        </li>
                                    @endif
                                </ul>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td
                                colspan="5"
                                class="no-values"
                            >
                                Sem produtos para o filtro atual
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
