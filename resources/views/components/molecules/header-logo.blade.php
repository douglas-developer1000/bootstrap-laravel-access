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
            title="{{ $user->name }}"
        />
    @else
        <i class="bi bi-person-circle px-2 fs-1" title="{{ $user->name }}"></i>
    @endif
</a>
