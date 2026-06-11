<form
    class="create-form"
    action="{{ route('stocks.exits.store', ['exitType' => $exitType]) }}"
    method="post"
>
    @csrf
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
