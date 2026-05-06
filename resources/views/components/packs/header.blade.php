@push ('styling')
    @vite ('resources/css/components/packs/header.css')
    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css"
    />
@endpush

<header class="w-100 bg-light p-2 header-app">
    <x-organisms.navbar>
        <x-molecules.header-logo />
    </x-organisms.navbar>

    <div class="btns">
        <x-molecules.impersonate-logout-btn />
        <x-molecules.settings-btn />
        <x-molecules.logout-btn />
    </div>
    {{ $slot }}
</header>
