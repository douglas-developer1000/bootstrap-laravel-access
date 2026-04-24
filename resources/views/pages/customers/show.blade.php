@push ('styling')
    @vite ([
        'resources/css/pages/generic/default.css',
        'resources/css/pages/generic/table.css',
        'resources/css/pages/customers/show.css',
    ])
@endpush

@use ('App\Libraries\Utils\PhoneFormatter')

<x-layout title="Visualizar Cliente">
    <x-packs.header>
        <x-packs.page-heading-row>
            <x-slot:heading>
                Visualizar Cliente:
                <span class="text-primary ms-2">{{ $customer->name }}</span>
            </x-slot:heading>
        </x-packs.page-heading-row>
    </x-packs.header>
    <main class="bg-secondary-subtle create-main main-default">
        <section class="content bg-light d-flex flex-column row-gap-3">
            <fieldset
                class="border border-1 border-dark rounded-1 fieldset-tag"
            >
                <legend class="field-legend bg-light">Dados</legend>
                <div class="data-box">
                    <div class="label">Nome:</div>
                    <div>{{ $customer->name }}</div>
                    <div class="label">E-mail:</div>
                    <div>{{ $customer->email ?? '--' }}</div>
                    <div class="label">Anfitriã:</div>
                    <div>{{ $customer->hostess ?? '--' }}</div>
                    <div class="label">Criação:</div>
                    <div>{{ $customer->created_at_formatted }}</div>
                    <div class="label">Aniversário:</div>
                    <div>{{ $birthday ?? '--' }}</div>
                    <div class="label">Telefones:</div>
                    <div>
                        @forelse ($phones as $phone)
                            <div class="d-flex gap-2">
                                <div>{{ $phone->type->toString() }}:</div>
                                <a
                                    class="text-decoration-none"
                                    href="tel:{{ $phone->number }}"
                                    target="_blank"
                                    >{{ PhoneFormatter::toView($phone->number) }}</a
                                >
                            </div>
                        @empty
                            <div>--</div>
                        @endforelse
                    </div>
                    <div class="label">Contato por:</div>
                    <div>
                        @forelse ($customer->contact_list as $contact)
                            <div>{{ $contact->toString() }}</div>
                        @empty
                            <div>--</div>
                        @endforelse
                    </div>
                    <div class="label">Horário:</div>
                    <div>
                        @forelse ($customer->schedule_list as $schedule)
                            <div>{{ $schedule->toString() }}</div>
                        @empty
                            <div>--</div>
                        @endforelse
                    </div>
                </div>
            </fieldset>
        </section>
        <x-packs.success-toast />
    </main>
</x-layout>
