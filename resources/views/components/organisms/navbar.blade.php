<nav class="navbar bg-body-tertiary">
    <div class="container-fluid justify-content-start">
        {{ $slot }}
        {{-- The Hamburger Button --}}
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
        {{-- The Collapsible Links --}}
        <div
            class="offcanvas offcanvas-start"
            tabindex="-1"
            id="offcanvasNavbar"
        >
            <div class="offcanvas-body">
                <ul class="navbar-nav w-auto flex-grow-1">
                    {{-- <li
                        id="accordion-menu"
                        class="nav-item text-center w-auto accordion accordion-flush"
                    >
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button
                                    class="accordion-button collapsed"
                                    type="button"
                                    data-bs-toggle="collapse"
                                    data-bs-target="#flush-collapse-menu"
                                    aria-expanded="false"
                                    aria-controls="flush-collapse-menu"
                                >
                                    <div
                                        class="label position-relative w-100 h-100"
                                    >
                                        Administração
                                    </div>
                                </button>
                            </h2>
                        </div>
                        <ul
                            id="flush-collapse-menu"
                            class="accordion-collapse collapse"
                            data-bs-parent="#accordion-menu"
                        >
                            <x-molecules.superuser-menu-items />
                        </ul>
                    </li> --}}
                    <x-molecules.superuser-menu-items />
                    <x-molecules.user-menu-items />
                </ul>
            </div>
        </div>
    </div>
</nav>
