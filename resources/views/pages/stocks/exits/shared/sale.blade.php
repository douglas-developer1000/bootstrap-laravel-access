@use ('App\Libraries\Enums\StockExitTypeEnum')
@use ('App\Models\Customer')
@use ('App\Models\PaymentCard')
@use ('App\Models\Discount')

@push ('styling')
    @vite ([
        'resources/css/pages/stocks/exit-cards.css',
    ])
@endpush
@push ('ecmascript-bottom')
    @vite ([
        'resources/js/pages/stocks/sale-cards-exit.ts',
    ])
@endpush

<form
    class="create-form"
    action="{{
        route('stocks.exits.store', [
            'exitType' => StockExitTypeEnum::SALE->value
        ])
    }}"
    method="post"
>
    @csrf
    @can('viewAny', Customer::class)
        <x-molecules.select-field
            label-text="Cliente:"
            name="customer"
            aria-label="Selecione um cliente"
            required
            size="auto"
            :value="old('customer', $customer->id)"
            readonly
        >
            <option
                @selected (true)
                value="{{ $customer->id }}"
            >
                {{ $customer->name }}
            </option>
        </x-molecules.select-field>
    @endcan
    <x-molecules.select-field
        label-text="Pagamento"
        placeholder="Selecione..."
        aria-label="Selecione um tipo de pagamento"
        name="payment-type"
        size="auto"
        required
        :value="old('payment-type', '')"
        :autofocus="true"
    >
        @foreach ($payTypes as $payType)
            <option
                @selected ($payType->value == old('payment-type', ''))
                value="{{ $payType->value }}"
            >
                {{ $payType->toString() }}
            </option>
        @endforeach
    </x-molecules.select-field>

    @can('viewAny', PaymentCard::class)
        <x-molecules.select-field
            class="card-comboboxes cards"
            label-text="Cartão"
            placeholder="Selecione..."
            aria-label="Selecione o cartão utilizado"
            name="card"
            size="auto"
            :value="old('card', '')"
        >
            @foreach ($cards as $card)
                <option
                    @selected ($card->id == old('card', ''))
                    value="{{ $card->id }}"
                    data-pay-ways="{{
                        $card->pay_way_list->map(
                            fn($enum) => $enum->value
                        )->implode('+')
                    }}"
                >
                    {{ $card->flag }}
                </option>
            @endforeach
            <x-slot:bottom>
                <div class="pay-ways"></div>
            </x-slot:bottom>
        </x-molecules.select-field>
    @endcan
    @if ($hasAccess('viewAny', PaymentCard::class) && $hasAccess('viewAny', Discount::class))
        <x-molecules.select-field
            class="card-comboboxes"
            label-text="Taxa do cartão:"
            placeholder="Nenhum"
            name="card_fee"
            size="auto"
            :value="old('card_fee', '')"
        >
            @foreach ($discounts as $discount)
                <option
                    @selected ($discount->id == old('card_fee', ''))
                    value="{{ $discount->id }}"
                    >{{ $discount->type->parseViewValue($discount->value) }}
                </option>
            @endforeach
        </x-molecules.select-field>
    @endif

    @can('viewAny', Discount::class)
        <x-molecules.select-field
            label-text="Desconto de venda:"
            placeholder="Nenhum"
            name="discount"
            size="auto"
            :value="old('discount', '')"
        >
            @foreach ($discounts as $discount)
                <option
                    @selected ($discount->id == old('discount', ''))
                    value="{{ $discount->id }}"
                    >{{ $discount->type->parseViewValue($discount->value) }}
                </option>
            @endforeach
        </x-molecules.select-field>
    @endcan

    @error ('prices')
        <x-molecules.block-error :keys="['prices', 'entries']" />
    @enderror
    @foreach ($products as $product)
        <x-packs.exit-entries
            :entries="$entries->get($product->id)"
            :product="$product"
        >
            <x-slot:pre-content>
                <x-molecules.form-field
                    class="py-1"
                    name="prices[{{ $product->id }}]"
                    errorName="prices.{{ $product->id }}"
                    label-text="Preço por item:"
                    placeholder="Insira o custo total"
                    required
                    :value='old("prices.{$product->id}", 0)'
                    lang="pt"
                    size="auto"
                    :dtAttr="['mask' => 'float-positive']"
                />
            </x-slot:pre-content>
        </x-packs.exit-entries>
    @endforeach

    <x-atoms.submit-btn class="btn-primary create-btn">
        Salvar
    </x-atoms.submit-btn>
</form>
