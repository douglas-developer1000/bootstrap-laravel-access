@push ('styling')
    @vite ([
        'resources/css/pages/generic/index.css',
        'resources/css/pages/generic/table.css'
    ])
@endpush

@php
    $qs = request()->query->all();
@endphp

<x-layout title="Pedidos de Registro">
    <x-packs.header>
        <x-packs.page-heading-row
            heading="Pedidos de Registro"
            class="page-heading-row-custom"
        />
    </x-packs.header>
    <main class="bg-secondary-subtle list-main">
        <section class="content bg-light">
            <x-packs.term-search
                label-text="Nome:"
                placeholder="Insira um email"
            />
            <table
                class="table table-hover table-striped list-table tabular-data"
            >
                <thead>
                    <tr>
                        <x-app-table-head sort="id">ID</x-app-table-head>
                        <x-app-table-head sort="name">E-mail</x-app-table-head>
                        <th scope="col">Telefone</th>
                        <x-app-table-head
                            default
                            sort="created_at"
                            >Criação</x-app-table-head
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
                            <td>{{$order->id}}</td>
                            <td>
                                <div class="ellipsis">{{ $order->email }}</div>
                            </td>
                            <td>{{ $order->phone ?? '---' }}</td>
                            <td>{{ $order->created_at_formatted }}</td>
                            <td>
                                <div
                                    class="w-100 d-flex justify-content-between gap-1"
                                >
                                    <x-atoms.button
                                        class="btn-success"
                                        data-bs-toggle="modal"
                                        data-bs-target="#confirmModalApproval{{ $order->id }}"
                                    >
                                        <i class="bi bi-hand-thumbs-up"></i>
                                    </x-atoms.button>
                                    <x-molecules.confirm-modal
                                        id="Approval{{ $order->id }}"
                                        href="{!! 
                                            route(
                                                'register.orders.approve',
                                                collect([
                                                    'order' => $order->id,
                                                ])->merge($qs)->all()
                                            )
                                        !!}"
                                        heading="Aprovar este pedido de registro?"
                                        :method="method_field('DELETE')"
                                        negative-text="Manter"
                                        positive-text="Aprovar pedido"
                                    >
                                        Isso aprovará este pedido.
                                    </x-molecules.confirm-modal>
                                    <x-atoms.button
                                        class="btn-danger"
                                        data-bs-toggle="modal"
                                        data-bs-target="#confirmModalRemotion{{ $order->id }}"
                                    >
                                        <i class="bi bi-trash"></i>
                                    </x-atoms.button>
                                    <x-molecules.confirm-modal
                                        id="Remotion{{ $order->id }}"
                                        href="{!! 
                                            route(
                                                'register.orders.destroy',
                                                collect([
                                                    'order' => $order->id,
                                                ])->merge($qs)->all()
                                            )
                                        !!}"
                                        heading="Remover este pedido de registro?"
                                        :method="method_field('DELETE')"
                                        negative-text="Manter"
                                        positive-text="Remover pedido"
                                    >
                                        Isso removerá permanentemente este
                                        pedido.
                                    </x-molecules.confirm-modal>
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
            </table>
            <x-app-pagination :paginator="$list" />
        </section>
        <x-packs.success-toast />
    </main>
</x-layout>
