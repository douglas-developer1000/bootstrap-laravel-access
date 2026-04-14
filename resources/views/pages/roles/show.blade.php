@push ('styling')
    @vite ([
        'resources/css/pages/generic/default.css',
        'resources/css/pages/generic/table.css',
    ])
@endpush
<x-layout title="Visualizar Papel">
    <x-packs.header>
        <x-packs.page-heading-row>
            <x-slot:heading>
                Visualizar Papel:
                <span class="text-primary ms-2">{{ $role->name }}</span>
            </x-slot:heading>
            <x-atoms.button
                class="top-right-item btn-secondary"
                format="anchor"
                href="{{ route('roles.attach', ['role' => $role->id]) }}"
            >
                <i class="bi bi-box-arrow-in-down-left"></i>
            </x-atoms.button>
        </x-packs.page-heading-row>
    </x-packs.header>
    <main class="bg-secondary-subtle create-main main-default">
        <section class="content bg-light d-flex flex-column row-gap-3">
            <fieldset
                class="border border-1 border-dark rounded-1 fieldset-tag"
            >
                <legend class="field-legend bg-light">Permissões</legend>
                <table class="table tabular-data">
                    <thead>
                        <tr>
                            <th scope="col">ID</th>
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
                        @forelse ($permissions as $perm)
                            <tr>
                                <td>{{$perm->id}}</td>
                                <td>{{$perm->name}}</td>
                                <td>
                                    <div
                                        class="w-100 d-flex justify-content-between gap-1"
                                    >
                                        <x-atoms.button
                                            class="btn-primary"
                                            data-bs-toggle="modal"
                                            data-bs-target="#confirmModal{{ $perm->id }}"
                                            title="Desvincular"
                                        >
                                            <i class="bi bi-x-circle"></i>
                                        </x-atoms.button>
                                        <x-molecules.confirm-modal
                                            id="{{ $perm->id }}"
                                            href="{{
                                                route(
                                                    'roles.unbind',
                                                    [
                                                        'role' => $role->id,
                                                        'permission' => $perm->id,
                                                    ]
                                                )
                                            }}"
                                            heading="Desvincular esta permissão?"
                                            negative-text="Agora não"
                                            positive-text="Desvincular permissão"
                                        >
                                            Isso desvinculará a permissão
                                            <span
                                                class="fw-medium"
                                                >{{ $perm->name }}</span
                                            >
                                            do papel
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
                                    colspan="2"
                                    class="no-values"
                                >
                                    Sem permissões vinculadas
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </fieldset>
        </section>
        <x-packs.success-toast />
    </main>
</x-layout>
