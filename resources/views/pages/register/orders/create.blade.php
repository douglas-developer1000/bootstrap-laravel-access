@push ('styling')
    @vite ('resources/css/pages/register-order.css')
    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css"
    />
@endpush

<x-layout title="Solicitação de registro">
    <main class="gate-main register-order-gate-main w-100 vh-100 p-0 d-flex">
        <x-packs.gate-back-img
            href="/images/register-order/back.webp"
            mobile-href="/images/register-order/backMobile.webp"
            img-alt="Foto de um gato deitado em uma casa"
        />
        <x-atoms.gate.container>
            <x-atoms.gate.logo
                href="/"
                class="logo"
                src="/images/register-order/logo.webp"
                alt="ícone do formulário da tela de solicitação de registro"
            />
            <form
                action="{{ route('register.orders.store') }}"
                method="post"
            >
                @csrf
                <x-atoms.gate.card>
                    <x-atoms.gate.heading>
                        Solicite sua nova conta
                    </x-atoms.gate.heading>
                    <x-molecules.form-field
                        type="email"
                        id="email"
                        name="email"
                        placeholder="Insira seu email aqui"
                        label-text="Email:"
                        :value="old('email', '')"
                    />
                    <x-molecules.form-field
                        type="tel"
                        id="phone"
                        name="phone"
                        placeholder="(xx) xxxx-xxxx"
                        label-text="Telefone:"
                        :value="old('phone', '')"
                    />
                    <x-atoms.gate.btns-row>
                        <x-atoms.submit-btn class="btn-primary">
                            Solicitar
                        </x-atoms.submit-btn>
                    </x-atoms.gate.btns-row>
                </x-atoms.gate.card>
            </form>
            <x-packs.toast delay="3000" />
        </x-atoms.gate.container>
    </main>
</x-layout>
