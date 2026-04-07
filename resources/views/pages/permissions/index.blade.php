@push ('styling')
    @vite ('resources/css/pages/permissions/index.css')
@endpush

<x-layout title="Lista de Permissões">
    <x-packs.header>
        <x-packs.page-heading-row
            heading="Lista de Permissões"
            class="page-heading-row-custom"
        >
            <x-atoms.button
                class="custom-create-btn btn-secondary"
                format="anchor"
                href="{{ route('permissions.create') }}"
            >
                <i class="bi bi-plus h-1"></i>
            </x-atoms.button>
        </x-packs.page-heading-row>
    </x-packs.header>
    <main class="bg-secondary-subtle permission-list-main">
        <section class="content bg-light">
            <table
                class="table table-hover table-striped permission-list-table"
            >
                <thead>
                    <tr>
                        <th scope="col">Nº</th>
                        <th scope="col">Nome</th>
                        <th scope="col">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($list as $perm)
                        <tr>
                            <td>{{$loop->iteration}}</td>
                            <td>{{$perm->name}}</td>
                            <td>
                                <div
                                    class="w-100 d-flex justify-content-between"
                                >
                                    <x-atoms.button
                                        format="anchor"
                                        class="btn-secondary"
                                        href="{{ route('permissions.edit', ['permission' => $perm->id]) }}"
                                    >
                                        <i class="bi bi-wrench"></i>
                                    </x-atoms.button>
                                    <x-atoms.button
                                        class="btn-danger"
                                        data-bs-toggle="modal"
                                        data-bs-target="#confirmModal{{ $perm->id }}"
                                    >
                                        <i class="bi bi-trash"></i>
                                    </x-atoms.button>
                                    <x-molecules.confirm-modal
                                        id="{{ $perm->id }}"
                                        href="{{ route('permissions.destroy', ['permission' => $perm->id]) }}"
                                        heading="Remover esta permissão?"
                                        :method="method_field('DELETE')"
                                        negative-text="Manter"
                                        positive-text="Remover permissão"
                                    >
                                        Isso removerá permanentemente esta
                                        permissão.
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
            {{ $list->links('components.packs.pagination') }}
        </section>
        <x-packs.success-toast />
    </main>
</x-layout>
