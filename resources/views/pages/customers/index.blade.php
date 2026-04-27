@push ('styling')
    @vite ([
        'resources/css/pages/generic/index.css',
        'resources/css/pages/generic/table.css'
    ])
@endpush

@php
    $qs = request()->query->all();
@endphp

@use ('App\Libraries\Enums\PermissionNameEnum')

<x-layout title="Lista de Clientes">
    <x-packs.header>
        <x-packs.page-heading-row
            heading="Lista de Clientes"
            class="page-heading-row-custom"
        >
            @can (PermissionNameEnum::CUSTOMER_CREATE->value)
                <x-atoms.button
                    class="btn-secondary"
                    format="anchor"
                    href="{{ route('customers.create') }}"
                >
                    <i class="bi bi-plus h-1"></i>
                </x-atoms.button>
            @endcan
        </x-packs.page-heading-row>
    </x-packs.header>
    <main class="bg-secondary-subtle list-main">
        <section class="content bg-light">
            <x-packs.term-search
                label-text="Nome:"
                placeholder="Insira o nome do cliente"
            />
            <x-molecules.table-index>
                <x-slot:cols>
                    <col class="col-remain-email" />
                    <col class="col-remain-created_at" />
                </x-slot:cols>
                <thead>
                    <tr>
                        <x-app-table-head sort="id">ID</x-app-table-head>
                        <x-app-table-head sort="name">Nome</x-app-table-head>
                        <x-app-table-head
                            colRemain
                            sort="email"
                            >E-mail</x-app-table-head
                        >
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
                    @forelse ($list as $customer)
                        <tr>
                            <td>{{$customer->id}}</td>
                            <td>
                                <a
                                    href="{{ route('customers.show', ['customer' => $customer->id]) }}"
                                    class="ellipsis text-decoration-none text-info"
                                    title="Visualizar dados do cliente"
                                    >{{$customer->name}}</a
                                >
                            </td>
                            <td>
                                <div class="ellipsis">{{$customer->email}}</div>
                            </td>
                            <td>{{ $customer->created_at_formatted }}</td>
                            <td>
                                <div
                                    class="w-100 d-flex justify-content-between gap-1"
                                >
                                    <x-atoms.button
                                        format="anchor"
                                        class="btn-secondary"
                                        href="{{ route('customers.edit', ['customer' => $customer->id]) }}"
                                    >
                                        <i class="bi bi-wrench"></i>
                                    </x-atoms.button>
                                    <x-atoms.button
                                        class="btn-danger"
                                        data-bs-toggle="modal"
                                        data-bs-target="#confirmModal{{ $customer->id }}"
                                    >
                                        <i class="bi bi-trash"></i>
                                    </x-atoms.button>
                                    <x-molecules.confirm-modal
                                        id="{{ $customer->id }}"
                                        href="{!! 
                                            route(
                                                'customers.destroy',
                                                collect([
                                                    'customer' => $customer->id,
                                                ])->merge($qs)->all()
                                            )
                                        !!}"
                                        heading="Remover este cliente?"
                                        :method="method_field('DELETE')"
                                        negative-text="Manter"
                                        positive-text="Remover cliente"
                                    >
                                        Isso removerá permanentemente este
                                        cliente.
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
                                Sem clientes para o filtro atual
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
