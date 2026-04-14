@push ('styling')
    @vite ('resources/css/pages/signup.css')
    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css"
    />
@endpush

<x-layout title="Registro de conta">
    <main class="gate-main signup-gate-main w-100 vh-100 p-0 d-flex">
        <x-packs.gate-back-img
            href="/images/signup/back.webp"
            mobile-href="/images/signup/backMobile.webp"
            img-alt="Foto de um jardim de flores"
        />
        <x-atoms.gate.container>
            <x-atoms.gate.logo
                href="/"
                class="logo"
                src="/images/signup/logo.webp"
                alt="ícone do formulário da tela de criação de usuário"
                style="--size: 9.5rem"
            />
            <form
                action="{{ route('guest.users.store') }}"
                method="post"
            >
                @csrf
                <x-atoms.gate.card>
                    <x-atoms.gate.heading>
                        Cadastro de conta
                    </x-atoms.gate.heading>
                    @error ('token')
                        <x-atoms.error-msg class="mb-0">
                            {{ $message }}</x-atoms.error-msg
                        >
                    @enderror
                    <x-molecules.form-field
                        id="name"
                        name="name"
                        placeholder="Insira seu nome aqui"
                        label-text="Nome:"
                        value="{{ old('name', '') }}"
                        required
                    />
                    <x-molecules.form-field
                        type="email"
                        id="email"
                        name="email"
                        placeholder="Insira seu e-mail aqui"
                        label-text="Email:"
                        value="{{ old('email', '') }}"
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
                    <x-molecules.form-field
                        type="password"
                        id="password_confirmation"
                        name="password_confirmation"
                        placeholder="Repita a senha aqui"
                        label-text="Confirmação:"
                        required
                    />
                    <input
                        type="hidden"
                        name="token"
                        value="{{ request()->query('token') }}"
                    />
                    <x-atoms.gate.btns-row>
                        <x-atoms.submit-btn class="btn-primary">
                            Criar
                        </x-atoms.submit-btn>
                    </x-atoms.gate.btns-row>
                </x-atoms.gate.card>
            </form>
        </x-atoms.gate.container>
    </main>
</x-layout>
