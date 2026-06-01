<nav class="navbar navbar-light bg-light border-bottom px-4">
    <span class="navbar-brand mb-0 h5">Panel de administración</span>

    <div class="d-flex align-items-center">
        <span class="me-3">{{ auth()->user()?->name ?? 'Usuario' }}</span>

        <form action="{{ route('logout') }}" method="POST" class="mb-0">
            @csrf
            <button type="submit" class="btn btn-outline-danger btn-sm">
                Cerrar sesión
            </button>
        </form>
    </div>
</nav>
