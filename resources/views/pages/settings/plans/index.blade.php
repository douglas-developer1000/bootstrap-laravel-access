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
                    @if ($activeLicense?->plan->slug === $plan->slug)
                        <div class="active-plan">Plano atual</div>
                    @endif
                    @if ($pendingLicense?->plan->slug === $plan->slug)
                        <div class="pending-plan">Aguardando</div>
                    @endif
                    <div class="features">
                        <div class="features-title">Recursos</div>
                        <ul class="list">
                            @foreach ($roles[$plan->slug]['core'] as $role)
                                <li class="list-item">
                                    {{ $role->summary }}
                                </li>
                            @endforeach
                        </ul>
                    </div>
                    <div class="additionals">
                        <div class="additionals-title">Adicionais</div>
                        <ul class="list">
                            @forelse ($roles[$plan->slug]['additionals'] as $role)
                                <li class="list-item">
                                    {{ $role->summary }}
                                </li>
                            @empty
                                <li class="list-item text-danger">
                                    Sem adicionais
                                </li>
                            @endforelse
                        </ul>
                    </div>
                    <div class="spacer"></div>
                    <x-atoms.button
                        class="btn-primary"
                        format="anchor"
                        href="{{
                            route('plans.view.show', ['plan' => $plan])
                        }}"
                    >
                        Acesse aqui
                    </x-atoms.button>
                </article>
            @endforeach
        </section>
    </main>
</x-layout>