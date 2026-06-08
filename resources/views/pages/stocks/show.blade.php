@php
    /**
     * @see \App\Http\Controllers\StockController::show
     * @see \App\Http\Controllers\StockController::showDeleted
     **/
@endphp

@use ('App\Models\StockEntry')
@use ('App\Models\StockExit')
@push ('styling')
    @vite ([
        'resources/css/pages/generic/default.css',
        'resources/css/pages/generic/table.css',
        'resources/css/pages/generic/show.css',
        'resources/css/pages/stocks/show.css'
    ])
@endpush

<x-layout title="Visualizar Estoque">
    <x-packs.header>
        <x-packs.page-heading-row>
            <x-slot:heading>
                Estoque do produto:
                <span
                    class="text-primary ms-2 user-select-none white-space-nowrap"
                    >{{ $product->name }}</span
                >
            </x-slot:heading>
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
                            class="dropdown-item"
                            format="anchor"
                            href="{{ route('stocks.entries.create', ['product' => $product->id]) }}"
                            title="Adicionar estoque"
                            :disabled="!$hasAccess('create', [StockEntry::class, $product])"
                        >
                            <i class="bi bi-plus-lg"></i>
                            <span>Estoque</span>
                        </x-atoms.button>
                    </li>
                </ul>
            </div>
        </x-packs.page-heading-row>
    </x-packs.header>
    <main class="bg-secondary-subtle create-main main-default">
        <section class="content bg-light d-flex flex-column row-gap-3">
            <fieldset
                class="border border-1 border-dark rounded-1 fieldset-tag"
            >
                <legend class="field-legend bg-light">Dados</legend>
                <div class="data-box">
                    <div class="label">Nome:</div>
                    <div>{{ $product->name }}</div>
                    <div class="label">Imagem:</div>
                    <div>
                        <br />
                        @if ($product->img === NULL)
                            <svg class="product-icon" xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="bi bi-box-seam" viewBox="0 0 16 16">
                                <path d="M8.186 1.113a.5.5 0 0 0-.372 0L1.846 3.5l2.404.961L10.404 2zm3.564 1.426L5.596 5 8 5.961 14.154 3.5zm3.25 1.7-6.5 2.6v7.922l6.5-2.6V4.24zM7.5 14.762V6.838L1 4.239v7.923zM7.443.184a1.5 1.5 0 0 1 1.114 0l7.129 2.852A.5.5 0 0 1 16 3.5v8.662a1 1 0 0 1-.629.928l-7.185 2.874a.5.5 0 0 1-.372 0L.63 13.09a1 1 0 0 1-.63-.928V3.5a.5.5 0 0 1 .314-.464z" />
                            </svg>
                        @else
                            <img
                                src="{{ $product->img }}"
                                alt="Imagem do produto"
                            />
                        @endif
                    </div>
                    <div class="label">Observações:</div>
                    <div>
                        <br />
                        <div>{{ $product->obs ?? 'N/A' }}</div>
                    </div>
                    <div class="label">Detalhes</div>
                    <div class="details">
                        <br />
                        @forelse (json_decode($product->details) as $detail)
                            <div class="key">{{ $detail->key }}</div>
                            <div class="value">{{ $detail->value }}</div>
                        @empty
                            <div>Sem detalhes</div>
                        @endforelse
                    </div>
                    <div class="label">Categorias</div>
                    <div class="categories">
                        <br />
                        @foreach ($categories as $cat)
                            <div>{{ $cat }}</div>
                        @endforeach
                    </div>
                </div>
            </fieldset>
            <fieldset
                class="border border-1 border-dark rounded-1 fieldset-tag mt-2"
            >
                <legend class="field-legend bg-light">Estoques</legend>
                <div class="py-3">
                    @if ($emptyStock)
                        <div class="text-center">Nenhum estoque disponível</div>
                    @else
                        @foreach ($entries as $entry)
                            <div class="stocks">
                                <div class="label">Qtd restante:</div>
                                <div>{{ $entry->qtyRemain }}</div>
                                <div class="label">Custo / item:</div>
                                <div>{{ $entry->cost }}</div>
                                <div class="label">Desconto:</div>
                                <div>{{ $entry->discountValue }}</div>
                                <div class="label">Validade:</div>
                                <div>{{ $entry->validity }}</div>
                                <div class="label">Cadastro:</div>
                                <div>{{ $entry->created_at }}</div>
                            </div>
                            <div class="supplier-box">
                                <div class="supplier-info">
                                    <div class="label supplier-label">
                                        Fornecedor:
                                    </div>
                                    <div class="supplier-name">
                                        {{ $entry->supplierName ?? '---' }}
                                    </div>
                                    @if ($entry->supplierImg)
                                        <img
                                            src="{{ $entry->supplierImg }}"
                                            alt="Imagem do fornecedor"
                                            class="img-displayed"
                                        />
                                    @else
                                        <div
                                            class="rounded-circle img-displayed supplier-color"
                                            style="background-color: {{ $entry->supplierColor ?? 'gray' }};"
                                        ></div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
            </fieldset>
        </section>
        <x-packs.toast />
    </main>
</x-layout>
