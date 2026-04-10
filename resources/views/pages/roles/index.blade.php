@push ('styling')
    @vite ([
        'resources/css/pages/generic/index.css',
        'resources/css/pages/generic/table.css'
    ])
@endpush

<x-layout title="Lista de Papeis">
    <x-packs.header>
        <x-packs.page-heading-row
            heading="Lista de Papeis"
            class="page-heading-row-custom"
        >
            <x-atoms.button
                class="custom-top-btn btn-secondary"
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
            <table
                class="table table-hover table-striped list-table tabular-data"
            >
                <thead>
                    <tr>
                        <th scope="col">ID</th>
                        <th scope="col">Nome</th>
                        <th scope="col">Criação</th>
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
                            <td>{{$role->name}}</td>
                            <td>{{$role->created_at->format('d/m/Y')}}</td>
                            <td>
                                <div
                                    class="w-100 d-flex justify-content-between gap-1"
                                >
                                    <x-atoms.button
                                        format="anchor"
                                        class="btn-secondary"
                                        href="{{ route('roles.show', ['role' => $role->id]) }}"
                                        title="Visualizar"
                                    >
                                        <i class="bi bi-sunglasses"></i>
                                    </x-atoms.button>
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
                                        href="{{
                                            route(
                                                'roles.destroy',
                                                [
                                                    'role' => $role->id,
                                                    ...(request()->query() ?? [])
                                                ]
                                            )
                                        }}"
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
            </table>
            <x-app-pagination :paginator="$list" />
        </section>
        <x-packs.success-toast />
    </main>
</x-layout>
