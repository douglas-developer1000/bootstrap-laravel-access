@push ('styling')
    @vite ([
        'resources/css/pages/generic/index.css',
        'resources/css/pages/generic/table.css'
    ])
@endpush

@php
    $trashed = request()->boolean('trashed');
    $subject = 'Usuários' . ($trashed ? ' Removidos' : '');
    $qs = request()->query->all();
@endphp

<x-layout title="Lista de {{ $subject  }}">
    <x-packs.header>
        <x-packs.page-heading-row
            heading="Lista de {{ $subject }}"
            class="page-heading-row-custom"
        >
            <div class="top-right-item">
                <x-atoms.button
                    title="Usuários {{ $trashed ? 'ativos' : 'removidos' }}"
                    class="btn-secondary"
                    format="anchor"
                    href="{{
                        route(
                            'users.index',
                            $trashed ? [] : ['trashed' => 1]
                        )
                    }}"
                >
                    @if ($trashed)
                        <i class="bi bi-toggle-on"></i>
                        Ativos
                    @else
                        <i class="bi bi-toggle-off"></i>
                        Removidos
                    @endif
                </x-atoms.button>
                <x-atoms.button
                    title="Criar usuário"
                    class="btn-secondary"
                    format="anchor"
                    href="{{ route('users.create') }}"
                >
                    <i class="bi bi-plus h-1"></i>
                </x-atoms.button>
            </div>
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
                                    @if ($trashed)
                                        <x-atoms.button
                                            class="btn-success"
                                            data-bs-toggle="modal"
                                            data-bs-target="#confirmModalRestore{{ $user->id }}"
                                            title="Restaurar usuário"
                                        >
                                            <i
                                                class="bi bi-arrow-return-left"
                                            ></i>
                                        </x-atoms.button>
                                        <x-molecules.confirm-modal
                                            id="Restore{{ $user->id }}"
                                            href="{!! 
                                                route(
                                                    'users.trashed.restore',
                                                    collect([
                                                        'user' => $user->id,
                                                    ])->merge($qs)->all()
                                                )
                                            !!}"
                                            heading="Restaurar este usuário?"
                                            negative-text="Manter"
                                            positive-text="Restaurar usuário"
                                        >
                                            Isso restaurará este usuário.
                                        </x-molecules.confirm-modal>
                                        <x-atoms.button
                                            class="btn-danger"
                                            data-bs-toggle="modal"
                                            data-bs-target="#confirmModalRemove{{ $user->id }}"
                                            title="Remover usuário"
                                        >
                                            <i class="bi bi-trash"></i>
                                        </x-atoms.button>
                                        <x-molecules.confirm-modal
                                            id="Remove{{ $user->id }}"
                                            href="{!! 
                                                route(
                                                    'users.trashed.destroy',
                                                    collect([
                                                        'user' => $user->id,
                                                    ])->merge($qs)->all()
                                                )
                                            !!}"
                                            heading="Remover este usuário?"
                                            :method="method_field('DELETE')"
                                            negative-text="Manter"
                                            positive-text="Remover usuário"
                                        >
                                            Isso removerá este usuário
                                            permanentemente.
                                        </x-molecules.confirm-modal>
                                    @else
                                        <x-atoms.button
                                            format="anchor"
                                            class="btn-secondary"
                                            href="{{ route('users.show', ['user' => $user->id]) }}"
                                            title="Visualizar dados do usuário"
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
                                                title="Remover usuário"
                                            >
                                                <i class="bi bi-trash"></i>
                                            </x-atoms.button>
                                            <x-molecules.confirm-modal
                                                id="{{ $user->id }}"
                                                href="{!! 
                                                    route(
                                                        'users.destroy',
                                                        collect([
                                                            'user' => $user->id,
                                                        ])->merge($qs)->all()
                                                    )
                                                !!}"
                                                heading="Remover este usuário?"
                                                :method="method_field('DELETE')"
                                                negative-text="Manter"
                                                positive-text="Remover usuário"
                                            >
                                                Isso removerá este usuário
                                                temporariamente.
                                            </x-molecules.confirm-modal>
                                        @endcan
                                    @endif
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
