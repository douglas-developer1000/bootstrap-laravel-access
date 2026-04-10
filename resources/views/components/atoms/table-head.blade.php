@props (['sort'])

<th scope="col">
    <a
        class="text-decoration-none position-relative"
        href="{{ $makeHref(request()->url()) }}"
    >
        {{ $slot }}
        @if (request()->query('sort', 'created_at') === $sort)
            <span class="badge text-dark p-0 position-absolute">
                @if (request()->query('order', 'desc') === 'desc')
                    <i class="bi bi-caret-down-fill"></i>
                @else
                    <i class="bi bi-caret-up-fill"></i>
                @endif
            </span>
        @endif
    </a>
</th>
