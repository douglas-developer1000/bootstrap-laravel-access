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
        <ul class="navbar-nav w-auto flex-grow-1 pe-3">
            <x-superuser-menu-items />
            <x-user-menu-items />
        </ul>
    </div>
</div>
