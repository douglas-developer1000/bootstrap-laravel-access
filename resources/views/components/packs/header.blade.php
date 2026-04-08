@push ('styling')
    @vite ('resources/css/components/packs/header.css')
    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css"
    />
@endpush
<header class="w-100 bg-light p-2 header-app">
    <a
        href="/dashboard"
        class="d-block"
    >
        <i class="bi bi-person-circle px-2 fs-2"></i>
    </a>
    <nav class="navigation">
        <ul class="d-flex list-unstyled mb-0 gap-3 px-2">
            <li>
                <a
                    class="text-decoration-none"
                    href="{{ route('roles.index') }}"
                >
                    Papeis
                </a>
            </li>
            <li>
                <a
                    class="text-decoration-none"
                    href="{{ route('permissions.index') }}"
                >
                    Permissões
                </a>
            </li>
            <li>Usuários</li>
        </ul>
    </nav>
    <div class="btns">
        <form
            action="{{ route('logout') }}"
            method="post"
        >
            @csrf
            <button
                type="submit"
                class="btn btn-primary"
                title="Sair"
            >
                <i class="bi bi-box-arrow-right"></i>
            </button>
        </form>
    </div>
    {{ $slot }}
</header>
