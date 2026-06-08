@use ('App\Libraries\Utils\DatetimeFormatter')
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

<x-layout title="Vinculação de Usuário">
    <x-packs.header>
        <x-packs.page-heading-row class="page-heading-row-custom">
            <x-slot:heading>
                <span>Vinculação de papéis:</span>
                <a
                    href="{{ route('users.show', ['user' => $user->id]) }}"
                    href=""
                    class="ms-2 text-decoration-none"
                    >{{ $user->name }}</a
                >
            </x-slot:heading>
        </x-packs.page-heading-row>
    </x-packs.header>
    <main class="bg-secondary-subtle list-main">
        <section class="content bg-light">
            <x-molecules.block-error :keys="['attachment', 'attachment.*']" />
            <div class="d-flex flex-wrap justify-content-between row-gap-2">
                <x-packs.term-search
                    label-text="Nome:"
                    placeholder="Insira o nome do papel"
                />
                <x-organisms.confirm-attach-group-btn
                    :routeParams="['user' => $user->id]"
                    route="users.bind.roles.group"
                    heading="Vincular estes papéis?"
                    positive-text="Vincular papéis"
                    title="Vincular vários papéis"
                >
                    Isso vinculará os papéis selecionados ao usuário
                    <span class="fw-medium">{{ $user->name }}</span>.
                </x-organisms.confirm-attach-group-btn>
            </div>
            <x-molecules.table-index>
                <x-slot:cols>
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
                    @forelse ($roles as $role)
                        <tr>
                            <td>
                                <input
                                    type="checkbox"
                                    value="{{ $role->id }}"
                                    class="form-check-input cursor-pointer multiselection-item"
                                />
                            </td>
                            <td>
                                <div class="ellipsis">{{ $role->name }}</div>
                            </td>
                            <td>
                                {{ DatetimeFormatter::formatToDate($role->created_at) }}
                            </td>
                            <td>
                                <div
                                    class="w-100 d-flex justify-content-center gap-1"
                                >
                                    <x-organisms.confirm-attach-btn
                                        :routeParams="['user' => $user->id, 'role' => $role->id]"
                                        route="users.bind.roles"
                                        heading="Vincular este papel?"
                                        negative-text="Agora não"
                                        positive-text="Vincular papel"
                                        title="Vincular"
                                    >
                                        Isso vinculará o papel
                                        <span
                                            class="fw-medium"
                                            >{{ $role->name }}</span
                                        >
                                        ao usuário
                                        <span
                                            class="fw-medium"
                                            >{{ $user->name }}</span
                                        >.
                                    </x-organisms.confirm-attach-btn>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td
                                colspan="4"
                                class="no-values"
                            >
                                Sem papéis para o filtro atual
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </x-molecules.table-index>
            <x-molecules.root-pagination :paginator="$roles" />
        </section>
        <x-packs.toast />
    </main>
</x-layout>
