@use ('App\Models\PaymentCard')
@use ('App\Facades\DateFormatter')
@push ('styling')
    @vite ([
        'resources/css/pages/generic/default.css',
        'resources/css/pages/generic/table.css',
        'resources/css/pages/generic/show.css',
    ])
@endpush

<x-layout title="Visualizar Cartão">
    <x-packs.header>
        <x-packs.page-heading-row>
            <x-slot:heading>
                Visualizar Cartão:
                <span class="text-primary ms-2">{{ $card->flag }}</span>
            </x-slot:heading>
            <div class="dropdown top-right-item">
                <x-atoms.button
                    class="btn-secondary dropdown-toggle"
                    data-bs-toggle="dropdown"
                    aria-expanded="false"
                >
                    <i class="bi bi-menu-button-wide"></i>
                </x-atoms.button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        <x-atoms.button
                            class="dropdown-item d-flex gap-2"
                            format="anchor"
                            href="{{ route('payment-cards.index') }}"
                            :disabled="!$hasAccess('viewAny', PaymentCard::class)"
                        >
                            <i class="bi bi-credit-card"></i>
                            <span>Cartões</span>
                        </x-atoms.button>
                    </li>
                </ul>
            </div>
        </x-packs.page-heading-row>
    </x-packs.header>
    <main class="bg-secondary-subtle create-main main-default">
        <section class="content bg-light d-flex flex-column row-gap-3">
            <fieldset
                class="border border-1 border-dark rounded-1 fieldset-tag"
            >
                <legend class="field-legend bg-light">Dados do Cartão</legend>
                <div class="data-box">
                    <div class="label">Imagem:</div>
                    <img
                        src="{{ $card->img }}"
                        alt="Foto do cartão"
                        class="rounded-circle img-displayed"
                    />
                    <div class="label">Bandeira:</div>
                    <div>{{ $card->flag }}</div>
                    <div class="label">Tipos de pagamentos:</div>
                    <ul>
                        @foreach ($card->pay_way_list as $payWay)
                            <li>{{ $payWay->toString() }}</li>
                        @endforeach
                    </ul>
                    <div class="label">Criação:</div>
                    <div>
                        {{ DateFormatter::formatToDate($card->created_at) }}
                    </div>
                    <div class="label">Nativo:</div>
                    <div>{{ $card ? 'Sim' : 'Não' }}</div>
                </div>
            </fieldset>
        </section>
    </main>
</x-layout>
