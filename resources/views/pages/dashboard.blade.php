<x-layout title="Painel">
    <form
        action="{{ route('logout') }}"
        method="post"
    >
        @csrf
        <button
            type="submit"
            class="btn btn-primary"
        >
            Sair
        </button>
    </form>
</x-layout>
