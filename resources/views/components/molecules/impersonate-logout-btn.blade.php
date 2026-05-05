@session ('impersonate-owner')
    <form
        action="{{ route('impersonate.logout') }}"
        method="post"
    >
        @csrf
        <x-atoms.button
            class="btn btn-secondary"
            type="submit"
            title="Voltar ao acesso de administrador"
        >
            <i class="bi bi-mortarboard"></i>
        </x-atoms.button>
    </form>
@endsession
