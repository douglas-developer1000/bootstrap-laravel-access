@use ('App\Libraries\Utils\DatetimeFormatter')
@push ('styling')
    @vite ([
        'resources/css/pages/generic/default.css',
        'resources/css/pages/generic/table.css',
        'resources/css/pages/generic/show.css',
        'resources/css/pages/suppliers/show.css',
    ])
@endpush

<x-layout title="Visualizar Fornecedor">
    <x-packs.header>
        <x-packs.page-heading-row>
            <x-slot:heading>
                Visualizar Fornecedor:
                <span class="text-primary ms-2">{{ $supplier->name }}</span>
            </x-slot:heading>
        </x-packs.page-heading-row>
    </x-packs.header>
    <main class="bg-secondary-subtle create-main main-default">
        <section class="content bg-light d-flex flex-column row-gap-3">
            <fieldset
                class="border border-1 border-dark rounded-1 fieldset-tag"
            >
                <legend class="field-legend bg-light">
                    Dados do fornecedor
                </legend>
                <div class="data-box">
                    <div class="label">Nome:</div>
                    <div>{{ $supplier->name }}</div>
                    <div class="label">Cnpj:</div>
                    <div>{{ $supplier->cnpj ?: '--' }}</div>
                    <div class="label">Imagem:</div>
                    @if ($supplier->img)
                        <img
                            src="{{ $supplier->img }}"
                            alt="Foto do fornecedor"
                            class="rounded-circle img-displayed"
                        />
                    @else
                        <div
                            class="rounded-circle img-displayed"
                            style="background-color: {{ $supplier->color }};"
                        ></div>
                    @endif
                    <div class="label">Criação:</div>
                    <div>
                        {{ DatetimeFormatter::formatToDate($supplier->created_at) }}
                    </div>
                </div>
            </fieldset>
            <fieldset
                class="border border-1 border-dark rounded-1 fieldset-tag px-3 pb-4"
            >
                <legend class="field-legend bg-light">Produtos</legend>
                @if ($products->isEmpty())
                    <div
                        class="border w-max px-2 rounded-pill border-secondary"
                    >
                        Sem produtos vinculados
                    </div>
                @else
                    <div class="products-data-box">
                        @foreach ($products as $product)
                            <div class="product-item">
                                <div>Produto:</div>
                                <div>{{ $product->prodName }}</div>
                                <div>Categoria:</div>
                                <div>{{ $product->prodCatName }}</div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </fieldset>
        </section>
    </main>
</x-layout>
