@push ('styling')
    @vite ([
        'resources/css/pages/generic/index.css',
        'resources/css/pages/generic/table.css'
    ])
@endpush

@php
    $qs = request()->query->all();
@endphp

<x-layout title="Aprovações de Registro">
    <x-packs.header>
        <x-packs.page-heading-row
            heading="Aprovações de Registro"
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
                    @forelse ($list as $approval)
                        <tr>
                            <td>{{$approval->id}}</td>
                            <td>
                                <div class="ellipsis">
                                    {{ $approval->email }}
                                </div>
                            </td>
                            <td>{{ $approval->phone ?? '---' }}</td>
                            <td>{{ $approval->created_at_formatted }}</td>
                            <td>
                                <div
                                    class="w-100 d-flex justify-content-between gap-1"
                                >
                                    <x-atoms.button
                                        class="btn-danger"
                                        data-bs-toggle="modal"
                                        data-bs-target="#confirmModal{{ $approval->id }}"
                                    >
                                        <i class="bi bi-trash"></i>
                                    </x-atoms.button>
                                    <x-molecules.confirm-modal
                                        id="{{ $approval->id }}"
                                        href="{!! 
                                            route(
                                                'register.approvals.destroy',
                                                collect([
                                                    'approval' => $approval->id,
                                                ])->merge($qs)->all()
                                            )
                                        !!}"
                                        heading="Remover esta aprovação de registro?"
                                        :method="method_field('DELETE')"
                                        negative-text="Manter"
                                        positive-text="Remover aprovação"
                                    >
                                        Isso removerá permanentemente esta
                                        aprovação.
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
                                Sem aprovações para o filtro atual
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
