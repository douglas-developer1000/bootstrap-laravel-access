@use ('App\Models\Discount')
@use ('App\Libraries\Enums\DiscountTypeEnum')
@push ('styling')
    @vite ([
        'resources/css/pages/generic/default.css',
        'resources/css/pages/generic/create.css',
    ])
@endpush
@push ('ecmascript-bottom')
    @vite ('resources/js/pages/generic/masks.ts')
@endpush

<x-layout title="Editar Desconto">
    <x-packs.header>
        <x-packs.page-heading-row heading="Editar Desconto">
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
                            href="{{ route('discounts.index') }}"
                            :disabled="!$hasAccess('viewAny', Discount::class)"
                        >
                            <i class="bi bi-currency-dollar"></i>
                            <span>Descontos</span>
                        </x-atoms.button>
                    </li>
                </ul>
            </div>
        </x-packs.page-heading-row>
    </x-packs.header>
    <main class="bg-secondary-subtle create-main main-default">
        <section class="content bg-light">
            <x-molecules.block-error :keys="['id']" />
            <form
                class="create-form"
                action="{{ route('discounts.update', ['discount' => $discount->id]) }}"
                method="post"
            >
                @csrf
                @method ('PUT')
                <x-molecules.select-field
                    label-text="Tipo"
                    name="type"
                    placeholder="Selecione..."
                    aria-label="Selecione um tipo de desconto"
                    required
                    size="auto"
                    :value="old('type', $discount->type)"
                >
                    @foreach (DiscountTypeEnum::cases() as $type)
                        <option
                            @selected ($type->value == old('type', $discount->type))
                            value="{{ $type->value }}"
                        >
                            {{ $type->toString() }}
                        </option>
                    @endforeach
                </x-molecules.select-field>
                <x-molecules.form-field
                    name="value"
                    label-text="Valor:"
                    placeholder="Insira o valor do desconto"
                    required
                    value="{{ old('value', $discount->value) }}"
                    lang="pt"
                    size="auto"
                    :dtAttr="['mask' => 'float-positive']"
                />
                <x-atoms.submit-btn class="btn-primary create-btn">
                    Salvar
                </x-atoms.submit-btn>
            </form>
        </section>
    </main>
</x-layout>
