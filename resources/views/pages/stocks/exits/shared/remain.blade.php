<form
    data-show="{{ $exitType }}"
    class="create-form"
    action="{{ route('stocks.exits.store') }}"
    method="post"
>
    @csrf
    <input
        type="hidden"
        name="type"
        value="{{ $exitType }}"
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
