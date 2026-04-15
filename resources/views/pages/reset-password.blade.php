@push ('styling')
    @vite ('resources/css/pages/generic/password.css')
@endpush

<x-layout title="Redefinição de senha">
    <main
        class="gate-main password-gate-main w-100 vh-100 p-0 d-flex"
    >
        <x-packs.gate-back-img
            href="/images/reset-password/back.webp"
            mobile-href="/images/reset-password/backMobile.webp"
            img-alt="Foto de um peixe em um aquário"
        />
        <x-atoms.gate.container>
            <x-atoms.gate.logo
                href="/"
                class="logo"
                src="/images/reset-password/logo.webp"
                alt="ícone do formulário da tela de redefinição de senha"
            />
            <form
                method="post"
                action="{{ route('password.update') }}"
            >
                @csrf
                <x-atoms.gate.card>
                    <x-atoms.gate.heading>
                        Redefinir senha
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
                        placeholder="Insira seu email"
                        label-text="Email:"
                        :value="old('email', '')"
                        required
                    />
                    <x-molecules.form-field
                        type="password"
                        id="password"
                        name="password"
                        placeholder="Insira sua nova senha"
                        label-text="Senha:"
                        required
                    />
                    <x-molecules.form-field
                        type="password"
                        id="password_confirmation"
                        name="password_confirmation"
                        placeholder="Repita sua nova senha"
                        label-text="Confirmação:"
                        required
                    />
                    <input
                        type="hidden"
                        name="token"
                        value="{{ $token ?? '' }}"
                    />
                    <x-atoms.gate.btns-row>
                        <x-atoms.submit-btn class="btn-primary">
                            Modificar
                        </x-atoms.submit-btn>
                    </x-atoms.gate.btns-row>
                </x-atoms.gate.card>
            </form>
        </x-atoms.gate.container>
    </main>
</x-layout>
