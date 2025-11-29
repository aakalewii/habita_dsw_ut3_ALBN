<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container-fluid">
        <!-- Opción Inicio -->
        <li class="nav-item">
            <a class="navbar-brand fw-bold {{ request()->routeIs('productos.galeria') }}" href="{{ route('productos.galeria') }}">
                <i class="bi bi-house-door-fill"></i> Inicio </a>
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
                @guest
                    <!-- Opción Registrar -->
                    <li class="nav-item">
                        <a class="nav-link text-white" href="{{ route('register') }}">
                            <i class="bi bi-people-fill"></i> Registrarse
                        </a>
                    </li>

                    <!-- Opción Login -->
                    <li class="nav-item">
                        <a class="nav-link text-white" href="{{ route('login') }}">
                            <i class="bi bi-shield-lock-fill"></i> Login
                        </a>
                    </li>
                @endguest
            </ul>
            <div class="d-flex">
                @auth
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
