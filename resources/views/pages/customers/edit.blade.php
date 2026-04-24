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

<x-layout title="Editar Cliente">
    <x-packs.header>
        <x-packs.page-heading-row heading="Editar Cliente" />
    </x-packs.header>
    <main class="bg-secondary-subtle create-main main-default">
        <section class="content bg-light">
            <form
                method="post"
                class="create-form"
                action="{{ route('customers.update', ['customer' => $customer->id]) }}"
            >
                @csrf
                @method ('PUT')
                <x-molecules.form-field
                    name="name"
                    type="text"
                    label-text="Nome:"
                    id="name-field"
                    placeholder="Insira o nome do cliente"
                    required
                    value="{{ old('name', $customer->name) }}"
                    autocomplete="no"
                />
                <x-molecules.form-field
                    name="email"
                    type="email"
                    label-text="E-mail:"
                    id="email-field"
                    placeholder="Insira o e-mail do cliente"
                    required
                    value="{{ old('email', $customer->email ?? '') }}"
                    autocomplete="no"
                />
                <x-molecules.form-field
                    name="hostess"
                    type="text"
                    label-text="Anfitriã:"
                    id="hostess-field"
                    placeholder="Insira o nome da anfitriã"
                    value="{{ old('hostess', $customer->hostess ?? '') }}"
                    autocomplete="no"
                />
                <x-molecules.form-field
                    name="birthdate"
                    type="date"
                    label-text="Aniversário:"
                    id="birthdate-field"
                    placeholder="Insira o aniversário"
                    value="{{ old('birthdate', $customer->birthdate) }}"
                    autocomplete="no"
                />
                @foreach ($phones as $enumKey => $number)
                    <x-molecules.form-field
                        name="phone[{{ $enumKey }}]"
                        error-name="phone.{{ $enumKey }}"
                        type="tel"
                        label-text="Telefone {{ CustomerPhoneTypeEnum::tryFrom($enumKey)->toString() }}:"
                        id="phone-{{ $enumKey }}-field"
                        placeholder="(DDD) xxxxx xxxx"
                        value='{{ old("phone.{$enumKey}", $number) }}'
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
                                checked='{{ old("contact.{$enum->value}", $customer->contact_list->contains(fn($val) => $val === $enum)) }}'
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
                                checked='{{ old("period.{$enum->value}", $customer->schedule_list->contains(fn($val) => $val === $enum)) }}'
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
