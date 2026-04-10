@push ('styling')
    @vite ([
        'resources/css/pages/generic/index.css',
        'resources/css/pages/generic/table.css'
    ])
@endpush

<x-layout title="Lista de Permissões">
    <x-packs.header>
        <x-packs.page-heading-row
            heading="Lista de Permissões"
            class="page-heading-row-custom"
        >
            <x-atoms.button
                class="custom-top-btn btn-secondary"
                format="anchor"
                href="{{ route('permissions.create') }}"
            >
                <i class="bi bi-plus h-1"></i>
            </x-atoms.button>
        </x-packs.page-heading-row>
    </x-packs.header>
    <main class="bg-secondary-subtle list-main">
        <section class="content bg-light">
            <x-packs.term-search
                label-text="Nome:"
                placeholder="Insira o nome da permissão"
            />
            <table
                class="table table-hover table-striped list-table tabular-data"
            >
                <thead>
                    <tr>
                        <x-app-table-head sort="id">ID</x-app-table-head>
                        <x-app-table-head sort="name">Nome</x-app-table-head>
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
                    @forelse ($list as $perm)
                        <tr>
                            <td>{{$perm->id}}</td>
                            <td>
                                <div class="ellipsis">{{$perm->name}}</div>
                            </td>
                            <td>{{$perm->created_at->format('d/m/Y')}}</td>
                            <td>
                                <div
                                    class="w-100 d-flex justify-content-between gap-1"
                                >
                                    <x-atoms.button
                                        format="anchor"
                                        class="btn-secondary"
                                        href="{{ route('permissions.edit', ['permission' => $perm->id]) }}"
                                    >
                                        <i class="bi bi-wrench"></i>
                                    </x-atoms.button>
                                    <x-atoms.button
                                        class="btn-danger"
                                        data-bs-toggle="modal"
                                        data-bs-target="#confirmModal{{ $perm->id }}"
                                    >
                                        <i class="bi bi-trash"></i>
                                    </x-atoms.button>
                                    <x-molecules.confirm-modal
                                        id="{{ $perm->id }}"
                                        href="{{ 
                                            route(
                                                'permissions.destroy',
                                                [
                                                    'permission' => $perm->id,
                                                    ...(request()->query() ?? [])
                                                ]
                                            )
                                        }}"
                                        heading="Remover esta permissão?"
                                        :method="method_field('DELETE')"
                                        negative-text="Manter"
                                        positive-text="Remover permissão"
                                    >
                                        Isso removerá permanentemente esta
                                        permissão.
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
                                Sem permissões para o filtro atual
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
