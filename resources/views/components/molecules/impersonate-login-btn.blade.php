@use ('Illuminate\Support\Facades\Auth')
@use ('App\Models\User')

@props (['id' => 0])

@php
    /** @var User $user */
    $user = Auth::user();
@endphp

@if ($user->hasRole('super-admin') && $id !== $user->id)
    <form
        action="{{ route('impersonate.login', ['user' => $id]) }}"
        method="post"
    >
        @csrf
        <x-atoms.button
            class="top-right-item btn-secondary"
            type="submit"
            title="Impersonalizar acesso"
        >
            <i class="bi bi-car-front"></i>
        </x-atoms.button>
    </form>
@endif
