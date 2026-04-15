@push ('styling')
    @vite ('resources/css/pages/generic/password.css')
@endpush

<x-layout title="Esqueci minha senha">
    <main class="gate-main password-gate-main w-100 vh-100 p-0 d-flex">
        <x-packs.gate-back-img
            href="/images/forgot-password/back.webp"
            mobile-href="/images/forgot-password/backMobile.webp"
            img-alt="Foto de um peixe em um aquário"
        />
        <x-atoms.gate.container>
            <x-atoms.gate.logo
                href="/"
                class="logo"
                src="/images/forgot-password/logo.webp"
                alt="ícone do formulário da tela de esqueci minha senha"
            />
            <form
                method="post"
                action="{{ route('password.email') }}"
            >
                @csrf
                <x-atoms.gate.card>
                    <x-atoms.gate.heading>
                        Esqueceu sua senha?
                    </x-atoms.gate.heading>
                    @session ('error')
                        <x-atoms.error-msg class="mb-0">
                            {{ session('error') }}
                        </x-atoms.error-msg>
                    @endsession
                    <x-molecules.form-field
                        type="email"
                        id="email"
                        name="email"
                        placeholder="Insira seu email aqui"
                        label-text="Email:"
                        :value="old('email', '')"
                    />
                    <x-atoms.gate.btns-row>
                        <x-atoms.submit-btn class="btn-primary">
                            Solicitar
                        </x-atoms.submit-btn>
                    </x-atoms.gate.btns-row>
                </x-atoms.gate.card>
            </form>
        </x-atoms.gate.container>
        <x-packs.success-toast />
    </main>
</x-layout>
