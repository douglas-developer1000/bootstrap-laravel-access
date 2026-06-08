@use ('App\Models\PaymentCard')
@use ('App\Libraries\Enums\CardPayWayEnum')
@push ('styling')
    @vite ([
        'resources/css/pages/generic/default.css',
        'resources/css/pages/generic/create.css',
    ])
@endpush
@push ('ecmascript-bottom')
    @vite ('resources/js/pages/generic/masks.ts')
@endpush

<x-layout title="Editar Cartão">
    <x-packs.header>
        <x-packs.page-heading-row heading="Editar Cartão">
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
        <section class="content bg-light">
            <x-molecules.block-error :keys="['native']" />
            <form
                class="create-form"
                action="{{ route('payment-cards.update', ['card' => $card->id]) }}"
                method="post"
                enctype="multipart/form-data"
            >
                @csrf
                @method ('PUT')
                <x-molecules.form-field
                    name="img"
                    type="file"
                    label-text="Foto:"
                    placeholder="Insira a foto do cartão"
                    value="{{ old('img', '') }}"
                />
                <x-molecules.form-field
                    name="flag"
                    type="text"
                    label-text="Bandeira:"
                    placeholder="Insira o nome da bandeira"
                    required
                    value="{{ old('flag', $card->flag) }}"
                    autocomplete="no"
                />
                <x-organisms.checks-enum-field
                    :enum="CardPayWayEnum::class"
                    key="pay_way"
                    label="Tipo de pagamento"
                    :defaults="$card->pay_way_list"
                />
                <input
                    type="hidden"
                    name="native"
                    value="{{ $card->native }}"
                />
                <x-atoms.submit-btn class="btn-primary create-btn">
                    Salvar
                </x-atoms.submit-btn>
            </form>
        </section>
    </main>
</x-layout>
