@use ('App\Models\Sale')
@use ('App\Libraries\Enums\PaymentTypeEnum')
@use ('App\Facades\DateFormatter')
@use ('App\Libraries\Enums\DiscountTypeEnum')
@use ('App\Libraries\Enums\CardPayWayEnum')
@push ('styling')
    @vite ([
        'resources/css/pages/generic/default.css',
        'resources/css/pages/generic/table.css',
        'resources/css/pages/generic/show.css',
    ])
@endpush

<x-layout title="Visualizar Venda">
    <x-packs.header>
        <x-packs.page-heading-row heading="Visualizar Venda">
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
                            href="{{ route('stocks.index') }}"
                            :disabled="!$hasAccess('viewAny', Sale::class)"
                        >
                            <i class="bi bi-boxes"></i>
                            <span>Estoques</span>
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
                <legend class="field-legend bg-light">Dados da venda</legend>
                <div class="data-box">
                    <div class="label">Cliente:</div>
                    <div>{{ $sale->customer->name }}</div>
                    <div class="label">Produtos:</div>
                    <div></div>
                    <div></div>
                    <div>
                        @foreach ($exits as $exit)
                            <table class="table table-hover table-striped w-75">
                                <thead>
                                    <tr>
                                        <th
                                            scope="col"
                                            colspan="2"
                                            class="text-center fw-medium text-info-emphasis"
                                        >
                                            {{ $exit->stockEntry->product->name }}
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <th
                                            class="text-end w-50 fw-medium"
                                            scope="row"
                                        >
                                            Quantidade:
                                        </th>
                                        <td class="text-start w-50">
                                            {{ $exit->qty }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th
                                            class="text-end w-50 fw-medium"
                                            scope="row"
                                        >
                                            Fornecedor:
                                        </th>
                                        <td>
                                            {{ $exit->stockEntry->supplier->name }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            {{-- <div class="label">Nome:</div> --}}
                            {{-- <div>{{ $exit->stockEntry->product->name }}</div> --}}
                        @endforeach
                    </div>
                    <div class="label">Pagamentos:</div>
                    <div></div>
                    <div></div>
                    <div>
                        @foreach ($payments as $payment)
                            <table class="table table-hover table-striped w-75">
                                <tbody>
                                    <tr>
                                        <th
                                            class="text-end w-50 fw-medium"
                                            scope="row"
                                        >
                                            Tipo:
                                        </th>
                                        <td class="text-start w-50">
                                            {{ $payment->type->toString() }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th
                                            class="text-end w-50 fw-medium"
                                            scope="row"
                                        >
                                            Valor:
                                        </th>
                                        <td class="text-start w-50">
                                            {{ $parsePrice($payment->value) }}
                                        </td>
                                    </tr>
                                    @if ($payment->type === PaymentTypeEnum::CARD)
                                        @foreach ($payment->paymentCards as $paymentCard)
                                            <tr>
                                                <th
                                                    class="text-end w-50 fw-medium"
                                                    scope="row"
                                                >
                                                    Bandeira:
                                                </th>
                                                <td class="text-start w-50">
                                                    {{ $paymentCard->flag }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <th
                                                    class="text-end w-50 fw-medium"
                                                    scope="row"
                                                >
                                                    Modalidade:
                                                </th>
                                                <td class="text-start w-50">
                                                    {{
                                                        $paymentCard->pivot->pay_way->toString()
                                                    }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <th
                                                    class="text-end w-50 fw-medium"
                                                    scope="row"
                                                >
                                                    Desconto:
                                                </th>
                                                <td class="text-start w-50">
                                                    @if ($paymentCard->paymentPaymentCard->isNotEmpty())
                                                        @php
                                                            $fee = $paymentCard->paymentPaymentCard->first()->fee;
                                                        @endphp
                                                        {{ $parseDiscount($fee->type, $fee->value) }}
                                                    @else
                                                        Não
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endif
                                </tbody>
                            </table>
                        @endforeach
                    </div>
                    <div class="label">Data da venda:</div>
                    <div>
                        {{ DateFormatter::formatToDate($sale->created_at) }}
                    </div>
                    <div class="label">Desconto na venda:</div>
                    @if ($discount)
                        <div></div>
                        <div></div>
                        <div
                            class="d-grid"
                            style="
                                grid-template-columns: repeat(2, max-content);
                                column-gap: 0.5em;
                            "
                        >
                            <div class="label">Tipo:</div>
                            <div>
                                {{ DiscountTypeEnum::from($discount->type)->toString() }}
                            </div>
                            <div class="label">Valor:</div>
                            <div>
                                {{ $parseDiscount($discount->type, $discount->value) }}
                            </div>
                        </div>
                    @else
                        <div>N/A</div>
                    @endif
                </div>
            </fieldset>
        </section>
    </main>
</x-layout>
