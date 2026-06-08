@push ('styling')
    @vite ([
        'resources/css/pages/generic/default.css',
        'resources/css/pages/generic/create.css'
    ])
@endpush

<x-layout title="Cadastrar Categoria de Produto">
    <x-packs.header>
        <x-packs.page-heading-row heading="Cadastrar Categoria de Produto" />
    </x-packs.header>
    <main class="bg-secondary-subtle create-main main-default">
        <section class="content bg-light">
            @session ('emptyCategories')
                <div
                    @class ([
                        'p-3',
                        'text-info-emphasis',
                        'bg-info-subtle',
                        'border',
                        'border-info-subtle',
                        'rounded-3',
                        'd-flex',
                        'flex-column',
                        'mb-3'
                    ])
                >
                    <span
                        >Parece que não temos nenhuma categoria de produto
                        existente ainda.</span
                    >
                    <span
                        >Por favor, crie a primeria categoria de produto a ser
                        utilizada.</span
                    >
                </div>
            @endsession
            <form
                method="post"
                class="create-form"
                action="{{ route('product-categories.store') }}"
            >
                @csrf
                <x-molecules.form-field
                    name="name"
                    type="text"
                    label-text="Nome:"
                    id="name-field"
                    placeholder="Insira o nome da categoria"
                    required
                    value="{{ old('name', '') }}"
                />
                <x-molecules.select-field
                    label-text="Super categoria"
                    placeholder="Nenhuma"
                    name="inheritance"
                    size="auto"
                    :value="old('inheritance', '')"
                >
                    @foreach ($categories as $cat)
                        <option
                            @selected ($cat->id == old('inheritance', ''))
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
