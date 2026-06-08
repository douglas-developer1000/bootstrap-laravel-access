@use ('Illuminate\Support\Facades\Auth')

<x-atoms.button
    format="anchor"
    class="btn btn-secondary"
    href="{{ route('settings.user.show') }}"
    :disabled="!Auth::user()->can('show', Auth::user())"
>
    <i class="bi bi-gear"></i>
</x-atoms.button>
