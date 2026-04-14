@push ('styling')
    @vite ('resources/css/pages/signin.css')
    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css"
    />
@endpush

<x-layout title="Autenticação">
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
            <form
                action="{{ route('login.post') }}"
                method="post"
            >
                @csrf
                <x-atoms.gate.card>
                    @error ('generic')
                        <x-atoms.error-msg class="mb-0">
                            {{ $message }}</x-atoms.error-msg
                        >
                    @enderror
                    <x-molecules.form-field
                        type="email"
                        id="email"
                        name="email"
                        placeholder="Insira seu email aqui"
                        label-text="Email:"
                        value="{{ old('email') }}"
                        required
                    />
                    <x-molecules.form-field
                        type="password"
                        id="password"
                        name="password"
                        placeholder="Insira sua senha aqui"
                        label-text="Senha:"
                        required
                    />
                    <x-atoms.gate.btns-row>
                        <x-atoms.submit-btn class="btn-primary">
                            Autenticar
                        </x-atoms.submit-btn>
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
        <x-packs.success-toast />
    </main>
</x-layout>
