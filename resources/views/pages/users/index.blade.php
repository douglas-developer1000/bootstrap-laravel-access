@use ('App\Libraries\Utils\DatetimeFormatter')
@push ('styling')
    @vite ([
        'resources/css/pages/generic/index.css',
        'resources/css/pages/generic/table.css',
    ])
@endpush

@push ('ecmascript-bottom')
    @vite ([
        'resources/js/pages/generic/multiselection.ts'
    ])
@endpush

@php
    $trashed = request()->boolean('trashed');
    $subject = 'Contas' . ($trashed ? ' Removidas' : '');
@endphp

<x-layout title="{{ $subject  }}">
    <x-packs.header>
        <x-packs.page-heading-row
            :heading="$subject"
            class="page-heading-row-custom"
        >
            <div class="top-right-item">
                <x-atoms.button
                    title="Contas {{ $trashed ? 'ativas' : 'removidas' }}"
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
            <x-molecules.block-error
                :keys="[
                    'remotion', 'remotion.*', 'restoration', 'restoration.*'
                ]"
            />
            <div class="d-flex flex-wrap justify-content-between row-gap-2">
                <x-packs.term-search
                    label-text="Nome:"
                    placeholder="Insira o nome do Usuário"
                    key-term="name"
                />
                <div
                    class="d-flex justify-content-end flex-grow-1 column-gap-2"
                >
                    @if ($trashed)
                        <x-organisms.confirm-restore-group-btn
                            route="users.trashed.group.restore"
                            heading="Restaurar estes usuários?"
                            positive-text="Restaurar usuários"
                            title="Restaurar usuários selecionados"
                        >
                            Isso restaurará os usuários selecionados.
                        </x-organisms.confirm-restore-group-btn>
                    @endif
                    <x-organisms.confirm-rm-group-btn
                        route="users.group.destroy"
                        heading="Remover estes usuários?"
                        positive-text="Remover usuários"
                        title="Remover vários usuários"
                    >
                        Isso removerá os usuários selecionados {{ $trashed ? 'permanentemente' : 'temporariamente' }}.
                    </x-organisms.confirm-rm-group-btn>
                </div>
            </div>
            <x-molecules.table-index>
                <x-slot:cols>
                    <col class="col-remain-email" />
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
                            Nome</x-atoms.table-head
                        >
                        <x-atoms.table-head
                            colRemain
                            sort="email"
                        >
                            Email</x-atoms.table-head
                        >
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
                    @forelse ($list as $user)
                        <tr>
                            <td>
                                <input
                                    type="checkbox"
                                    value="{{ $user->id }}"
                                    class="form-check-input cursor-pointer multiselection-item"
                                    @cannot ('remove-user', $user)
                                        disabled
                                    @endcannot
                                />
                            </td>
                            <td>
                                <a
                                    class="ellipsis text-decoration-none text-info"
                                    href="{{ route('users.show', ['user' => $user->id]) }}"
                                    title="Visualizar dados do usuário"
                                    >{{ $user->name }}</a
                                >
                            </td>
                            <td>
                                <div
                                    class="text-truncate"
                                    title="{{ $user->email }}"
                                >
                                    {{$user->email}}
                                </div>
                            </td>
                            <td>
                                {{ DatetimeFormatter::formatToDate($user->created_at) }}
                            </td>
                            <td>
                                <div
                                    class="w-100 d-flex justify-content-end gap-1"
                                >
                                    @if ($trashed)
                                        <x-organisms.confirm-restore-btn
                                            route="users.trashed.restore"
                                            :routeParams="['user' => $user->id]"
                                            heading="Restaurar este usuário?"
                                            positiveText="Restaurar usuário"
                                            title="Restaurar usuário"
                                        >
                                            Isso restaurará este usuário.
                                        </x-organisms.confirm-restore-btn>
                                        <x-organisms.confirm-rm-btn
                                            :routeParams="['user' => $user->id]"
                                            route="users.trashed.destroy"
                                            heading="Remover este usuário?"
                                            positiveText="Remover usuário"
                                            title="Remover usuário"
                                        >
                                            Isso removerá este usuário
                                            permanentemente.
                                        </x-organisms.confirm-rm-btn>
                                    @else
                                        <x-atoms.button
                                            format="anchor"
                                            class="btn-secondary"
                                            href="{{ route('users.edit', ['user' => $user->id]) }}"
                                        >
                                            <i class="bi bi-wrench"></i>
                                        </x-atoms.button>
                                        {{-- Gate defined on provider --}}
                                        @can ('remove-user', $user)
                                            <x-organisms.confirm-rm-btn
                                                :routeParams="['user' => $user->id]"
                                                route="users.destroy"
                                                heading="Remover este usuário?"
                                                positiveText="Remover usuário"
                                                title="Remover usuário"
                                            >
                                                Isso removerá este usuário
                                                temporariamente.
                                            </x-organisms.confirm-rm-btn>
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
            </x-molecules.table-index>
            <x-molecules.root-pagination :paginator="$list" />
        </section>
        <x-packs.toast />
    </main>
</x-layout>
