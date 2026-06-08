@use ('App\Models\Product')
@use ('App\Models\ProductCategory')
@use ('App\Libraries\Utils\DatetimeFormatter')
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

@php
    $trashed = request()->boolean('trashed');
    $subject = $trashed ? 'Categorias de produto removidas' : 'Categorias de produto';
@endphp

<x-layout title="{{ $subject }}">
    <x-packs.header>
        <x-packs.page-heading-row
            :heading="$subject"
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
                            title="Categorias {{ $trashed ? 'ativas' : 'removidas' }}"
                            class="dropdown-item"
                            format="anchor"
                            href="{{
                                route(
                                    'product-categories.index',
                                    $trashed ? [] : ['trashed' => 1]
                                )
                            }}"
                        >
                            @if ($trashed)
                                <i class="bi bi-check-lg"></i>
                                Categorias Ativas
                            @else
                                <i class="bi bi-trash"></i>
                                Categorias Removidas
                            @endif
                        </x-atoms.button>
                    </li>
                    @if (!$trashed)
                        <li>
                            <x-atoms.button
                                class="dropdown-item"
                                format="anchor"
                                href="{{ route('product-categories.create') }}"
                                :disabled="!$hasAccess('create', ProductCategory::class)"
                            >
                                <i class="bi bi-plus-lg"></i>
                                <span>Categoria</span>
                            </x-atoms.button>
                        </li>
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
                    placeholder="Insira o nome da categoria"
                />
                <div
                    class="d-flex justify-content-end flex-grow-1 column-gap-2"
                >
                    @if ($trashed)
                        <x-organisms.confirm-restore-group-btn
                            :routeParams="['key' => 'restoration', 'productCategoryList' => 'trashed']"
                            route="product-categories.group.restore"
                            heading="Restaurar estas categorias?"
                            positive-text="Restaurar categorias"
                            title="Restaurar categorias selecionadas"
                        >
                            Isso restaurará as categorias de produto
                            selecionadas e todos seus dados relacionados.
                        </x-organisms.confirm-restore-group-btn>
                    @else
                        <x-organisms.confirm-rm-group-btn
                            :routeParams="['key' => 'remotion', 'productCategoryList' => 'list']"
                            route="product-categories.group.destroy"
                            heading="Remover estas categorias?"
                            positive-text="Remover categorias"
                            title="Remover várias categorias de produto"
                        >
                            <div class="mb-3">
                                Para cada categoria de produto selecionada:
                            </div>
                            <div class="mb-1">
                                Se ela não possuir utilização, será removida
                                permanentemente.
                            </div>
                            <div>
                                Caso contrário, será removida apenas desta
                                listagem.
                            </div>
                        </x-organisms.confirm-rm-group-btn>
                    @endif
                </div>
            </div>
            <x-molecules.table-index :qtyBtns="$trashed ? 1 : 2">
                <x-slot:cols>
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
                            default
                            colRemain
                            sort="created_at"
                        >
                            Criada em</x-atoms.table-head
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
                    @forelse ($models($list) as $cat)
                        <tr>
                            <td>
                                <input
                                    type="checkbox"
                                    value="{{ $cat->id }}"
                                    class="form-check-input cursor-pointer multiselection-item"
                                    @disabled ($trashed ? !$hasAccess('restore', $cat) : !$hasAccess('delete', $cat))
                                />
                            </td>
                            <td>
                                <a
                                    href="{{ route('product-categories.show', ['category' => $cat->id]) }}"
                                    class="text-truncate text-decoration-none text-info"
                                    title="Visualizar categoria de produto"
                                    >{{$cat->name}}</a
                                >
                            </td>
                            <td>
                                {{ DatetimeFormatter::formatToDate($cat->created_at) }}
                            </td>
                            <td>
                                <div
                                    class="w-100 d-flex justify-content-between gap-1"
                                >
                                    @if ($trashed)
                                        <x-organisms.confirm-restore-btn
                                            :routeParams="['prodCategoryDeleted' => $cat->id]"
                                            route="product-categories.restore"
                                            heading="Restaurar esta categoria?"
                                            positiveText="Restaurar categoria"
                                            title="Restaurar categoria de produto"
                                            :disabled="!$hasAccess('restore', $cat)"
                                        >
                                            Isso restaurará esta categoria de
                                            produto e todos seus dados
                                            relacionados.
                                        </x-organisms.confirm-restore-btn>
                                    @else
                                        <x-atoms.button
                                            format="anchor"
                                            class="btn-secondary"
                                            href="{{ route('product-categories.edit', ['category' => $cat->id]) }}"
                                            :disabled="!$hasAccess('edit', $cat)"
                                        >
                                            <i class="bi bi-wrench"></i>
                                        </x-atoms.button>
                                        <x-organisms.confirm-rm-btn
                                            :routeParams="['category' => $cat->id]"
                                            route="product-categories.destroy"
                                            heading="Remover esta categoria?"
                                            positiveText="Remover categoria"
                                            title="Remover categoria de produto"
                                            :disabled="!$hasAccess('delete', $cat)"
                                        >
                                            <div class="mb-1">
                                                Se esta categoria de produto não
                                                possuir utilização, será
                                                removida permanentemente.
                                            </div>
                                            <div>
                                                Caso contrário, será removida
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
                                colspan="4"
                                class="no-values"
                            >
                                Sem categorias de produtos para o filtro atual
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
