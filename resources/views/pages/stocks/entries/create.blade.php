@use ('App\Models\Product')
@use ('App\Models\Discount')
@use ('App\Models\Supplier')
@push ('styling')
    @vite ([
        'resources/css/pages/generic/default.css',
        'resources/css/pages/generic/create.css'
    ])
@endpush
@push ('ecmascript-bottom')
    @vite ([
        'resources/js/pages/generic/datepicker.ts',
        'resources/js/pages/generic/masks.ts',
    ])
@endpush

<x-layout title="Cadastrar Estoque: {{ $product->name }}">
    <x-packs.header>
        <x-packs.page-heading-row>
            <x-slot:heading>
                Cadastrar Estoque:
                <a
                    class="ms-2 text-decoration-none"
                    href="{{ route('stocks.show', ['product' => $product->id]) }}"
                >
                    {{ $product->name }}
                </a>
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
            <x-molecules.block-error :keys="['product']" />
            <form
                class="create-form"
                action="{{ route('stocks.entries.store', ['product' => $product->id]) }}"
                method="post"
            >
                @csrf
                <x-molecules.form-field
                    name="cost"
                    label-text="Custo:"
                    placeholder="Insira o custo total"
                    required
                    value="{{ old('cost', 0) }}"
                    lang="pt"
                    size="auto"
                    :dtAttr="['mask' => 'float-positive']"
                />
                <x-molecules.number-form-field
                    name="qty"
                    label-text="Quantidade:"
                    placeholder="Insira a quantidade"
                    required
                    value="{{ old('qty', 1) }}"
                    size="auto"
                    step="1"
                    min="1"
                />
                <x-molecules.form-field
                    name="validity"
                    label-text="Validade:"
                    id="validity-field"
                    placeholder="Insira a validade"
                    value="{{ old('validity', '') }}"
                    :dtAttr="[
                        'dtpicker' => '',
                        'mindate' => now()->timestamp * 1000
                    ]"
                    size="auto"
                />
                @can('viewAny', Discount::class)
                    <x-molecules.select-field
                        label-text="Desconto"
                        placeholder="Nenhum"
                        name="discount"
                        size="auto"
                        :value="old('discount', '')"
                    >
                        @foreach ($discounts as $discount)
                            <option
                                @selected ($discount->id == old('discount', ''))
                                value="{{ $discount->id }}"
                                >{{ $parseDiscount($discount->type, $discount->value) }}
                            </option>
                        @endforeach
                    </x-molecules.select-field>
                @endcan
                @can('viewAny', Supplier::class)
                    <x-molecules.select-field
                        label-text="Fornecedor:"
                        name="supplier"
                        placeholder="Selecione..."
                        aria-label="Selecione o fornecedor"
                        required
                        size="auto"
                    >
                        @foreach ($suppliers as $supplier)
                            <option value="{{ $supplier->id }}">
                                {{ $supplier->name }}
                            </option>
                        @endforeach
                    </x-molecules.select-field>
                @endcan
                <x-atoms.submit-btn class="btn-primary create-btn">
                    Salvar
                </x-atoms.submit-btn>
            </form>
        </section>
    </main>
</x-layout>
