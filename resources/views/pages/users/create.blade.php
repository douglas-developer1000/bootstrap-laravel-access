@push ('styling')
    @vite ([
        'resources/css/pages/generic/default.css',
        'resources/css/pages/generic/create.css'
    ])
@endpush

<x-layout title="Criar Usuários">
    <x-packs.header>
        <x-packs.page-heading-row heading="Criar Usuários" />
    </x-packs.header>
    <main class="bg-secondary-subtle create-main main-default">
        <section class="content bg-light">
            <form
                method="post"
                class="create-form"
                action="{{ route('users.store') }}"
            >
                @csrf
                <x-molecules.form-field
                    name="name"
                    type="text"
                    label-text="Nome:"
                    placeholder="Insira o nome do usuário"
                    required
                    value="{{ old('name', '') }}"
                />
                <x-molecules.form-field
                    name="email"
                    type="email"
                    label-text="E-mail:"
                    placeholder="Insira o e-mail do usuário"
                    required
                    value="{{ old('email', '') }}"
                    autocomplete="no"
                />
                <x-molecules.form-field
                    name="password"
                    type="password"
                    label-text="Senha:"
                    placeholder="Insira a senha do usuário"
                    required
                    value="{{ old('password', '') }}"
                    autocomplete="no"
                />
                <x-atoms.submit-btn class="btn-primary create-btn">
                    Criar
                </x-atoms.submit-btn>
            </form>
        </section>
        <x-packs.toast />
    </main>
</x-layout>
