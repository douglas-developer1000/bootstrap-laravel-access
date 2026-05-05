@use ('App\Libraries\Enums\PermissionNameEnum')
@can (PermissionNameEnum::HEADER_SETTINGS->value)
    <x-atoms.button
        format="anchor"
        class="btn btn-secondary"
        href="{{ route('settings.user.show') }}"
    >
        <i class="bi bi-gear"></i>
    </x-atoms.button>
@endcan
