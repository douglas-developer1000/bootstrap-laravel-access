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

<x-layout title="Aprovações de Registro">
    <x-packs.header>
        <x-packs.page-heading-row
            heading="Aprovações de Registro"
            class="page-heading-row-custom"
        />
    </x-packs.header>
    <main class="bg-secondary-subtle list-main">
        <section class="content bg-light">
            <x-molecules.block-error :keys="['remotion', 'remotion.*']" />
            <div class="d-flex flex-wrap justify-content-between row-gap-2">
                <x-packs.term-search
                    label-text="Nome:"
                    placeholder="Insira um email"
                />
                <x-organisms.confirm-rm-group-btn
                    route="register.approvals.group.destroy"
                    heading="Remover estas aprovações?"
                    positive-text="Remover aprovações"
                    title="Remover vários papéis"
                >
                    Isso removerá as aprovações selecionadas permanentemente.
                </x-organisms.confirm-rm-group-btn>
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
                        <x-atoms.table-head sort="name">
                            E-mail</x-atoms.table-head
                        >
                        <th
                            scope="col"
                            class="col-remain"
                        >
                            Telefone
                        </th>
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
                            <td>{{ $approval->phone }}</td>
                            <td>
                                {{ DatetimeFormatter::formatToDate($approval->created_at) }}
                            </td>
                            <td>
                                <div
                                    class="w-100 d-flex justify-content-center gap-1"
                                >
                                    <x-organisms.confirm-rm-btn
                                        :routeParams="['approval' => $approval->id]"
                                        route="register.approvals.destroy"
                                        heading="Remover esta aprovação de registro?"
                                        positiveText="Remover aprovação"
                                        title="Remover aprovação"
                                    >
                                        Isso removerá permanentemente esta
                                        aprovação.
                                    </x-organisms.confirm-rm-btn>
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
            <x-molecules.root-pagination :paginator="$list" />
        </section>
        <x-packs.toast />
    </main>
</x-layout>
