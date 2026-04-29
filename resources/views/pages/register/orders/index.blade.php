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
@use ('App\Libraries\Utils\PhoneFormatter')

@php
    $qs = request()->query->all();
    $formApprovementGroupId = uniqid('formApprove_');
    $formRemotionGroupId = uniqid('formRemove_');
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
            @if (
                $errors->has('remotion') ||
                $errors->has('remotion.*') ||
                $errors->has('approvement') ||
                $errors->has('approvement.*')
            )
                <div
                    class="p-3 text-danger-emphasis bg-danger-subtle border border-danger-subtle rounded-3"
                >
                    {{ $message }}
                </div>
            @endif
            <div class="d-flex flex-wrap justify-content-between row-gap-2">
                <x-packs.term-search
                    label-text="Nome:"
                    placeholder="Insira um email"
                />
                <div
                    class="d-flex justify-content-end flex-grow-1 column-gap-2"
                >
                    <x-atoms.button
                        class="btn-secondary align-self-end justify-content-end multiselection-submit cursor-pointer"
                        data-bs-toggle="modal"
                        data-bs-target="#confirmModalGroupApprove"
                        title="Aprovar vários pedidos"
                        disabled
                        data-form="{{ $formApprovementGroupId }}"
                        data-name="approvement[]"
                    >
                        Aprovar selecionados
                    </x-atoms.button>
                    <x-molecules.confirm-modal
                        id="GroupApprove"
                        href=""
                        href="{!!
                            route('register.orders.group.approve', $qs)
                        !!}"
                        :formId="$formApprovementGroupId"
                        heading="Aprovar estes pedidos?"
                        :method="method_field('DELETE')"
                        negative-text="Manter"
                        positive-text="Aprovar pedidos"
                    >
                        Isso aprovará os pedidos selecionados.
                    </x-molecules.confirm-modal>
                    <x-atoms.button
                        class="btn-secondary align-self-end justify-content-end multiselection-submit cursor-pointer"
                        data-bs-toggle="modal"
                        data-bs-target="#confirmModalGroupRemove"
                        title="Remover vários pedidos"
                        data-form="{{ $formRemotionGroupId }}"
                        data-name="remotion[]"
                        disabled
                    >
                        Remover selecionados
                    </x-atoms.button>
                    <x-molecules.confirm-modal
                        id="GroupRemove"
                        href="{!!
                            route('register.orders.group.destroy', $qs)
                        !!}"
                        :formId="$formRemotionGroupId"
                        heading="Remover estes pedidos?"
                        :method="method_field('DELETE')"
                        negative-text="Manter"
                        positive-text="Remover pedidos"
                    >
                        Isso removerá os pedidos selecionados permanentemente.
                    </x-molecules.confirm-modal>
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
                        <x-app-table-head sort="name">E-mail</x-app-table-head>
                        <th
                            scope="col"
                            class="col-remain"
                        >
                            Telefone
                        </th>
                        <x-app-table-head
                            default
                            colRemain
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
                            <td>{{ PhoneFormatter::toView($order->phone) }}</td>
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
            </x-molecules.table-index>
            <x-app-pagination :paginator="$list" />
        </section>
        <x-packs.success-toast />
    </main>
</x-layout>
