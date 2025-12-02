<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container-fluid">
        <!-- Inicio -->
        <li class="nav-item">
            <a class="navbar-brand fw-bold {{ request()->routeIs('admin.dashboard') }}" href="{{ route('admin.dashboard') }}">
                <i class="bi bi-speedometer2"></i> Inicio
            </a>
        </li>

        <!-- Botón de Desplegable para Móviles -->
        <button
            class="navbar-toggler"
            type="button"
            data-bs-toggle="collapse"
            data-bs-target="#navbarSupportedContent"
            aria-controls="navbarSupportedContent"
            aria-expanded="false"
            aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Opciones del Menú -->
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <!-- Opción Usuarios -->
                <li class="nav-item">
                    <a class="nav-link text-white {{ request()->routeIs('categorias.index') }}" href="{{ route('categorias.index') }}">
                        <i class="bi bi-people-fill"></i> Categorias
                    </a>
                </li>

                <!-- Opción Roles -->
                <li class="nav-item">
                    <a class="nav-link text-white {{ request()->routeIs('productos.index') }}" href="{{ route('productos.index') }}">
                        <i class="bi bi-shield-lock-fill"></i> Productos
                    </a>
                </li>
            </ul>
            <div class="d-flex gap-2">
                @auth
                    <!-- Preferencias -->
                    <a href="{{ route('preferencias.edit') }}" class="btn btn-outline-light btn-sm">
                        <i class="bi bi-gear-fill"></i> Preferencias
                    </a>
                    <form action="{{ route('logout') }}" method="POST" class="mb-0">
                        @csrf
                        <button type="submit" class="btn btn-outline-light btn-sm">
                            <i class="bi bi-box-arrow-right"></i> Cerrar sesión
                        </button>
                    </form>
                @endauth
            </div>
        </div>
    </div>
</nav>
