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
            <x-molecules.block-error :keys="['remotion', 'remotion.*']" />
            <div class="d-flex flex-wrap justify-content-between row-gap-2">
                <x-packs.term-search
                    label-text="Nome:"
                    placeholder="Insira o nome do papel"
                />
                <x-organisms.confirm-rm-group-btn
                    route="roles.group.destroy"
                    heading="Remover estes papéis?"
                    positive-text="Remover papéis"
                    title="Remover vários papéis"
                >
                    Isso removerá os papéis selecionados permanentemente.
                </x-organisms.confirm-rm-group-btn>
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
                    @forelse ($list as $role)
                        <tr>
                            <td>
                                <input
                                    type="checkbox"
                                    value="{{ $role->id }}"
                                    class="form-check-input cursor-pointer multiselection-item"
                                    @if ($role->name === 'super-admin' || $role->name === 'user')
                                        disabled
                                    @endif
                                />
                            </td>
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
                                    <x-organisms.confirm-rm-btn
                                        :routeParams="['role' => $role->id]"
                                        route="roles.destroy"
                                        heading="Remover este papel?"
                                        positiveText="Remover papel"
                                        title="Remover papel"
                                    >
                                        Isso removerá permanentemente este
                                        papel.
                                    </x-organisms.confirm-rm-btn>
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
            <x-molecules.root-pagination :paginator="$list" />
        </section>
        <x-packs.toast />
    </main>
</x-layout>
