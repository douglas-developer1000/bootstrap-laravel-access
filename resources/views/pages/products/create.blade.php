@push ('styling')
    @vite ([
        'resources/css/pages/generic/default.css',
        'resources/css/pages/generic/create.css'
    ])
@endpush

<x-layout title="Cadastrar Produto">
    <x-packs.header>
        <x-packs.page-heading-row heading="Cadastrar Produto" />
    </x-packs.header>
    <main class="bg-secondary-subtle create-main main-default">
        <section class="content bg-light">
            <form
                class="create-form"
                action="{{ route('products.store') }}"
                method="post"
                enctype="multipart/form-data"
            >
                @csrf
                <x-molecules.form-field
                    name="name"
                    type="text"
                    label-text="Nome:"
                    placeholder="Insira o nome do produto"
                    required
                    value="{{ old('name', '') }}"
                    size="auto"
                />
                <x-molecules.select-field
                    label-text="Categoria"
                    name="category"
                    placeholder="Selecione..."
                    aria-label="Selecione a categoria do produto"
                    required
                    size="auto"
                    :value="old('category', '')"
                >
                    @foreach ($categories as $cat)
                        <option
                            @selected ($cat->id == old('category', ''))
                            value="{{ $cat->id }}"
                        >
                            {{ $cat->name }}
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
                    :value="old('obs', '')"
                    rows="5"
                />
                <x-packs.add-details-field :details="old('details')" />
                <x-atoms.submit-btn class="btn-primary create-btn">
                    Cadastrar
                </x-atoms.submit-btn>
            </form>
        </section>
    </main>
</x-layout>
