@use ('App\Models\Customer')
@use ('App\Facades\DateFormatter')
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
        'resources/js/pages/generic/multiselection.ts',
        'resources/js/pages/generic/realocate-markups.ts'
    ])
@endpush

@php
    $trashed = request()->boolean('trashed');
    $subject = $trashed ? 'Clientes removidos' : 'Clientes';
@endphp

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
                            title="Clientes {{ $trashed ? 'ativos' : 'removidos' }}"
                            class="dropdown-item"
                            format="anchor"
                            href="{{
                                route(
                                    'customers.index',
                                    $trashed ? [] : ['trashed' => 1]
                                )
                            }}"
                        >
                            @if ($trashed)
                                <i class="bi bi-cash-coin"></i>
                                Clientes Ativos
                            @else
                                <i class="bi bi-trash"></i>
                                Clientes Removidos
                            @endif
                        </x-atoms.button>
                    </li>
                    @if (!$trashed)
                        <li>
                            <x-atoms.button
                                class="dropdown-item"
                                format="anchor"
                                href="{{ route('customers.create') }}"
                                :disabled="!$hasAccess('create', Customer::class)"
                            >
                                <i class="bi bi-plus h-1"></i>
                                <span>Cliente</span>
                            </x-atoms.button>
                        </li>
                    @endif
                </ul>
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
                    placeholder="Insira o nome do cliente"
                />
                <div
                    class="d-flex justify-content-end flex-grow-1 column-gap-2"
                >
                    @if ($trashed)
                        <x-organisms.confirm-restore-group-btn
                            :routeParams="['key' => 'restoration', 'customerList' => 'trashed']"
                            route="customers.group.restore"
                            heading="Restaurar estes clientes?"
                            positive-text="Restaurar clientes"
                            title="Restaurar clientes selecionados"
                        >
                            Isso restaurará os clientes selecionados e todos
                            seus dados relacionados.
                        </x-organisms.confirm-restore-group-btn>
                    @else
                        <x-organisms.confirm-rm-group-btn
                            :routeParams="['key' => 'remotion', 'customerList' => 'list']"
                            route="customers.group.destroy"
                            heading="Remover estes clientes?"
                            positive-text="Remover clientes"
                            title="Remover clientes selecionados"
                        >
                            <div class="mb-3">
                                Para cada cliente selecionado:
                            </div>
                            <div class="mb-1">
                                Se ele não possuir histórico de pagamentos, será
                                removido permanentemente.
                            </div>
                            <div>
                                Caso contrário, será removido apenas desta
                                listagem.
                            </div>
                        </x-organisms.confirm-rm-group-btn>
                    @endif
                </div>
            </div>
            <x-molecules.table-index :qtyBtns="1.25">
                <x-slot:cols>
                    <col class="col-remain-email" />
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
                            sort="email"
                        >
                            E-mail</x-atoms.table-head
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
                    @forelse ($models($list) as $customer)
                        <tr>
                            <td>
                                <input
                                    type="checkbox"
                                    value="{{ $customer->id }}"
                                    class="form-check-input cursor-pointer multiselection-item"
                                    @disabled ($trashed ? !$hasAccess('restore', $customer) : !$hasAccess('delete', $customer))
                                />
                            </td>
                            <td>
                                <a
                                    href="{{ route('customers.show', ['customer' => $customer->id]) }}"
                                    class="ellipsis text-decoration-none text-info"
                                    title="Visualizar dados do cliente"
                                    >{{$customer->name}}</a
                                >
                            </td>
                            <td>
                                <div class="ellipsis">
                                    {{$customer->email ?? '--'}}
                                </div>
                            </td>
                            <td>
                                {{ DateFormatter::formatToDate($customer->created_at) }}
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
                                                :routeParams="['customer' => $customer->id]"
                                                route="customers.restore"
                                                heading="Restaurar este cliente?"
                                                positiveText="Restaurar cliente"
                                                title="Restaurar cliente"
                                                :disabled="!$hasAccess('restore', $customer)"
                                            >
                                                Isso restaurará este cliente e
                                                todos seus dados relacionados.
                                            </x-organisms.confirm-restore-btn>
                                        </li>
                                    @else
                                        @can('createSaleExit', [StockExit::class, StockExitTypeEnum::SALE, $customer])
                                            <li>
                                                <x-atoms.button
                                                    format="anchor"
                                                    class="btn btn-success"
                                                    href="{{
                                                        route('stocks.exits.sale.create', [
                                                            'exitType' => StockExitTypeEnum::SALE->value,
                                                            'customer' => $customer->id
                                                        ])
                                                    }}"
                                                    title="Vender produtos"
                                                    :disabled="!$hasAccess('createSaleExit', [StockExit::class, StockExitTypeEnum::SALE, $customer])"
                                                >
                                                    <i class="bi bi-cash-coin"></i>
                                                </x-atoms.button>
                                            </li>
                                        @endcan
                                        <li>
                                            <x-organisms.confirm-rm-btn
                                                :routeParams="['customer' => $customer->id]"
                                                route="customers.destroy"
                                                heading="Remover este cliente?"
                                                positiveText="Remover cliente"
                                                title="Remover cliente"
                                            >
                                                <div class="mb-1">
                                                    Se ele não possuir histórico
                                                    de pagamentos, será removido
                                                    permanentemente.
                                                </div>
                                                <div>
                                                    Caso contrário, será
                                                    removido apenas desta
                                                    listagem.
                                                </div>
                                            </x-organisms.confirm-rm-btn>
                                        </li>
                                        <li>
                                            <x-atoms.button
                                                format="anchor"
                                                class="btn-secondary"
                                                href="{{ route('customers.edit', ['customer' => $customer->id]) }}"
                                                :disabled="!$hasAccess('edit', $customer)"
                                            >
                                                <i class="bi bi-wrench"></i>
                                            </x-atoms.button>
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
                                Sem clientes para o filtro atual
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
