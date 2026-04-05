<x-layout title="Página Inicial">
    <main class="gate-main w-100 vh-100 p-0 d-flex">
        <x-packs.gate-back-img
            href="/images/home/back.webp"
            mobile-href="/images/home/backMobile.webp"
            img-alt="Foto de um cachorro deitado na grama"
        />
        <x-atoms.gate.container>
            <x-atoms.gate.logo
                src="/images/home/logoHome.webp"
                alt="ícone do formulário da tela inicial"
            />
            <x-atoms.gate.card>
                <x-atoms.gate.heading>
                    Olá! Seja bem vindo!
                </x-atoms.gate.heading>
                <x-packs.home-btns />
            </x-atoms.gate.card>
        </x-atoms.gate.container>
    </main>
</x-layout>
