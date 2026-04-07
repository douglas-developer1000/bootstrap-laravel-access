@push ('styling')
    @vite ('resources/css/pages/permissions/create.css')
@endpush

<x-layout title="Editar Permissão">
    <x-packs.header>
        <x-packs.page-heading-row heading="Editar Permissão" />
    </x-packs.header>
    <main class="bg-secondary-subtle permission-create-main">
        <section class="content bg-light">
            <form
                method="post"
                class="create-form"
                action="{{ route('permissions.update', $permission->id) }}"
            >
                @csrf
                @method ('PUT')
                <x-molecules.form-field
                    name="name"
                    type="text"
                    label-text="Nome:"
                    id="name-field"
                    placeholder="Insira o nome da permissão"
                    required
                    value="{{ old('name', $permission->name) }}"
                />
                <x-atoms.submit-btn class="btn-primary create-btn">
                    Salvar
                </x-atoms.submit-btn>
            </form>
        </section>
    </main>
</x-layout>
