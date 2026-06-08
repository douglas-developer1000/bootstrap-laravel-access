@use ('App\Models\PaymentCard')
@use ('App\Libraries\Utils\DatetimeFormatter')

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

@php
    $trashed = request()->boolean('trashed');
    $subject = $trashed ? 'Cartões removidos' : 'Cartões';
@endphp

<x-layout title="{{ $subject }}">
    <x-packs.header>
        <x-packs.page-heading-row
            :heading="$subject"
            class="page-heading-row-custom"
        >
            <div class="dropdown top-right-item">
                <x-atoms.button
                    class="btn-secondary dropdown-toggle"
                    data-bs-toggle="dropdown"
                    aria-expanded="false"
                >
                    <i class="bi bi-menu-button-wide"></i>
                </x-atoms.button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        <x-atoms.button
                            title="Cartões {{ $trashed ? 'ativos' : 'removidos' }}"
                            class="dropdown-item"
                            format="anchor"
                            href="{{
                                route(
                                    'payment-cards.index',
                                    $trashed ? [] : ['trashed' => 1]
                                )
                            }}"
                        >
                            @if ($trashed)
                                <i class="bi bi-check-lg"></i>
                                Cartões Ativos
                            @else
                                <i class="bi bi-trash"></i>
                                Cartões Removidos
                            @endif
                        </x-atoms.button>
                    </li>
                    @if (!$trashed)
                        @can ('create', PaymentCard::class)
                            <li>
                                <x-atoms.button
                                    class="dropdown-item"
                                    format="anchor"
                                    href="{{ route('payment-cards.create') }}"
                                >
                                    <i class="bi bi-plus-lg"></i>
                                    <span>Cartão</span>
                                </x-atoms.button>
                            </li>
                        @endcan
                    @endif
                </ul>
            </div>
        </x-packs.page-heading-row>
    </x-packs.header>
    <main class="bg-secondary-subtle list-main">
        <section class="content bg-light">
            <x-molecules.block-error
                :keys="[
                    'remotion', 'remotion.*',
                    'restoration', 'restoration.*',
                    'id'
                ]"
            />
            <div class="d-flex flex-wrap justify-content-between row-gap-2">
                <x-packs.term-search
                    label-text="Nome:"
                    placeholder="Insira o nome do cartão"
                />
                <div
                    class="d-flex justify-content-end flex-grow-1 column-gap-2"
                >
                    @if ($trashed)
                        <x-organisms.confirm-restore-group-btn
                            :routeParams="['key' => 'restoration', 'paymentCardList' => 'trashed']"
                            route="payment-cards.group.restore"
                            heading="Restaurar estes Cartões?"
                            positive-text="Restaurar Cartões"
                            title="Restaurar cartões selecionados"
                        >
                            Isso restaurará os cartões selecionados e todos seus
                            dados relacionados.
                        </x-organisms.confirm-restore-group-btn>
                    @else
                        <x-organisms.confirm-rm-group-btn
                            :routeParams="['key' => 'remotion', 'paymentCardList' => 'list']"
                            route="payment-cards.group.destroy"
                            heading="Remover estes Cartões?"
                            positive-text="Remover Cartões"
                            title="Remover cartões selecionados"
                        >
                            <div class="mb-3">
                                Para cada cartão selecionado:
                            </div>
                            <div class="mb-1">
                                Se ele não possuir utilização, será removido
                                permanentemente.
                            </div>
                            <div>
                                Caso contrário, será removido apenas desta
                                listagem.
                            </div>
                        </x-organisms.confirm-rm-group-btn>
                    @endif
                </div>
                <x-organisms.filter-form-check
                    key="own"
                    :checked="request()->boolean('own')"
                    class="py-2 w-100"
                >
                    Somente meus cartões</x-organisms.filter-form-check
                >
            </div>
            <x-molecules.table-index
                :styleRows="[
                    'first' => 'width: 1.75em;',
                    'second' => 'width: 6.5em;',
                ]"
                :qtyBtns="$trashed ? 1 : 2"
            >
                <x-slot:cols>
                    <col class="col-remain-flag" />
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
                        <th scope="col">Foto</th>
                        <x-atoms.table-head
                            colRemain
                            sort="flag"
                        >
                            Bandeira
                        </x-atoms.table-head>
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
                    @forelse ($models($list) as $card)
                        <tr>
                            <td>
                                <input
                                    type="checkbox"
                                    value="{{ $card->id }}"
                                    class="form-check-input cursor-pointer multiselection-item"
                                    @disabled ($trashed ? !$hasAccess('restore', $card) : !$hasAccess('delete', $card))
                                />
                            </td>
                            <td>
                                <div class="rounded-circle card-img-box">
                                    <img
                                        src="{{ $card->img }}"
                                        alt="Foto do cartão"
                                        class="img-from-row"
                                    />
                                </div>
                            </td>
                            <td>
                                <a
                                    href="{{ route('payment-cards.show', ['card' => $card->id]) }}"
                                    class="d-block text-truncate text-decoration-none text-info"
                                    title="Visualizar dados do cartão"
                                    >{{$card->flag}}</a
                                >
                            </td>
                            <td>
                                {{ DatetimeFormatter::formatToDate($card->created_at) }}
                            </td>
                            <td>
                                <div
                                    class="w-100 d-flex justify-content-center gap-1"
                                >
                                    @if ($trashed)
                                        <x-organisms.confirm-restore-btn
                                            :routeParams="['card' => $card->id]"
                                            route="payment-cards.restore"
                                            heading="Restaurar este cartão?"
                                            positiveText="Restaurar cartão"
                                            title="Restaurar cartão"
                                            :disabled="!$hasAccess('restore', $card)"
                                        >
                                            Isso restaurará este cartão e todos
                                            seus dados relacionados.
                                        </x-organisms.confirm-restore-btn>
                                    @else
                                        <x-atoms.button
                                            format="anchor"
                                            class="btn-secondary"
                                            href="{{ route('payment-cards.edit', ['card' => $card->id]) }}"
                                            :disabled="!$hasAccess('edit', $card)"
                                        >
                                            <i class="bi bi-wrench"></i>
                                        </x-atoms.button>
                                        <x-organisms.confirm-rm-btn
                                            :routeParams="['card' => $card->id]"
                                            route="payment-cards.destroy"
                                            heading="Remover este cartão?"
                                            positiveText="Remover cartão"
                                            title="Remover cartão"
                                            :disabled="!$hasAccess('delete', $card)"
                                        >
                                            <div class="mb-1">
                                                Se ele não possuir utilização,
                                                será removido permanentemente.
                                            </div>
                                            <div>
                                                Caso contrário, será removido
                                                apenas desta listagem.
                                            </div>
                                        </x-organisms.confirm-rm-btn>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td
                                colspan="5"
                                class="no-values"
                            >
                                Sem cartões para o filtro atual
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
