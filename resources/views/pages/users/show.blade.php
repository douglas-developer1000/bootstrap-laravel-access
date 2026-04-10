@push ('styling')
    @vite ([
        'resources/css/pages/generic/default.css',
        'resources/css/pages/generic/table.css',
        'resources/css/pages/generic/show.css',
    ])
@endpush
<x-layout title="Visualizar Usuário">
    <x-packs.header>
        <x-packs.page-heading-row class="page-heading-row-custom">
            <x-slot:heading>
                Visualizar Usuário:
                <span class="text-primary ms-2">{{ $user->name }}</span>
            </x-slot:heading>
            <div class="top-right-item">
                <x-atoms.button
                    class="btn-secondary"
                    format="anchor"
                    href="{{ route('users.attach.permissions', ['user' => $user->id]) }}"
                    title="Vincular permissão direta"
                >
                    <i class="bi bi-box-arrow-in-down-left"></i>
                    <i class="bi bi-hammer"></i>
                </x-atoms.button>
                <x-atoms.button
                    class="btn-secondary"
                    format="anchor"
                    href="{{ route('users.attach.roles', ['user' => $user->id]) }}"
                    title="Vincular papel"
                >
                    <i class="bi bi-box-arrow-in-down-left"></i>
                    <i class="bi bi-clipboard2"></i>
                </x-atoms.button>
            </div>
        </x-packs.page-heading-row>
    </x-packs.header>
    <main class="bg-secondary-subtle create-main main-default">
        <section class="content bg-light d-flex flex-column row-gap-3">
            <fieldset
                class="border border-1 border-dark rounded-1 fieldset-tag"
            >
                <legend class="field-legend bg-light">Papéis</legend>
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
                        @forelse ($roles as $role)
                            <tr>
                                <td>{{$role->id}}</td>
                                <td>{{$role->name}}</td>
                                <td>
                                    <div
                                        class="w-100 d-flex justify-content-between gap-1"
                                    >
                                        <x-atoms.button
                                            class="btn-primary"
                                            data-bs-toggle="modal"
                                            data-bs-target="#confirmModal{{ $role->id }}"
                                            title="Desvincular"
                                        >
                                            <i class="bi bi-x-circle"></i>
                                        </x-atoms.button>
                                        <x-molecules.confirm-modal
                                            id="{{ $role->id }}"
                                            href="{{
                                                route(
                                                    'roles.unbind.roles',
                                                    [
                                                        'user' => $user->id,
                                                        'role' => $role->id,
                                                    ]
                                                )
                                            }}"
                                            heading="Desvincular este papel?"
                                            negative-text="Agora não"
                                            positive-text="Desvincular papel"
                                        >
                                            Isso desvinculará o papel
                                            <span
                                                class="fw-medium"
                                                >{{ $role->name }}</span
                                            >
                                            do usuário
                                            <span
                                                class="fw-medium"
                                                >{{ $user->name }}</span
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
                                    Sem papéis vinculados
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </fieldset>
            <fieldset
                class="border border-1 border-dark rounded-1 fieldset-tag"
            >
                <legend class="field-legend bg-light">
                    Permissões diretas
                </legend>
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
                                                    'roles.unbind.permissions',
                                                    [
                                                        'user' => $user->id,
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
                                            do usuário
                                            <span
                                                class="fw-medium"
                                                >{{ $user->name }}</span
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
                                    Sem permissões diretas vinculadas
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
