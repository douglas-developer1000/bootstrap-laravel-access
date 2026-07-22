@use ('App\Facades\DateFormatter')
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

<x-layout title="Vinculação de Permissões">
    <x-packs.header>
        <x-packs.page-heading-row class="page-heading-row-custom">
            <x-slot:heading>
                <span>Vinculação de permissões:</span>
                <a
                    href="{{ route('roles.show', ['role' => $role->id]) }}"
                    class="ms-2 text-decoration-none"
                    >{{ $role->name }}</a
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
                    :routeParams="['role' => $role->id]"
                    route="roles.group.bind"
                    heading="Vincular estas permissões?"
                    positive-text="Vincular permissões"
                    title="Vincular várias permissões"
                >
                    Isso vinculará as permissões selecionadas ao papel
                    <span class="fw-medium">{{ $role->name }}</span>.
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
                    @forelse ($list as $perm)
                        <tr>
                            <td>
                                <input
                                    type="checkbox"
                                    value="{{ $perm->id }}"
                                    class="form-check-input cursor-pointer multiselection-item"
                                />
                            </td>
                            <td>{{$perm->name}}</td>
                            <td>
                                {{ DateFormatter::formatToDate($perm->created_at) }}
                            </td>
                            <td>
                                <div
                                    class="w-100 d-flex justify-content-center gap-1"
                                >
                                    <x-organisms.confirm-attach-btn
                                        :routeParams="['role' => $role->id, 'permission' => $perm->id]"
                                        route="roles.bind"
                                        heading="Vincular esta permissão?"
                                        negative-text="Agora não"
                                        positive-text="Vincular permissão"
                                        title="Vincular"
                                    >
                                        Isso vinculará a permissão
                                        <span
                                            class="fw-medium"
                                            >{{ $perm->name }}</span
                                        >
                                        ao papel
                                        <span
                                            class="fw-medium"
                                            >{{ $role->name }}</span
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
            <x-molecules.root-pagination :paginator="$list" />
        </section>
        <x-packs.toast />
    </main>
</x-layout>
