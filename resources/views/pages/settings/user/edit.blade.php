@push ('styling')
    @vite ([
        'resources/css/pages/generic/default.css',
        'resources/css/pages/generic/table.css',
        'resources/css/pages/settings/user/edit.css',
    ])
@endpush
@use ('App\Libraries\Utils\PhoneFormatter')

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
                    <table class="table tabular-data">
                        <tbody>
                            <tr>
                                <td
                                    class="no-values photo-field"
                                    colspan="2"
                                >
                                    <x-molecules.form-field
                                        name="photo"
                                        type="file"
                                        label-text="Foto:"
                                        :value="old('photo', $user->photo)"
                                        position="static"
                                    />
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    @php
                                        $idName = uniqid('el_');
                                    @endphp
                                    <label
                                        for="{{ $idName }}"
                                        class="form-label"
                                        >Nome</label
                                    >
                                </th>
                                <td>
                                    <x-molecules.form-field
                                        :id="$idName"
                                        name="name"
                                        type="text"
                                        placeholder="Insira o nome do usuário"
                                        required
                                        :value="old('name', $user->name)"
                                        autocomplete="no"
                                        position="static"
                                    />
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    @php
                                        $idPhone = uniqid('el_');
                                    @endphp
                                    <label
                                        for="{{ $idPhone }}"
                                        class="form-label"
                                        >Telefone</label
                                    >
                                </th>
                                <td>
                                    <x-molecules.form-field
                                        :id="$idPhone"
                                        name="phone"
                                        type="tel"
                                        placeholder="Insira o telefone do usuário"
                                        :value="old('phone', PhoneFormatter::toView($user->phone))"
                                        autocomplete="no"
                                        position="static"
                                    />
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="d-flex w-100 justify-content-end mt-2">
                        <x-atoms.button
                            class="btn btn-primary"
                            type="submit"
                            title="Salvar"
                        >
                            Atualizar
                        </x-atoms.button>
                    </div>
                </form>
            </fieldset>
        </section>
    </main>
</x-layout>
