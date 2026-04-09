@push ('styling')
    @vite ([
        'resources/css/pages/generic/index.css',
        'resources/css/pages/generic/table.css'
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
            <table
                class="table table-hover table-striped list-table tabular-data"
            >
                <thead>
                    <tr>
                        <th scope="col">Nº</th>
                        <th scope="col">Nome</th>
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
                            <td>{{$loop->iteration}}</td>
                            <td>{{$perm->name}}</td>
                            <td>
                                <div
                                    class="w-100 d-flex justify-content-between gap-1"
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
                                        href="{{
                                            route(
                                                'roles.bind',
                                                [
                                                    'role' => $role->id,
                                                    'permission' => $perm->id,
                                                    ...(request()->query() ?? [])
                                                ]
                                            )
                                        }}"
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
                                colspan="3"
                                class="no-values"
                            >
                                Sem permissões para o filtro atual
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
