@use ('App\Libraries\Enums\StockExitTypeEnum')
@use ('App\Models\Product')
@push ('styling')
    @vite ([
        'resources/css/pages/generic/default.css',
        'resources/css/pages/generic/create.css',
        'resources/css/pages/stocks/exit.css',
    ])
@endpush
@push ('ecmascript-bottom')
    @vite ([
        'resources/js/pages/generic/masks.ts',
        'resources/js/pages/stocks/exit.ts',
        'resources/js/pages/stocks/stock-qty-exit.ts',
    ])
@endpush

<x-layout title="Utilização de Estoque">
    <x-packs.header>
        <x-packs.page-heading-row>
            <x-slot:heading>
                <span class="me-1">Utilização de Estoque:</span>
                <span class="text-info">{{ $exitType->toString() }}</span>
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
                            class="dropdown-item d-flex gap-2"
                            format="anchor"
                            href="{{ route('stocks.index') }}"
                            :disabled="!$hasAccess('viewAny', Product::class)"
                        >
                            <i class="bi bi-boxes"></i>
                            <span>Estoques</span>
                        </x-atoms.button>
                    </li>
                </ul>
            </div>
        </x-packs.page-heading-row>
    </x-packs.header>
    <main class="bg-secondary-subtle create-main main-default">
        <section class="content bg-light">
            <x-molecules.select-field
                name="type"
                aria-hidden="true"
                :value="old('type', $exitType->value)"
                id="type"
                :disabled="true"
                class="d-none"
            >
                <option
                    @selected (true)
                    value="{{ $exitType->value }}"
                >
                    {{ $exitType->toString() }}
                </option>
            </x-molecules.select-field>
            @switch ($exitType)
                @case (StockExitTypeEnum::SALE)
                    @include ('pages.stocks.exits.shared.sale')
                    @break
                @case (StockExitTypeEnum::EXCHANGE)
                    @include ('pages.stocks.exits.shared.exchange')
                    @break
                @case (StockExitTypeEnum::DEMONSTRATION)
                    @include ('pages.stocks.exits.shared.remain', [
                        'exitType' => StockExitTypeEnum::DEMONSTRATION->value
                    ])
                    @break
                @case (StockExitTypeEnum::PERSONAL_USE)
                    @include ('pages.stocks.exits.shared.remain', [
                        'exitType' => StockExitTypeEnum::PERSONAL_USE->value
                    ])
                    @break
                @default
                    @include ('pages.stocks.exits.shared.remain', [
                        'exitType' => StockExitTypeEnum::LOSS->value
                    ])
            @endswitch
            @foreach ($products as $product)
                <form
                    id="extract-product-{{ $product->id }}"
                    action="{{
                        route('stocks.sales.unmark', ['product' => $product->id])
                    }}"
                    method="post"
                    class="form-remotion"
                >
                    @csrf
                </form>
            @endforeach
        </section>
    </main>
</x-layout>
