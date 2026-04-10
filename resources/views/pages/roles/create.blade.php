@push ('styling')
    @vite ([
        'resources/css/pages/generic/default.css',
        'resources/css/pages/generic/create.css'
    ])
@endpush

<x-layout title="Criar Papeis">
    <x-packs.header>
        <x-packs.page-heading-row heading="Criar Papeis" />
    </x-packs.header>
    <main class="bg-secondary-subtle create-main main-default">
        <section class="content bg-light">
            <form
                method="post"
                class="create-form"
                action="{{ route('roles.store') }}"
            >
                @csrf
                <x-molecules.form-field
                    name="name"
                    type="text"
                    label-text="Nome:"
                    id="name-field"
                    placeholder="Insira o nome do papel"
                    required
                    value="{{ old('name', '') }}"
                />
                <x-atoms.submit-btn class="btn-primary create-btn">
                    Salvar
                </x-atoms.submit-btn>
            </form>
        </section>
    </main>
</x-layout>
