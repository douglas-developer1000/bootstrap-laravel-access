@push ('styling')
    @vite ([
        'resources/css/pages/generic/index.css',
        'resources/css/pages/generic/table.css'
    ])
@endpush

@php
    $qs = request()->query->all();
@endphp
@use ('App\Libraries\Utils\DatetimeFormatter')

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
            <x-packs.term-search
                label-text="Nome:"
                placeholder="Insira o nome da permissão"
            />
            <x-molecules.table-index>
                <x-slot:cols>
                    <col class="col-remain-created_at" />
                </x-slot:cols>
                <thead>
                    <tr>
                        <x-app-table-head sort="id">ID</x-app-table-head>
                        <x-app-table-head sort="name">Nome</x-app-table-head>
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
                    @forelse ($list as $perm)
                        <tr>
                            <td>{{$perm->id}}</td>
                            <td>{{$perm->name}}</td>
                            <td>
                                {{ DatetimeFormatter::formatToDate($perm->created_at) }}
                            </td>
                            <td>
                                <div
                                    class="w-100 d-flex justify-content-center gap-1"
                                >
                                    <x-atoms.button
                                        class="btn-primary"
                                        data-bs-toggle="modal"
                                        data-bs-target="#confirmModal{{ $perm->id }}"
                                        title="Vincular"
                                    >
                                        <i class="bi bi-paperclip"></i>
                                    </x-atoms.button>
                                    <x-molecules.confirm-modal
                                        id="{{ $perm->id }}"
                                        href="{!!
                                            route(
                                                'roles.bind',
                                                collect([
                                                    'role' => $role->id,
                                                    'permission' => $perm->id,
                                                ])->merge($qs)->all()
                                            )
                                        !!}"
                                        heading="Vincular esta permissão?"
                                        negative-text="Agora não"
                                        positive-text="Vincular permissão"
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
            </x-molecules.table-index>
            <x-app-pagination :paginator="$list" />
        </section>
        <x-packs.success-toast />
    </main>
</x-layout>
