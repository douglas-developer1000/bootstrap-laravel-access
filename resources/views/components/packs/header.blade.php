@push ('styling')
    @vite ('resources/css/components/packs/header.css')
    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css"
    />
@endpush
<header class="w-100 bg-light p-2 header-app">
    <nav class="navbar navbar-expand-lg bg-body-tertiary">
        <div class="container-fluid justify-content-start">
            <a
                class="navbar-brand"
                href="/dashboard"
                ><i class="bi bi-person-circle px-2 fs-2"></i
            ></a>
            <!-- The Hamburger Button -->
            <button
                class="navbar-toggler"
                type="button"
                data-bs-toggle="offcanvas"
                data-bs-target="#offcanvasNavbar"
                aria-controls="offcanvasNavbar"
                aria-label="Toggle navigation"
            >
                <span class="navbar-toggler-icon"></span>
            </button>
            <!-- The Collapsible Links -->
            <div
                class="offcanvas offcanvas-start"
                tabindex="-1"
                id="offcanvasNavbar"
            >
                <div class="offcanvas-body">
                    <ul class="navbar-nav w-auto flex-grow-1 pe-3">
                        <x-superuser-menu-items />
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <div class="btns">
        @can ('user')
            <x-atoms.button
                format="anchor"
                class="btn btn-secondary"
                href="{{ route('settings.user.show') }}"
            >
                <i class="bi bi-gear"></i>
            </x-atoms.button>
        @endcan
        <form
            action="{{ route('logout') }}"
            method="post"
        >
            @csrf
            <x-atoms.button
                class="btn btn-primary"
                type="submit"
                title="Sair"
            >
                <i class="bi bi-box-arrow-right"></i>
            </x-atoms.button>
        </form>
    </div>
    {{ $slot }}
</header>
