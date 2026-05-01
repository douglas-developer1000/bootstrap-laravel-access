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
@use ('App\Libraries\Utils\PhoneFormatter')

@php
    $qs = request()->query->all();
    $formRemotionGroupId = uniqid('form_');
@endphp

<x-layout title="Aprovações de Registro">
    <x-packs.header>
        <x-packs.page-heading-row
            heading="Aprovações de Registro"
            class="page-heading-row-custom"
        />
    </x-packs.header>
    <main class="bg-secondary-subtle list-main">
        <section class="content bg-light">
            @if ($errors->has('remotion') || $errors->has('remotion.*'))
                <div
                    class="p-3 text-danger-emphasis bg-danger-subtle border border-danger-subtle rounded-3"
                >
                    {{ $message }}
                </div>
            @endif
            <div class="d-flex flex-wrap justify-content-between row-gap-2">
                <x-packs.term-search
                    label-text="Nome:"
                    placeholder="Insira um email"
                />
                <x-atoms.button
                    class="btn-secondary align-self-end justify-content-end multiselection-submit cursor-pointer"
                    data-bs-toggle="modal"
                    data-bs-target="#confirmModalGroupRemove"
                    title="Remover vários papéis"
                    data-form="{{ $formRemotionGroupId }}"
                    data-name="remotion[]"
                    disabled
                >
                    Remover selecionados
                </x-atoms.button>
                <x-molecules.confirm-modal
                    id="GroupRemove"
                    href="{!!
                        route('register.approvals.group.destroy', $qs)
                    !!}"
                    :formId="$formRemotionGroupId"
                    heading="Remover estas aprovações?"
                    :method="method_field('DELETE')"
                    negative-text="Manter"
                    positive-text="Remover aprovações"
                >
                    Isso removerá as aprovações selecionadas permanentemente.
                </x-molecules.confirm-modal>
            </div>
            <x-molecules.table-index>
                <x-slot:cols>
                    <col class="col-remain-phone" />
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
                        <x-app-table-head sort="name">E-mail</x-app-table-head>
                        <th
                            scope="col"
                            class="col-remain"
                        >
                            Telefone
                        </th>
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
                    @forelse ($list as $approval)
                        <tr>
                            <td>
                                <input
                                    type="checkbox"
                                    value="{{ $approval->id }}"
                                    class="form-check-input cursor-pointer multiselection-item"
                                />
                            </td>
                            <td>
                                <div class="ellipsis">
                                    {{ $approval->email }}
                                </div>
                            </td>
                            <td>
                                {{ PhoneFormatter::toView($approval->phone) }}
                            </td>
                            <td>{{ $approval->created_at_formatted }}</td>
                            <td>
                                <div
                                    class="w-100 d-flex justify-content-center gap-1"
                                >
                                    <x-atoms.button
                                        class="btn-danger"
                                        data-bs-toggle="modal"
                                        data-bs-target="#confirmModal{{ $approval->id }}"
                                    >
                                        <i class="bi bi-trash"></i>
                                    </x-atoms.button>
                                    <x-molecules.confirm-modal
                                        id="{{ $approval->id }}"
                                        href="{!! 
                                            route(
                                                'register.approvals.destroy',
                                                collect([
                                                    'approval' => $approval->id,
                                                ])->merge($qs)->all()
                                            )
                                        !!}"
                                        heading="Remover esta aprovação de registro?"
                                        :method="method_field('DELETE')"
                                        negative-text="Manter"
                                        positive-text="Remover aprovação"
                                    >
                                        Isso removerá permanentemente esta
                                        aprovação.
                                    </x-molecules.confirm-modal>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td
                                colspan="5"
                                class="no-values"
                            >
                                Sem aprovações para o filtro atual
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </x-molecules.table-index>
            <x-app-pagination :paginator="$list" />
        </section>
        <x-packs.toast />
    </main>
</x-layout>
