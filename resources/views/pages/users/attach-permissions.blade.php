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
                <span>Vinculação de permissões diretas:</span>
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
                    placeholder="Insira o nome da permissão"
                />
                <x-organisms.confirm-attach-group-btn
                    :routeParams="['user' => $user->id]"
                    route="users.bind.permissions.group"
                    heading="Vincular estas permissões?"
                    positive-text="Vincular permissões"
                    title="Vincular permissões diretas selecionadas"
                >
                    Isso vinculará as permissões diretas selecionadas ao usuário
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
                    @forelse ($permissions as $perm)
                        <tr>
                            <td>
                                <input
                                    type="checkbox"
                                    value="{{ $perm->id }}"
                                    class="form-check-input cursor-pointer multiselection-item"
                                />
                            </td>
                            <td>
                                <div class="ellipsis">{{$perm->name}}</div>
                            </td>
                            <td>
                                {{ DatetimeFormatter::formatToDate($perm->created_at) }}
                            </td>
                            <td>
                                <div
                                    class="w-100 d-flex justify-content-center gap-1"
                                >
                                    <x-organisms.confirm-attach-btn
                                        :routeParams="['user' => $user->id, 'permission' => $perm->id]"
                                        route="users.bind.permissions"
                                        heading="Vincular esta permissão?"
                                        negative-text="Agora não"
                                        positive-text="Vincular permissão"
                                        title="Vincular"
                                    >
                                        Isso vinculará diretamente a permissão
                                        <span
                                            class="fw-medium"
                                            >{{ $perm->name }}</span
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
                                Sem permissões para o filtro atual
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </x-molecules.table-index>
            <x-molecules.root-pagination :paginator="$permissions" />
        </section>
        <x-packs.toast />
    </main>
</x-layout>
