@use ('App\Libraries\Utils\DatetimeFormatter')
@use ('App\Libraries\Enums\LicenseStatusEnum')
@push ('styling')
    @vite ([
        'resources/css/pages/generic/index.css',
        'resources/css/pages/generic/table.css',
    ])
@endpush
@push ('ecmascript-bottom')
    @vite ([
        'resources/js/pages/generic/multiselection.ts'
    ])
@endpush

<x-layout title="{{ $title }}">
    <x-packs.header>
        <x-packs.page-heading-row
            :heading="$title"
            class="page-heading-row-custom"
        />
    </x-packs.header>
    <main class="bg-secondary-subtle list-main">
        <section class="content bg-light">
            <div class="d-flex flex-wrap justify-content-between row-gap-2">
                <x-packs.filter-form-checks
                    class="gap-3"
                    :checkboxes="$checkboxesData"
                />
            </div>
            <div class="d-flex flex-wrap justify-content-between row-gap-2">
                <x-packs.term-search
                    label-text="Nome:"
                    placeholder="Insira o nome do utilizador"
                />
            </div>
            <x-molecules.table-index
                :styleRows="[
                    'first' => 'width: 1.75em;',
                    'second' => 'width: 6.5em;',
                ]"
                :qtyBtns="2"
            >
                <x-slot:cols>
                    <col class="col-remain-status" />
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
                        <x-atoms.table-head
                            sort="licensableName"
                        >
                            Utilizador</x-atoms.table-head
                        >
                        <x-atoms.table-head
                            colRemain
                            sort="status"
                        >
                            Status</x-atoms.table-head
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
                    @forelse ($models($list) as $license)
                        <tr>
                            <td>
                                <input
                                    type="checkbox"
                                    value="{{ $license->id }}"
                                    class="form-check-input cursor-pointer multiselection-item"
                                />
                            </td>
                            <td>
                                <a
                                    href="{{
                                        route('licenses.show', [
                                            'license' => $license->id
                                        ])
                                    }}"
                                    class="text-truncate text-decoration-none text-info border-0 ps-0"
                                >
                                    {{ $license->licensable->name }}
                                </a>
                            </td>
                            <td>{{ $parseStatus($license->status) }}</td>
                            <td>
                                {{ DatetimeFormatter::formatToDate($license->created_at) }}
                            </td>
                            <td>
                                <div
                                    class="w-100 d-flex justify-content-center gap-1"
                                >
                                    @php /** @see App\View\Components\Organisms\ConfirmCancelBtn::class */ @endphp
                                    <x-organisms.confirm-cancel-btn
                                        :routeParams="['license' => $license->id]"
                                        route="licenses.cancel"
                                        heading="Cancelar esta licença?"
                                        positiveText="Cancelar licença"
                                        title="Cancelar licença"
                                        :disabled="!$license->isPreCancellable && !$license->isPostCancellable"
                                    >
                                        Essa licença mudará seu status de 
                                        "{{
                                            $license->status->toString()
                                        }}" para "{{
                                            LicenseStatusEnum::CANCELED->toString()
                                        }}".
                                    </x-organisms.confirm-cancel-btn>
                                    <x-organisms.confirm-activate-btn
                                        :routeParams="['license' => $license->id]"
                                        route="licenses.activate"
                                        heading="Ativar esta licença?"
                                        positiveText="Ativar licença"
                                        negativeText="Ainda não"
                                        title="Ativar licença"
                                        :disabled="!$license->isActivatable && !$license->isReactivatable"
                                    >
                                        Essa licença mudará seu status de 
                                        "{{
                                            $license->status->toString()
                                        }}" para "{{
                                            LicenseStatusEnum::ACTIVE->toString()
                                        }}".
                                    </x-organisms.confirm-activate-btn>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td
                                colspan="5"
                                class="no-values"
                            >
                                Sem licenças para o filtro atual
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
