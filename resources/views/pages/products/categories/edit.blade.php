@push ('styling')
    @vite ([
        'resources/css/pages/generic/default.css',
        'resources/css/pages/generic/create.css'
    ])
@endpush

<x-layout title="Editar Categoria de Produto">
    <x-packs.header>
        <x-packs.page-heading-row heading="Editar Categoria de Produto" />
    </x-packs.header>
    <main class="bg-secondary-subtle create-main main-default">
        <section class="content bg-light">
            <form
                method="post"
                class="create-form"
                action="{{ route('product-categories.update', ['category' => $category->id]) }}"
            >
                @csrf
                @method ('PUT')
                <x-molecules.form-field
                    name="name"
                    type="text"
                    label-text="Nome:"
                    id="name-field"
                    placeholder="Insira o nome da categoria"
                    required
                    value="{{ old('name', $category->name) }}"
                />
                <x-molecules.select-field
                    label-text="Super categoria"
                    placeholder="Nenhuma"
                    name="inheritance"
                    size="auto"
                    :value="old('inheritance', $category->parent_id)"
                >
                    @foreach ($categories as $cat)
                        <option
                            @selected ($cat->id == old('inheritance', $category->parent_id))
                            value="{{ $cat->id }}"
                            >{{ $cat->name }}
                        </option>
                    @endforeach
                </x-molecules.select-field>
                <x-atoms.submit-btn class="btn-primary create-btn">
                    Cadastrar
                </x-atoms.submit-btn>
            </form>
        </section>
    </main>
</x-layout>
