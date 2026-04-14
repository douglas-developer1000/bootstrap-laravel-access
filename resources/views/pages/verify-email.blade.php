@push ('styling')
    {{-- @vite ('resources/css/pages/signin.css') --}}
    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css"
    />
@endpush

<x-layout title="Autenticação">
    <main class="gate-main verify-email-gate-main w-100 vh-100 p-0 d-flex">
        <x-packs.gate-back-img
            href="/images/verify-email/back.webp"
            mobile-href="/images/verify-email/backMobile.webp"
            img-alt="Foto de dois cavalos"
        />
        <x-atoms.gate.container>
            <x-atoms.gate.logo
                href="/"
                class="logo"
                src="/images/verify-email/logo.webp"
                alt="ícone do tela de email não verificado"
            />
            <x-atoms.gate.card>
                <x-atoms.gate.heading>
                    Email ainda não verificado!
                </x-atoms.gate.heading>
                <p>Antes de continuar, por favor verifique seu email para o link de confirmação.</p>
                <p>Se você não recebeu o email, clique no botão abaixo para reenviar.</p>
                <x-atoms.gate.btns-row>
                    <form
                        method="post"
                        action="{{ route('verification.send') }}"
                    >
                        @csrf
                        <x-atoms.submit-btn class="btn-primary">
                            Reenviar email de verificação
                        </x-atoms.submit-btn>
                    </form>
                </x-atoms.gate.btns-row>
            </x-atoms.gate.card>
        </x-atoms.gate.container>
        <x-packs.success-toast />
    </main>
</x-layout>
