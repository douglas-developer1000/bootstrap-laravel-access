<div
    {{
        $attributes->class([
            'toast-header',
            'd-flex',
            'gap-2'
        ])
    }}
>
    {{ $slot }}
    <strong class="me-auto">Aviso</strong>
    <button
        type="button"
        class="btn-close"
        data-bs-dismiss="toast"
        aria-label="Fechar"
    ></button>
</div>
