@push ('styling')
    @vite ([
        'resources/css/pages/generic/default.css',
        'resources/css/pages/generic/table.css',
        'resources/css/pages/generic/show.css',
    ])
@endpush
@push ('ecmascript-bottom')
    @vite ([
        'resources/js/pages/generic/poli-multiselection.ts'
    ])
@endpush
@php
    $formRoleDetachGroupId = uniqid('form_');
    $formPermissionDetachGroupId = uniqid('form_');
@endphp

<x-layout title="Visualizar Usuário">
    <x-packs.header>
        <x-packs.page-heading-row>
            <x-slot:heading>
                Visualizar Usuário:
                <span class="text-primary ms-2">{{ $user->name }}</span>
            </x-slot:heading>
        </x-packs.page-heading-row>
    </x-packs.header>
    <main class="bg-secondary-subtle create-main main-default">
        <section class="content bg-light d-flex flex-column row-gap-3">
            <fieldset
                class="border border-1 border-dark rounded-1 fieldset-tag"
            >
                <legend class="field-legend bg-light">Papéis</legend>
                <div class="fieldset-top-btn">
                    <x-atoms.button
                        class="btn-secondary align-self-end justify-content-end multiselection-submit cursor-pointer detachment"
                        data-bs-toggle="modal"
                        data-bs-target="#confirmModalGroupRoleDetach"
                        title="Desvincular vários papéis"
                        data-form="{{ $formRoleDetachGroupId }}"
                        data-name="detachment[]"
                        data-key="roles"
                        disabled
                    >
                        <i class="bi bi-scissors"></i>
                    </x-atoms.button>
                    <x-atoms.button
                        class="btn-secondary"
                        format="anchor"
                        href="{{ route('users.attach.roles', ['user' => $user->id]) }}"
                        title="Vincular papel"
                    >
                        <i class="bi bi-plus h-1 icon-s2"></i>
                    </x-atoms.button>
                </div>
                <x-molecules.confirm-modal
                    id="GroupRoleDetach"
                    href="{!!
                        route('users.unbind.roles.group', [ 'user' => $user->id ])
                    !!}"
                    :formId="$formRoleDetachGroupId"
                    heading="Desvincular estes papéis?"
                    :method="method_field('DELETE')"
                    negative-text="Manter"
                    positive-text="Desvincular papéis"
                >
                    Isso desvinculará os papéis selecionados do usuário {{ $user->name }}.
                </x-molecules.confirm-modal>
                <table class="table tabular-data">
                    <thead>
                        <tr>
                            <th scope="col">
                                <input
                                    type="checkbox"
                                    class="form-check-input cursor-pointer multiselection-all"
                                    data-key="roles"
                                />
                            </th>
                            <th scope="col">Nome</th>
                            <th
                                scope="col"
                                class="last-thdata"
                                style="width: 4em"
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
                                        data-key="roles"
                                    />
                                </td>
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
                                                    'users.unbind.roles',
                                                    [
                                                        'user' => $user->id,
                                                        'role' => $role->id,
                                                    ]
                                                )
                                            }}"
                                            :method="method_field('DELETE')"
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
                <legend class="field-legend bg-light">Permissões</legend>
                <table class="table tabular-data">
                    <thead>
                        <tr>
                            <th scope="col">Nome</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($permissions as $perm)
                            <tr>
                                <td>{{$perm->name}}</td>
                            </tr>
                        @empty
                            <tr>
                                <td
                                    colspan="1"
                                    class="no-values"
                                >
                                    Sem permissões vinculadas
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
                <div class="fieldset-top-btn">
                    <x-atoms.button
                        class="btn-secondary align-self-end justify-content-end multiselection-submit cursor-pointer detachment"
                        data-bs-toggle="modal"
                        data-bs-target="#confirmModalGroupPermissionDetach"
                        title="Desvincular vários papéis"
                        data-form="{{ $formPermissionDetachGroupId }}"
                        data-name="detachment[]"
                        data-key="direct-permissions"
                        disabled
                    >
                        <i class="bi bi-scissors"></i>
                    </x-atoms.button>
                    <x-atoms.button
                        class="btn-secondary"
                        format="anchor"
                        href="{{ route('users.attach.permissions', ['user' => $user->id]) }}"
                        title="Vincular permissão direta"
                    >
                        <i class="bi bi-plus h-1 icon-s2"></i>
                    </x-atoms.button>
                </div>
                <x-molecules.confirm-modal
                    id="GroupPermissionDetach"
                    href="{!!
                        route('users.unbind.permissions.group', ['user' => $user->id])
                    !!}"
                    :formId="$formPermissionDetachGroupId"
                    heading="Desvincular estas permissões?"
                    :method="method_field('DELETE')"
                    negative-text="Manter"
                    positive-text="Desvincular permissões"
                >
                    Isso disvinculará as permissões diretas selecionadas do
                    usuário {{ $user->name }}.
                </x-molecules.confirm-modal>
                <table class="table tabular-data">
                    <thead>
                        <tr>
                            <th scope="col">
                                <input
                                    type="checkbox"
                                    class="form-check-input cursor-pointer multiselection-all"
                                    data-key="direct-permissions"
                                />
                            </th>
                            <th scope="col">Nome</th>
                            <th
                                scope="col"
                                class="last-thdata"
                                style="width: 4em"
                            >
                                Ações
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($dPermissions as $perm)
                            <tr>
                                <td>
                                    <input
                                        type="checkbox"
                                        value="{{ $perm->id }}"
                                        class="form-check-input cursor-pointer multiselection-item"
                                        data-key="direct-permissions"
                                    />
                                </td>
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
                                                    'users.unbind.permissions',
                                                    [
                                                        'user' => $user->id,
                                                        'permission' => $perm->id,
                                                    ]
                                                )
                                            }}"
                                            :method="method_field('DELETE')"
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
        <x-packs.toast />
    </main>
</x-layout>
