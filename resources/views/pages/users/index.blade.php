@push ('styling')
    @vite ([
        'resources/css/pages/generic/index.css',
        'resources/css/pages/generic/table.css'
    ])
@endpush

<x-layout title="Lista de Usuários">
    <x-packs.header>
        <x-packs.page-heading-row
            heading="Lista de Usuários"
            class="page-heading-row-custom"
        >
            <x-atoms.button
                class="custom-top-btn btn-secondary"
                format="anchor"
                href="{{ route('users.create') }}"
            >
                <i class="bi bi-plus h-1"></i>
            </x-atoms.button>
        </x-packs.page-heading-row>
    </x-packs.header>
    <main class="bg-secondary-subtle list-main">
        <section class="content bg-light">
            <x-packs.term-search
                label-text="Nome:"
                placeholder="Insira o nome do Usuário"
                key-term="name"
            />
            <table
                class="table table-hover table-striped list-table tabular-data"
            >
                <thead>
                    <tr>
                        <x-app-table-head sort="id">ID</x-app-table-head>
                        <x-app-table-head sort="name">Nome</x-app-table-head>
                        <x-app-table-head sort="email">Email</x-app-table-head>
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
                    @forelse ($list as $user)
                        <tr>
                            <td>{{$user->id}}</td>
                            <td>
                                <div class="ellipsis">{{$user->name}}</div>
                            </td>
                            <td>
                                <div class="ellipsis">{{$user->email}}</div>
                            </td>
                            <td>{{$user->created_at->format('d/m/Y')}}</td>
                            <td>
                                <div
                                    class="w-100 d-flex justify-content-end gap-1"
                                >
                                    <x-atoms.button
                                        format="anchor"
                                        class="btn-secondary"
                                        href="{{ route('users.show', ['user' => $user->id]) }}"
                                        title="Visualizar"
                                    >
                                        <i class="bi bi-sunglasses"></i>
                                    </x-atoms.button>
                                    <x-atoms.button
                                        format="anchor"
                                        class="btn-secondary"
                                        href="{{ route('users.edit', ['user' => $user->id]) }}"
                                    >
                                        <i class="bi bi-wrench"></i>
                                    </x-atoms.button>
                                    @can ('remove-user', $user)
                                        <x-atoms.button
                                            class="btn-danger"
                                            data-bs-toggle="modal"
                                            data-bs-target="#confirmModal{{ $user->id }}"
                                        >
                                            <i class="bi bi-trash"></i>
                                        </x-atoms.button>
                                        <x-molecules.confirm-modal
                                            id="{{ $user->id }}"
                                            href="{{ 
                                                route(
                                                    'users.destroy',
                                                    [
                                                        'user' => $user->id,
                                                        ...(request()->query() ?? [])
                                                    ]
                                                )
                                            }}"
                                            heading="Remover este usuário?"
                                            :method="method_field('DELETE')"
                                            negative-text="Manter"
                                            positive-text="Remover usuário"
                                        >
                                            Isso removerá este usuário.
                                        </x-molecules.confirm-modal>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td
                                colspan="5"
                                class="no-values"
                            >
                                Sem usuários para o filtro atual
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
