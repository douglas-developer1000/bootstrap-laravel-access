@use('App\Libraries\Enums\RoleNameEnum')
@push ('styling')
    @vite ([
        'resources/css/pages/generic/default.css',
        'resources/css/pages/settings/plans/index.css',
    ])
@endpush

<x-layout title="{{ $title }}">
    <x-packs.header>
        <x-packs.page-heading-row heading="{{ $title }}" />
    </x-packs.header>
    <main class="bg-secondary-subtle main-default">
        <section class="content bg-light" style="--max-width: 39.5em">
            @foreach ($plans as $plan)
                <article class="plan-item">
                    <div class="plan-name">{{ $plan->name }}</div>
                    <div class="plan-price">{{ $parsePrice($plan->price) }}</div>
                    <div class="features">
                        <div class="features-title">Recursos</div>
                        <ul class="list">
                            @foreach ($roles[$plan->slug]['core'] as $role)
                                <li class="list-term">
                                    {{ RoleNameEnum::from($role->name)->description() ?? '' }}
                                </li>
                            @endforeach
                        </ul>
                    </div>
                    <div class="additionals">
                        <div class="additionals-title">Adicionais</div>
                        <ul class="list">
                            @forelse ($roles[$plan->slug]['additionals'] as $role)
                                <li class="list-term">
                                    {{ RoleNameEnum::from($role->name)->description() ?? '' }}
                                </li>
                            @empty
                                <li class="list-term text-danger">
                                    Sem adicionais
                                </li>
                            @endforelse
                        </ul>
                    </div>
                    <div class="spacer"></div>
                    <x-atoms.button class="btn-primary">
                        Acesse aqui
                    </x-atoms.button>
                </article>
            @endforeach
        </section>
    </main>
</x-layout>