@push ('styling')
    @vite ([
        'resources/css/pages/generic/default.css',
        'resources/css/pages/generic/create.css'
    ])
@endpush

<x-layout title="Editar Produto">
    <x-packs.header>
        <x-packs.page-heading-row heading="Editar Produto" />
    </x-packs.header>
    <main class="bg-secondary-subtle create-main main-default">
        <section class="content bg-light">
            <form
                class="create-form"
                action="{{ route('products.update', $product->id) }}"
                method="post"
                enctype="multipart/form-data"
            >
                @csrf
                @method ('PUT')
                <x-molecules.form-field
                    name="name"
                    type="text"
                    label-text="Nome:"
                    placeholder="Insira o nome do produto"
                    required
                    value="{{ old('name', $product->name) }}"
                    size="auto"
                />
                <x-molecules.select-field
                    label-text="Categoria"
                    name="category"
                    placeholder="Selecione..."
                    aria-label="Selecione a categoria do produto"
                    required
                    size="auto"
                    :value="old('category', $product->product_category_id)"
                >
                    @foreach ($categories as $cat)
                        <option
                            @selected ($cat->id == old('category', $product->product_category_id))
                            value="{{ $cat->id }}"
                            >{{ $cat->name }}
                        </option>
                    @endforeach
                </x-molecules.select-field>
                <x-molecules.form-field
                    name="img"
                    type="file"
                    label-text="Foto (opcional):"
                    placeholder="Insira a foto do produto"
                    value="{{ old('img', '') }}"
                    size="auto"
                />
                <x-molecules.textarea-field
                    name="obs"
                    labelText="Observação (opcional)"
                    placeholder="Digite observações sobre o produto"
                    :value="old('obs', $product->obs)"
                    rows="5"
                />
                <x-packs.add-details-field
                    :details="old('details', $product->details)"
                />
                <x-atoms.submit-btn class="btn-primary create-btn">
                    Salvar
                </x-atoms.submit-btn>
            </form>
        </section>
    </main>
</x-layout>
