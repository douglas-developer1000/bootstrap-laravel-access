@php
    $user = auth()->user();
@endphp
<a
    class="navbar-brand"
    href="{{ route('dashboard') }}"
>
    @if ($user->photo)
        <img
            src="{{ $user->photo }}"
            alt="foto do usuário"
            class="rounded-circle photo-user"
        />
    @else
        <i class="bi bi-person-circle px-2 fs-1"></i>
    @endif
</a>
