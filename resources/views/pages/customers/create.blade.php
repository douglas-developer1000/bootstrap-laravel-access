@push ('styling')
    @vite ([
        'resources/css/pages/generic/default.css',
        'resources/css/pages/generic/create.css'
    ])
@endpush
@push ('ecmascript-bottom')
    @vite ([
        'resources/js/pages/generic/masks.ts',
        'resources/js/pages/generic/datepicker.ts',

    ])
@endpush

@use ('App\Libraries\Enums\CustomerPhoneTypeEnum')
@use ('App\Libraries\Enums\CustomerContactEnum')
@use ('App\Libraries\Enums\DayPeriodsEnum')

<x-layout title="Criar Cliente">
    <x-packs.header>
        <x-packs.page-heading-row heading="Criar Cliente" />
    </x-packs.header>
    <main class="bg-secondary-subtle create-main main-default">
        <section
            class="content bg-light"
            style="--max-width: 30em"
        >
            <form
                method="post"
                class="create-form"
                action="{{ route('customers.store') }}"
            >
                @csrf
                <x-molecules.form-field
                    name="name"
                    type="text"
                    label-text="Nome:"
                    id="name-field"
                    placeholder="Insira o nome do cliente"
                    required
                    value="{{ old('name', '') }}"
                    autocomplete="no"
                    size="auto"
                />
                <x-molecules.form-field
                    name="email"
                    type="email"
                    label-text="E-mail:"
                    id="email-field"
                    placeholder="Insira o e-mail do cliente"
                    value="{{ old('email', '') }}"
                    autocomplete="no"
                    size="auto"
                />
                <x-molecules.form-field
                    name="hostess"
                    type="text"
                    label-text="Anfitriã:"
                    id="hostess-field"
                    placeholder="Insira o nome da anfitriã"
                    value="{{ old('hostess', '') }}"
                    autocomplete="no"
                    size="auto"
                />
                <x-molecules.form-field
                    name="birthdate"
                    label-text="Aniversário:"
                    id="birthdate-field"
                    placeholder="Insira o aniversário"
                    value="{{ old('birthdate', '') }}"
                    autocomplete="no"
                    :dtAttr="['dtpicker' => '']"
                    size="auto"
                />
                @foreach (CustomerPhoneTypeEnum::casesExcept(CustomerPhoneTypeEnum::OTHER) as $enum)
                    <x-molecules.form-field
                        name="phone[{{ $enum->value }}]"
                        error-name="phone.{{ $enum->value }}"
                        type="tel"
                        label-text="Telefone {{ CustomerPhoneTypeEnum::tryFrom($enum->value)->toString() }}:"
                        id="phone-{{ $enum->value }}-field"
                        placeholder="(DDD) xxxxx xxxx"
                        value="{{ old('phone.' . $enum->value, '') }}"
                        :dtAttr="['mask' => 'phone']"
                        size="auto"
                    />
                @endforeach
                <x-organisms.checks-enum-field
                    :enum="CustomerContactEnum::class"
                    key="contact"
                    label="Contato por"
                />
                <x-organisms.checks-enum-field
                    :enum="DayPeriodsEnum::class"
                    key="period"
                    label="Horário"
                />
                <x-atoms.submit-btn class="btn-primary create-btn">
                    Salvar
                </x-atoms.submit-btn>
            </form>
        </section>
    </main>
</x-layout>
