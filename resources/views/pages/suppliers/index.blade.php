@use ('App\Models\Supplier')
@use ('App\Libraries\Utils\DatetimeFormatter')
@push ('styling')
    @vite ([
        'resources/css/pages/generic/index.css',
        'resources/css/pages/generic/table.css',
        'resources/css/pages/suppliers/index.css',
    ])
@endpush
@push ('ecmascript-bottom')
    @vite ([
        'resources/js/pages/generic/multiselection.ts'
    ])
@endpush

@php
    $trashed = request()->boolean('trashed');
    $subject = $trashed ? 'Fornecedores removidos' : 'Fornecedores';
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
                            title="Fornecedores {{ $trashed ? 'ativos' : 'removidos' }}"
                            class="dropdown-item"
                            format="anchor"
                            href="{{
                                route(
                                    'suppliers.index',
                                    $trashed ? [] : ['trashed' => 1]
                                )
                            }}"
                        >
                            @if ($trashed)
                                <i class="bi bi-check-lg"></i>
                                Fornecedores Ativos
                            @else
                                <i class="bi bi-trash"></i>
                                Fornecedores Removidos
                            @endif
                        </x-atoms.button>
                    </li>
                    @can ('create', Supplier::class)
                        <li>
                            <x-atoms.button
                                class="dropdown-item"
                                format="anchor"
                                href="{{ route('suppliers.create') }}"
                            >
                                <i class="bi bi-plus-lg"></i>
                                <span>Fornecedor</span>
                            </x-atoms.button>
                        </li>
                    @endcan
                </ul>
            </div>
        </x-packs.page-heading-row>
    </x-packs.header>
    <main class="bg-secondary-subtle list-main">
        <section class="content bg-light">
            <x-molecules.block-error :keys="['remotion', 'remotion.*']" />
            <div class="d-flex flex-wrap justify-content-between row-gap-2">
                <div class="d-flex flex-column">
                    <x-packs.term-search
                        label-text="Nome:"
                        placeholder="Insira o nome do fornecedor"
                        keyTerm="name"
                    />
                    <x-organisms.filter-form-check
                        key="own"
                        :checked="request()->boolean('own')"
                        class="py-2"
                    >
                        Meus fornecedores</x-organisms.filter-form-check
                    >
                </div>
                <div
                    class="d-flex justify-content-end flex-grow-1 column-gap-2"
                >
                    @if ($trashed)
                        <x-organisms.confirm-restore-group-btn
                            :routeParams="['key' => 'restoration', 'supplierList' => 'trashed']"
                            route="suppliers.group.restore"
                            heading="Restaurar estes fornecedores?"
                            positive-text="Restaurar fornecedores"
                            title="Restaurar fornecedores selecionados"
                        >
                            Isso restaurará os fornecedores selecionados e todos
                            seus dados relacionados.
                        </x-organisms.confirm-restore-group-btn>
                    @else
                        <x-organisms.confirm-rm-group-btn
                            :routeParams="['key' => 'remotion', 'supplierList' => 'list']"
                            route="suppliers.group.destroy"
                            heading="Remover estes fornecedores?"
                            positive-text="Remover fornecedores"
                            title="Remover fornecedores selecionados"
                        >
                            <div class="mb-3">
                                Para cada fornecedor selecionado:
                            </div>
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
            <x-molecules.table-index :qtyBtns="$trashed ? 1 : 2">
                <x-slot:cols>
                    <col class="col-remain-name" />
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
                        <th scope="col">Ícone</th>
                        <x-atoms.table-head sort="name">
                            Nome</x-atoms.table-head
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
                    @forelse ($models($list) as $supplier)
                        <tr>
                            <td>
                                <input
                                    type="checkbox"
                                    value="{{ $supplier->id }}"
                                    class="form-check-input cursor-pointer multiselection-item"
                                    @disabled ($trashed ? !$hasAccess('restore', $supplier) : !$hasAccess('delete', $supplier))
                                />
                            </td>
                            <td>
                                <div class="rounded-circle supplier-box">
                                    @if ($supplier->img)
                                        <img
                                            src="{{ $supplier->img }}"
                                            alt="Foto do fornecedor"
                                            class="img-from-row"
                                        />
                                    @else
                                        <div
                                            class="img-from-row"
                                            style="background-color: {{ $supplier->color }};"
                                        ></div>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <a
                                    href="{{ route('suppliers.show', ['supplier' => $supplier->id]) }}"
                                    class="text-truncate text-decoration-none text-info"
                                    title="Visualizar dados do fornecedor"
                                    >{{$supplier->name}}</a
                                >
                            </td>
                            <td>
                                {{ DatetimeFormatter::formatToDate($supplier->created_at) }}
                            </td>
                            <td>
                                <div
                                    class="w-100 d-flex justify-content-center gap-1"
                                >
                                    @if ($trashed)
                                        <x-organisms.confirm-restore-btn
                                            :routeParams="['supplierDeleted' => $supplier->id]"
                                            route="suppliers.restore"
                                            heading="Restaurar este fornecedor?"
                                            positiveText="Restaurar fornecedor"
                                            title="Restaurar fornecedor"
                                            :disabled="!$hasAccess('restore', $supplier)"
                                        >
                                            Isso restaurará este fornecedor e
                                            todos seus dados relacionados.
                                        </x-organisms.confirm-restore-btn>
                                    @else
                                        <x-atoms.button
                                            format="anchor"
                                            class="btn-secondary"
                                            href="{{ route('suppliers.edit', ['supplier' => $supplier->id]) }}"
                                            :disabled="!$hasAccess('edit', $supplier)"
                                        >
                                            <i class="bi bi-wrench"></i>
                                        </x-atoms.button>
                                        <x-organisms.confirm-rm-btn
                                            :routeParams="['supplier' => $supplier->id]"
                                            route="suppliers.destroy"
                                            heading="Remover este fornecedor?"
                                            positiveText="Remover fornecedor"
                                            title="Remover fornecedor"
                                            :disabled="!$hasAccess('delete', $supplier)"
                                        >
                                            <div class="mb-1">
                                                Se este fornecedor não possuir
                                                utilização, será removido
                                                permanentemente.
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
                                Sem fornecedores para o filtro atual
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
