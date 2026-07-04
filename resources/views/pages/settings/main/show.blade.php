@use ('App\Models\User')
@push ('styling')
    @vite ([
        'resources/css/pages/generic/default.css',
    ])
@endpush

<x-layout title="Configuração global">
    <x-packs.header>
        <x-packs.page-heading-row heading="Configurações" />
    </x-packs.header>
    <main class="bg-secondary-subtle main-default">
        <section class="content bg-light">
            <div class="card">
                <ul class="list-group list-group-flush settings-menu">
                    @can('show', User::class)
                        <li class="list-group-item">
                            <a
                                class="d-flex text-truncate text-decoration-none text-dark cursor-pointer justify-content-between"
                                href="{{ route('settings.user.show') }}"
                            >
                                <div class="d-flex gap-2">
                                    <i class="bi bi-person-fill"></i>
                                    <span>Usuário</span>
                                </div>
                                <i class="bi bi-chevron-right"></i>
                            </a>
                        </li>
                    @endcan
                    <li class="list-group-item">
                        <a href="{{ route('plans.view.index') }}" class="d-flex text-truncate text-decoration-none text-dark cursor-pointer justify-content-between">
                            <div class="d-flex gap-2">
                                <i class="bi bi-pass-fill"></i>
                                <span>Planos</span>
                            </div>
                            <i class="bi bi-chevron-right"></i>
                        </a>
                    </li>
                </ul>
            </div>
        </section>
    </main>
</x-layout>
