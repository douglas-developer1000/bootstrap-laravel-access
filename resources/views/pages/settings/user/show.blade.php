@push ('styling')
    @vite ([
        'resources/css/pages/generic/default.css',
        'resources/css/pages/generic/table.css',
    ])
@endpush

<x-layout title="Configurações">
    <x-packs.header>
        <x-packs.page-heading-row>
            <x-slot:heading>
                Dados do Usuário
            </x-slot:heading>
            <x-atoms.button
                class="btn-secondary top-right-item"
                format="anchor"
                href="{{ route('settings.user.edit', ['user' => $user->id]) }}"
                title="Editar dados do usuário"
            >
                <i class="bi bi-wrench"></i>
            </x-atoms.button>
        </x-packs.page-heading-row>
    </x-packs.header>
    <main class="bg-secondary-subtle main-default">
        <section class="content bg-light">
            <fieldset
                class="border border-1 border-dark rounded-1 fieldset-tag"
            >
                <legend class="field-legend bg-light">Conta</legend>
                <div class="w-100 d-flex justify-content-center mb-5">
                    @if ($user->photo)
                        <img
                            src="{{ $user->photo }}"
                            alt="foto do usuário"
                            style="width: 10rem"
                        />
                    @else
                        <i class="bi bi-person-circle px-2 fs-1"></i>
                    @endif
                </div>
                <table class="table tabular-data">
                    <tbody>
                        <tr>
                            <th scope="row">Nome</th>
                            <td>{{ $user->name }}</td>
                        </tr>
                        <tr>
                            <th scope="row">Email</th>
                            <td>{{ $user->email }}</td>
                        </tr>
                        <tr>
                            <th scope="row">Inicio</th>
                            <td>{{ $user->created_at_formatted }}</td>
                        </tr>
                        <tr>
                            <th scope="row">Telefone</th>
                            <td>{{ $user->phone ?? '---' }}</td>
                        </tr>
                    </tbody>
            </fieldset>
        </section>
    </main>
</x-layout>
