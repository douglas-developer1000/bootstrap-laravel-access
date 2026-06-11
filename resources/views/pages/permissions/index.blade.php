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

<x-layout title="Lista de Permissões">
    <x-packs.header>
        <x-packs.page-heading-row
            heading="Lista de Permissões"
            class="page-heading-row-custom"
        >
            <div class="top-right-item d-flex gap-2">
                <form
                    action="{{ route('permissions.flush') }}"
                    method="post"
                >
                    @csrf
                    <x-atoms.button
                        class="btn-secondary"
                        type="submit"
                    >
                        <i class="bi bi-database-fill-up"></i>
                    </x-atoms.button>
                </form>
                <x-atoms.button
                    class="btn-secondary"
                    format="anchor"
                    href="{{ route('permissions.create') }}"
                >
                    <i class="bi bi-plus h-1"></i>
                </x-atoms.button>
            </div>
        </x-packs.page-heading-row>
    </x-packs.header>
    <main class="bg-secondary-subtle list-main">
        <section class="content bg-light">
            <x-molecules.block-error :keys="['remotion', 'remotion.*']" />

            <div class="d-flex flex-wrap justify-content-between row-gap-2">
                <x-packs.term-search
                    label-text="Nome:"
                    placeholder="Insira o nome da permissão"
                />
                <x-organisms.confirm-rm-group-btn
                    route="permissions.group.destroy"
                    heading="Remover estas permissões?"
                    positive-text="Remover permissões"
                    title="Remover várias permissões"
                >
                    Isso removerá as permissões selecionadas permanentemente.
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
                    @forelse ($list as $perm)
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
                                    class="w-100 d-flex justify-content-between gap-1"
                                >
                                    <x-atoms.button
                                        format="anchor"
                                        class="btn-secondary"
                                        href="{{ route('permissions.edit', ['permission' => $perm->id]) }}"
                                    >
                                        <i class="bi bi-wrench"></i>
                                    </x-atoms.button>
                                    <x-organisms.confirm-rm-btn
                                        :routeParams="['permission' => $perm->id]"
                                        route="permissions.destroy"
                                        heading="Remover esta permissão?"
                                        positiveText="Remover permissão"
                                        title="Remover permissão"
                                    >
                                        Isso removerá permanentemente esta
                                        permissão.
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
                                Sem permissões para o filtro atual
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
