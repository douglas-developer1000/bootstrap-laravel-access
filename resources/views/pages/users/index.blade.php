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
    $subject = 'Usuários' . ($trashed ? ' Removidos' : '');
    $qs = request()->query->all();

    $formRemotionGroupId = uniqid('formRemove_');
    $formRestarionGroupId = uniqid('formRestore_');
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
            @if ($errors->has('remotion') || $errors->has('remotion.*'))
                <div
                    class="p-3 text-danger-emphasis bg-danger-subtle border border-danger-subtle rounded-3"
                >
                    {{ $message }}
                </div>
            @endif
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
                        <x-atoms.button
                            class="btn-secondary align-self-end justify-content-end multiselection-submit cursor-pointer"
                            data-bs-toggle="modal"
                            data-bs-target="#confirmModalGroupRestore"
                            title="Restaurar vários usuários"
                            disabled
                            data-form="{{ $formRestarionGroupId }}"
                            data-name="restoration[]"
                        >
                            Restaurar selecionados
                        </x-atoms.button>
                        <x-molecules.confirm-modal
                            id="GroupRestore"
                            href="{!!
                                route('users.trashed.group.restore')
                            !!}"
                            :formId="$formRestarionGroupId"
                            heading="Restaurar estes usuários?"
                            negative-text="Manter"
                            positive-text="Restaurar usuários"
                        >
                            Isso restaurará os usuários selecionados.
                        </x-molecules.confirm-modal>
                    @endif
                    <x-atoms.button
                        class="btn-secondary align-self-end justify-content-end multiselection-submit cursor-pointer"
                        data-bs-toggle="modal"
                        data-bs-target="#confirmModalGroupRemove"
                        title="Remover vários usuários"
                        data-form="{{ $formRemotionGroupId }}"
                        data-name="remotion[]"
                        disabled
                    >
                        Remover selecionados
                    </x-atoms.button>
                    <x-molecules.confirm-modal
                        id="GroupRemove"
                        href="{!!
                        route('users.group.destroy', $qs)
                    !!}"
                        :formId="$formRemotionGroupId"
                        heading="Remover estes usuários?"
                        :method="method_field('DELETE')"
                        negative-text="Manter"
                        positive-text="Remover usuários"
                    >
                        Isso removerá os usuários selecionados permanentemente.
                    </x-molecules.confirm-modal>
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
                        <x-app-table-head sort="name">Nome</x-app-table-head>
                        <x-app-table-head
                            colRemain
                            sort="email"
                            >Email</x-app-table-head
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
                                <div class="ellipsis">{{$user->email}}</div>
                            </td>
                            <td>{{ $user->created_at_formatted }}</td>
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
                                            href="{{ route('users.edit', ['user' => $user->id]) }}"
                                        >
                                            <i class="bi bi-wrench"></i>
                                        </x-atoms.button>
                                        {{-- Gate defined on provider --}}
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
            </x-molecules.table-index>
            <x-app-pagination :paginator="$list" />
        </section>
        <x-packs.success-toast />
    </main>
</x-layout>
