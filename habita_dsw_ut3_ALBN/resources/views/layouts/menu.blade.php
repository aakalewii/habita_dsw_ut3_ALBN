<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container-fluid">
        <!-- Inicio -->
        <li class="nav-item list-unstyled">
            <a class="navbar-brand fw-bold {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                <i class="bi bi-speedometer2"></i> Inicio
            </a>
        </li>

        <!-- Opciones del Menú -->
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <!-- Opción Categorias -->
                <li class="nav-item">
                    <a class="nav-link text-white {{ request()->routeIs('categorias.index') ? 'active' : '' }}" href="{{ route('categorias.index') }}">
                        <i class="bi bi-people-fill"></i> Categorias
                    </a>
                </li>

                <!-- Opción Productos -->
                <li class="nav-item">
                    <a class="nav-link text-white {{ request()->routeIs('productos.index') ? 'active' : '' }}" href="{{ route('productos.index') }}">
                        <i class="bi bi-shield-lock-fill"></i> Productos
                    </a>
                </li>
            </ul>
            <div class="d-flex align-items-center gap-2">
                <!-- Verificamos que haya sesión iniciada con nuestro token -->
                @if(session()->has('api_token'))
                    <!-- Mostramos el nombre del administrador -->
                    <a href="{{ route('perfil.index') }}" class="nav-link text-white me-3">
                        <i class="bi bi-person-circle"></i> {{ session('name') }}
                    </a>

                    <form action="{{ route('logout') }}" method="POST" class="mb-0">
                        @csrf
                        <button type="submit" class="btn btn-outline-light btn-sm">
                            <i class="bi bi-box-arrow-right"></i> Cerrar sesión
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>
</nav>