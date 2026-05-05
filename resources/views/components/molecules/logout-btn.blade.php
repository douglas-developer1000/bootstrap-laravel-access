<form
    action="{{ route('logout') }}"
    method="post"
>
    @csrf
    <x-atoms.button
        class="btn btn-primary"
        type="submit"
        title="Sair"
    >
        <i class="bi bi-box-arrow-right"></i>
    </x-atoms.button>
</form>
