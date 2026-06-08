@use ('App\Libraries\Enums\StockExitTypeEnum')

<form
    data-show="{{ StockExitTypeEnum::EXCHANGE->value }}"
    class="create-form"
    action="{{ route('stocks.exits.store') }}"
    method="post"
>
    @csrf
    <input
        type="hidden"
        name="type"
        value="{{ StockExitTypeEnum::EXCHANGE->value }}"
    />
    <x-molecules.form-field
        name="person"
        type="text"
        label-text="Nome:"
        id="person-field"
        placeholder="Insira o nome da pessoa"
        {{-- required --}}
        value="{{ old('person', '') }}"
        size="auto"
    />
    @foreach ($products as $product)
        <x-packs.exit-entries
            :entries="$entries->get($product->id)"
            :product="$product"
            style="--extract-btn-top: 0"
        />
    @endforeach

    <x-atoms.submit-btn class="btn-primary create-btn">
        Salvar
    </x-atoms.submit-btn>
</form>
