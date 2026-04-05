@push ('styling')
    @vite ('resources/css/pages/r-request.css')
@endpush

<x-layout title="Solicitação de registro">
    <main class="gate-main r-request-gate-main w-100 vh-100 p-0 d-flex">
        <x-packs.gate-back-img
            href="/images/r-request/back.webp"
            mobile-href="/images/r-request/backMobile.webp"
            img-alt="Foto de um gato deitado em uma casa"
        />
        <x-atoms.gate.container>
            <x-atoms.gate.logo
                href="/"
                class="logo"
                src="/images/r-request/logo.webp"
                alt="ícone do formulário da tela de solicitação de registro"
            />
            <form method="post">
                @csrf
                <x-atoms.gate.card>
                    <x-atoms.gate.heading>
                        Solicite sua nova conta
                    </x-atoms.gate.heading>
                    {{-- <div class="row w-100 generic-msg-box"></div> --}}
                    <x-molecules.form-field
                        type="email"
                        id="email"
                        name="email"
                        placeholder="Insira seu email aqui"
                        label-text="Email:"
                    />
                    <x-molecules.form-field
                        type="tel"
                        id="phone"
                        name="phone"
                        placeholder="(xx) xxxx-xxxx"
                        label-text="Telefone:"
                    />
                    <x-atoms.gate.btns-row>
                        <x-atoms.gate.submit-btn>
                            Solicitar
                        </x-atoms.gate.submit-btn>
                    </x-atoms.gate.btns-row>
                </x-atoms.gate.card>
            </form>
        </x-atoms.gate.container>
    </main>
</x-layout>
