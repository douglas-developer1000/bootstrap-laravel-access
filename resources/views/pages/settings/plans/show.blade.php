@push ('styling')
    @vite ([
        'resources/css/pages/generic/default.css',
        'resources/css/pages/generic/show.css',
        'resources/css/pages/settings/plans/show.css',
    ])
@endpush

<x-layout title="{{ $title }}">
    <x-packs.header>
        <x-packs.page-heading-row heading="{{ $title }}" />
    </x-packs.header>
    <main class="bg-secondary-subtle main-default">
        <section class="content bg-light">
            <fieldset
                class="border border-1 border-dark rounded-1 fieldset-tag"
            >
                <legend class="field-legend bg-light text-info-emphasis">{{ $plan->name }}</legend>
                <div class="text-center">{{ $plan->description }}</div>
                <div class="payment">
                    <div class="item left">
                        Pagamento
                    </div>
                    <div class="item right">
                        {{ $plan->billing_period->toString() }}
                    </div>
                    <div class="item price text-success border-dark">{{ $parsePrice($plan->price) }}</div>
                </div>
                <div>
                    <div class="fs-5 fw-medium ps-3 my-2">Você tem direito a:</div>
                    <ul class="list-group">
                        @foreach ($roleDescriptions['core'] as $description)
                            <li class="list-group-item">
                                {{ $description }}
                            </li>
                        @endforeach
                    </ul>
                </div>
                @if ($roleDescriptions['additional']->isNotEmpty())
                    <div>
                        <div class="fs-5 fw-medium ps-3 mt-4 mb-2">Opcionalmente você pode:</div>
                        <ul class="list-group">
                            @foreach ($roleDescriptions['additional'] as $description)
                                <li class="list-group-item">
                                    {{ $description }}
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </fieldset>
        </section>
    </main>
</x-layout>