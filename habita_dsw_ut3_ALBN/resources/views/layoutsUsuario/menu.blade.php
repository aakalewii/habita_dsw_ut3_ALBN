<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container-fluid">
        <li class="nav-item list-unstyled">
            <a class="navbar-brand fw-bold {{ request()->routeIs('productos.galeria') ? 'active' : '' }}" href="{{ route('productos.galeria') }}">
                <i class="bi bi-house-door-fill"></i> Inicio 
            </a>
        </li>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                @if(!session()->has('api_token'))
                    <li class="nav-item">
                        <a class="nav-link text-white" href="{{ route('register') }}">
                            <i class="bi bi-people-fill"></i> Registrarse
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link text-white" href="{{ route('login') }}">
                            <i class="bi bi-shield-lock-fill"></i> Login
                        </a>
                    </li>
                @endif
            </ul>
            <div class="d-flex">
                @if(session()->has('api_token'))
                    <a href="{{ route('perfil.index') }}" class="nav-link text-white me-3">
                        <i class="bi bi-person-circle"></i> {{ session('name') }}
                    </a>

                    <a href="{{ route('preferencias.edit') }}" class="btn btn-outline-light btn-sm me-2">
                        <i class="bi bi-gear-fill"></i> Preferencias
                    </a>
                    <a href="{{ route('carrito.index') }}" class="btn btn-outline-light btn-sm me-2">
                        <i class="bi bi-cart-fill"></i> Ver Carrito
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