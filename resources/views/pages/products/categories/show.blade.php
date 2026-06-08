@use ('App\Libraries\Utils\DatetimeFormatter')
@push ('styling')
    @vite ([
        'resources/css/pages/generic/default.css',
        'resources/css/pages/generic/table.css',
        'resources/css/pages/generic/show.css',
        'resources/css/pages/products/categories/show.css',
    ])
@endpush

<x-layout title="Categoria de Produto">
    <x-packs.header>
        <x-packs.page-heading-row>
            <x-slot:heading>
                Categoria de Produto:
                <span class="text-primary ms-2">{{ $cat->name }}</span>
            </x-slot:heading>
        </x-packs.page-heading-row>
    </x-packs.header>
    <main class="bg-secondary-subtle create-main main-default">
        <section class="content bg-light d-flex flex-column row-gap-3">
            <fieldset
                class="border border-1 border-dark rounded-1 fieldset-tag px-3 pb-4"
            >
                <legend class="field-legend bg-light">Dados</legend>
                <div class="data-box">
                    <div class="label">Nome:</div>
                    <div>{{ $cat->name }}</div>
                    <div class="label">Criação:</div>
                    <div>
                        {{ DatetimeFormatter::formatToDate($cat->created_at) }}
                    </div>
                </div>
            </fieldset>
            <fieldset
                class="border border-1 border-dark rounded-1 fieldset-tag px-3 pb-4"
            >
                <legend class="field-legend bg-light">
                    Categorias Superiores
                </legend>
                @if (count($supCats) === 1)
                    <div
                        class="border w-max px-2 rounded-pill border-secondary"
                    >
                        Nenhuma
                    </div>
                @else
                    <div class="sup-cat-box">
                        @foreach ($supCats as $catName)
                            @if ($loop->first)
                                <div>{{ $catName }}</div>
                            @else
                                <div class="ms-3">&uarr;</div>
                                @if ($catName === $cat->name)
                                    <div
                                        class="border align-self-start px-2 rounded-pill border-secondary"
                                    >
                                        Categoria atual
                                    </div>
                                @else
                                    <div>{{ $catName }}</div>
                                @endif
                            @endif
                        @endforeach
                    </div>
                @endif
            </fieldset>
            <fieldset
                class="border border-1 border-dark rounded-1 fieldset-tag px-3 pb-4 sub-categories"
            >
                <legend class="field-legend bg-light">Sub-Categorias</legend>
                @if ($subCats->isEmpty())
                    <div
                        class="border w-max px-2 rounded-pill border-secondary"
                    >
                        Nenhuma
                    </div>
                @else
                    @include ('partials.products.categories.sub-categories', [
                        'categories' => $subCats
                    ])
                @endif
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
                            <div class="product-item">{{ $product->name }}</div>
                        @endforeach
                    </div>
                @endif
            </fieldset>
        </section>
        <x-packs.toast />
    </main>
</x-layout>
