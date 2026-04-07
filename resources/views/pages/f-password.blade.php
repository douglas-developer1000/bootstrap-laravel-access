@push ('styling')
    @vite ('resources/css/pages/f-password.css')
@endpush

<x-layout title="Esqueci minha senha">
    <main class="gate-main f-password-gate-main w-100 vh-100 p-0 d-flex">
        <x-packs.gate-back-img
            href="/images/f-password/back.webp"
            mobile-href="/images/f-password/backMobile.webp"
            img-alt="Foto de um peixe em um aquário"
        />
        <x-atoms.gate.container>
            <x-atoms.gate.logo
                href="/"
                class="logo"
                src="/images/f-password/logo.webp"
                alt="ícone do formulário da tela de esqueci minha senha"
            />
            <form method="post">
                @csrf
                <x-atoms.gate.card>
                    <x-atoms.gate.heading>
                        Esqueceu sua senha?
                    </x-atoms.gate.heading>
                    <x-molecules.form-field
                        type="email"
                        id="email"
                        name="email"
                        placeholder="Insira seu email aqui"
                        label-text="Email:"
                    />
                    <x-atoms.gate.btns-row>
                        <x-atoms.submit-btn class="btn-primary">
                            Solicitar
                        </x-atoms.submit-btn>
                    </x-atoms.gate.btns-row>
                </x-atoms.gate.card>
            </form>
        </x-atoms.gate.container>
    </main>
</x-layout>
