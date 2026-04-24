@push ('styling')
    @vite ([
        'resources/css/pages/generic/default.css',
        'resources/css/pages/generic/create.css'
    ])
@endpush
@push ('ecmascript-bottom')
    @vite ([
        'resources/js/pages/customers/create-edit.ts'
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
        <section class="content bg-light">
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
                />
                <x-molecules.form-field
                    name="email"
                    type="email"
                    label-text="E-mail:"
                    id="email-field"
                    placeholder="Insira o e-mail do cliente"
                    required
                    value="{{ old('email', '') }}"
                    autocomplete="no"
                />
                <x-molecules.form-field
                    name="hostess"
                    type="text"
                    label-text="Anfitriã:"
                    id="hostess-field"
                    placeholder="Insira o nome da anfitriã"
                    value="{{ old('hostess', '') }}"
                    autocomplete="no"
                />
                <x-molecules.form-field
                    name="birthdate"
                    label-text="Aniversário:"
                    id="birthdate-field"
                    placeholder="Insira o aniversário"
                    value="{{ old('birthdate', '') }}"
                    autocomplete="no"
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
                    />
                @endforeach
                <label class="fs-075">Contato por:</label>
                <div class="d-flex align-items-center gap-3 position-relative">
                    @foreach (CustomerContactEnum::cases() as $enum)
                        <div class="cursor-pointer">
                            <x-molecules.input-check
                                class-label="fs-075"
                                name="contact[{{ $enum->value }}]"
                                error-name="contact.{{ $enum->value }}"
                                checked='{{ old("contact.{$enum->value}", false) }}'
                            >
                                {{ CustomerContactEnum::tryFrom($enum->value)->toString() }}
                            </x-molecules.input-check>
                        </div>
                    @endforeach
                </div>
                <label class="fs-075">Horário:</label>
                <div class="d-flex align-items-center gap-3 position-relative">
                    @foreach (DayPeriodsEnum::cases() as $enum)
                        <div class="cursor-pointer">
                            <x-molecules.input-check
                                class-label="fs-075"
                                name="period[{{ $enum->value }}]"
                                error-name="period.{{ $enum->value }}"
                                checked='{{ old("period.{$enum->value}", false) }}'
                            >
                                {{ DayPeriodsEnum::tryFrom($enum->value)->toString() }}
                            </x-molecules.input-check>
                        </div>
                    @endforeach
                </div>

                <x-atoms.submit-btn class="btn-primary create-btn">
                    Salvar
                </x-atoms.submit-btn>
            </form>
        </section>
    </main>
</x-layout>
