@if (auth()->user()->hasRole('super-admin'))
    @foreach ($menuItems as $label => $href)
        <li class="nav-item text-center w-auto">
            <a
                class="nav-link w-auto {{ $href === $url ? 'active' : '' }}"
                href="{{ $href }}"
                @if ($href === $url)
                    aria-current="page"
                @endif
            >
                {{ $label }}
            </a>
        </li>
    @endforeach
@endif
