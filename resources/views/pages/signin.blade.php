@push ('styling')
    @vite ('resources/css/pages/signin.css')
@endpush

<x-layout>
    <main class="gate-main signin-gate-main w-100 vh-100 p-0 d-flex">
        <x-packs.gate-back-img
            href="/images/signin/back.webp"
            mobile-href="/images/signin/backMobile.webp"
            img-alt="Foto de uma planta com foco em um passarinho"
        />
        <x-atoms.gate.container>
            <x-atoms.gate.logo
                href="/"
                class="logo"
                src="/images/signin/logo.png"
                alt="ícone do formulário da tela de autenticação"
            />
            <form method="post">
                @csrf
                <x-atoms.gate.card>
                    <div class="row w-100 generic-msg-box"></div>
                    <x-molecules.form-field
                        type="email"
                        id="email"
                        name="email"
                        placeholder="Insira seu email aqui"
                        label-text="Email:"
                    />
                    <x-molecules.form-field
                        type="password"
                        id="password"
                        name="password"
                        placeholder="Insira sua senha aqui"
                        label-text="Senha:"
                    />
                    <x-atoms.gate.btns-row>
                        <x-atoms.gate.submit-btn>
                            Autenticar
                        </x-atoms.gate.submit-btn>
                    </x-atoms.gate.btns-row>
                    <div
                        class="w-100 position-relative align-self-start fs-075"
                    >
                        <a
                            class="position-absolute text-decoration-none forgot-password"
                            href="/forgot-password"
                            >Esqueceu sua senha?</a
                        >
                    </div>
                </x-atoms.gate.card>
            </form>
        </x-atoms.gate.container>
    </main>
</x-layout>
