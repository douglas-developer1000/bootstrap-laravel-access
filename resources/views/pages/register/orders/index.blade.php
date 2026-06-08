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

<x-layout title="Pedidos de Registro">
    <x-packs.header>
        <x-packs.page-heading-row
            heading="Pedidos de Registro"
            class="page-heading-row-custom"
        />
    </x-packs.header>
    <main class="bg-secondary-subtle list-main">
        <section class="content bg-light">
            <x-molecules.block-error
                :keys="[
                    'remotion', 'remotion.*', 'approvement', 'approvement.*'
                ]"
            />
            <div class="d-flex flex-wrap justify-content-between row-gap-2">
                <x-packs.term-search
                    label-text="Nome:"
                    placeholder="Insira um email"
                />
                <div
                    class="d-flex justify-content-end flex-grow-1 column-gap-2"
                >
                    <x-organisms.confirm-approve-group-btn
                        route="register.orders.group.approve"
                        heading="Aprovar estes pedidos?"
                        positive-text="Aprovar pedidos"
                        title="Aprovar pedidos selecionados"
                    >
                        Isso aprovará os pedidos selecionados.
                    </x-organisms.confirm-approve-group-btn>
                    <x-organisms.confirm-rm-group-btn
                        route="register.orders.group.destroy"
                        heading="Remover estes pedidos?"
                        positive-text="Remover pedidos"
                        title="Remover vários pedidos"
                    >
                        Isso removerá os pedidos selecionados permanentemente.
                    </x-organisms.confirm-rm-group-btn>
                </div>
            </div>
            <x-molecules.table-index>
                <x-slot:cols>
                    <col class="col-remain-phone" />
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
                            E-mail</x-atoms.table-head
                        >
                        <th
                            scope="col"
                            class="col-remain"
                        >
                            Telefone
                        </th>
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
                    @forelse ($list as $order)
                        <tr>
                            <td>
                                <input
                                    type="checkbox"
                                    value="{{ $order->id }}"
                                    class="form-check-input cursor-pointer multiselection-item"
                                />
                            </td>
                            <td>
                                <div class="ellipsis">{{ $order->email }}</div>
                            </td>
                            <td>{{ $order->phone ?? '--' }}</td>
                            <td>
                                {{ DatetimeFormatter::formatToDate($order->created_at) }}
                            </td>
                            <td>
                                <div
                                    class="w-100 d-flex justify-content-between gap-1"
                                >
                                    <x-organisms.confirm-approve-btn
                                        :routeParams="['order' => $order->id]"
                                        route="register.orders.approve"
                                        heading="Aprovar este pedido?"
                                        positive-text="Aprovar pedido"
                                        title="Aprovar pedido desta linha"
                                    >
                                        Isso aprovará este pedido de registro.
                                    </x-organisms.confirm-approve-btn>
                                    <x-organisms.confirm-rm-btn
                                        :routeParams="['order' => $order->id]"
                                        route="register.orders.destroy"
                                        heading="Remover este pedido de registro?"
                                        positiveText="Remover pedido"
                                        title="Remover pedido"
                                    >
                                        Isso removerá permanentemente este
                                        pedido.
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
                                Sem pedidos para o filtro atual
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
