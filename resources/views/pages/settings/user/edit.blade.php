@push ('styling')
    @vite ([
        'resources/css/pages/generic/default.css',
        'resources/css/pages/settings/user/edit.css',
    ])
@endpush

<x-layout title="Configurações">
    <x-packs.header>
        <x-packs.page-heading-row>
            <x-slot:heading>
                <span>Edição do Usuário:</span>
                <a
                    href="{{ route('settings.user.show', ['user' => $user->id]) }}"
                    class="ms-2 text-decoration-none"
                    >{{ $user->name }}</a
                >
            </x-slot:heading>
        </x-packs.page-heading-row>
    </x-packs.header>
    <main class="bg-secondary-subtle main-default">
        <section class="content bg-light">
            <fieldset
                class="border border-1 border-dark rounded-1 fieldset-tag"
            >
                <legend class="field-legend bg-light">Conta</legend>
                <form
                    action="{{ route('settings.user.update', ['user' => $user->id]) }}"
                    method="post"
                    enctype="multipart/form-data"
                >
                    @csrf
                    @method ('PUT')
                    <div>
                        <x-molecules.form-field
                            class="grid-field"
                            name="photo"
                            type="file"
                            label-text="Foto:"
                            :value="old('photo', $user->photo)"
                        />
                        <x-molecules.form-field
                            class="grid-field"
                            name="name"
                            type="text"
                            label-text="Nome:"
                            placeholder="Insira o nome do usuário"
                            {{-- required --}}
                            :value="old('name', $user->name)"
                            autocomplete="no"
                        />
                        <x-molecules.form-field
                            class="grid-field"
                            label-text="Telefone:"
                            name="phone"
                            type="tel"
                            placeholder="Insira o telefone do usuário"
                            :value="old('phone', $user->phone)"
                            autocomplete="no"
                        />
                        <div class="d-flex w-100 justify-content-end mt-2">
                            <x-atoms.button
                                class="btn btn-primary"
                                type="submit"
                                title="Salvar"
                            >
                                Atualizar
                            </x-atoms.button>
                        </div>
                    </div>
                </form>
            </fieldset>
        </section>
    </main>
</x-layout>
