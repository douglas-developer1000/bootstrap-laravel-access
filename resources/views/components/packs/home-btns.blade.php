@push ('styling')
    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css"
    />
@endpush

<div class="d-flex flex-column py-3 w-100 gap-1">
    <div class="p-2">
        <a
            href="/signin"
            role="button"
            class="btn btn-outline-primary rounded-3 w-100 d-flex justify-content-center align-items-center gap-2"
        >
            <i class="bi bi-door-open fs-4"></i
            ><span class="fs-085">Entrar</span>
        </a>
    </div>
    <div class="p-2">
        <a
            href="/register-request"
            role="button"
            class="btn btn-outline-primary rounded-3 w-100 d-flex justify-content-center align-items-center gap-2"
        >
            <i class="bi bi-backpack4 fs-4"></i
            ><span class="fs-085">Registrar</span>
        </a>
    </div>
</div>
