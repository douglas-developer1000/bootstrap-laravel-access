@push ('styling')
    @vite ([
        'resources/css/pages/generic/default.css',
        'resources/css/pages/generic/table.css',
        'resources/css/pages/generic/show.css',
    ])
@endpush
@push ('ecmascript-bottom')
    @vite ([
        'resources/js/pages/generic/multiselection.ts'
    ])
@endpush

@php
    $formPermissionDetachGroupId = uniqid('form_');
@endphp

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
                <x-atoms.button
                    class="btn-secondary align-self-end justify-content-end multiselection-submit cursor-pointer fieldset-top-btn detachment"
                    data-bs-toggle="modal"
                    data-bs-target="#confirmModalGroupPermissionDetach"
                    title="Desvincular várias permissões"
                    data-form="{{ $formPermissionDetachGroupId }}"
                    data-name="detachment[]"
                    disabled
                >
                    <i class="bi bi-scissors"></i>
                </x-atoms.button>
                <x-molecules.confirm-modal
                    id="GroupPermissionDetach"
                    href="{!!
                        route('roles.group.unbind', [ 'role' => $role->id ])
                    !!}"
                    :formId="$formPermissionDetachGroupId"
                    heading="Desvincular estas permissões?"
                    :method="method_field('DELETE')"
                    negative-text="Manter"
                    positive-text="Desvincular permissões"
                >
                    Isso desvinculará as permissões selecionadas do papel {{ $role->name }}.
                </x-molecules.confirm-modal>
                <table class="table tabular-data">
                    <thead>
                        <tr>
                            <th scope="col">
                                <input
                                    type="checkbox"
                                    class="form-check-input cursor-pointer multiselection-all"
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
                                    <div class="ellipsis">
                                        {{ $perm->name }}
                                    </div>
                                </td>
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
                                    colspan="3"
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
