@push ('styling')
    @vite ([
        'resources/css/pages/generic/index.css',
        'resources/css/pages/generic/table.css'
    ])
@endpush

@php
    $qs = request()->query->all();
@endphp
@use ('App\Libraries\Utils\DatetimeFormatter')

<x-layout title="Lista de Papeis">
    <x-packs.header>
        <x-packs.page-heading-row
            heading="Lista de Papeis"
            class="page-heading-row-custom"
        >
            <x-atoms.button
                class="btn-secondary"
                format="anchor"
                href="{{ route('roles.create') }}"
            >
                <i class="bi bi-plus h-1"></i>
            </x-atoms.button>
        </x-packs.page-heading-row>
    </x-packs.header>
    <main class="bg-secondary-subtle list-main">
        <section class="content bg-light">
            <x-packs.term-search
                label-text="Nome:"
                placeholder="Insira o nome do papel"
            />
            <x-molecules.table-index>
                <x-slot:cols>
                    <col class="col-remain-created_at" />
                </x-slot:cols>
                <thead>
                    <tr>
                        <x-app-table-head sort="id">ID</x-app-table-head>
                        <x-app-table-head sort="name">Nome</x-app-table-head>
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
                    @forelse ($list as $role)
                        <tr>
                            <td>{{$role->id}}</td>
                            <td>
                                <a
                                    class="ellipsis text-decoration-none text-info"
                                    href="{{ route('roles.show', ['role' => $role->id]) }}"
                                    title="Visualizar papel"
                                    >{{ $role->name }}</a
                                >
                            </td>
                            <td>
                                {{ DatetimeFormatter::formatToDate($role->created_at) }}
                            </td>
                            <td>
                                <div
                                    class="w-100 d-flex justify-content-between gap-1"
                                >
                                    <x-atoms.button
                                        format="anchor"
                                        class="btn-secondary"
                                        href="{{ route('roles.edit', ['role' => $role->id]) }}"
                                        title="Editar"
                                    >
                                        <i class="bi bi-wrench"></i>
                                    </x-atoms.button>
                                    <x-atoms.button
                                        class="btn-danger"
                                        data-bs-toggle="modal"
                                        data-bs-target="#confirmModal{{ $role->id }}"
                                        title="Remover"
                                    >
                                        <i class="bi bi-trash"></i>
                                    </x-atoms.button>
                                    <x-molecules.confirm-modal
                                        id="{{ $role->id }}"
                                        href="{!!
                                            route(
                                                'roles.destroy',
                                                collect([
                                                    'role' => $role->id,
                                                ])->merge($qs)->all()
                                            )
                                        !!}"
                                        heading="Remover este papel?"
                                        :method="method_field('DELETE')"
                                        negative-text="Manter"
                                        positive-text="Remover papel"
                                    >
                                        Isso removerá permanentemente este
                                        papel.
                                    </x-molecules.confirm-modal>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td
                                colspan="4"
                                class="no-values"
                            >
                                Sem papeis para o filtro atual
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
