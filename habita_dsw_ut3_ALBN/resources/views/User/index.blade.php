@include('layoutsUsuario.header')

@include('layoutsUsuario.menu')

<div class="container mt-5 mb-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-person-vcard"></i> Mi Perfil</h5>
                </div>
                <div class="card-body">
                    
                    <div class="text-center mb-4">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($usuario['name']) }}&background=random&color=fff&size=128&rounded=true" alt="Avatar de {{ $usuario['name'] }}" class="mb-3 shadow-sm" width="120">
                        <i class="bi bi-person-circle text-secondary" style="font-size: 5rem;"></i>
                        <h4 class="mt-2">{{ $usuario['name'] }}</h4>
                        <span class="badge bg-info text-dark fs-6">{{ $usuario['rol'] }}</span>
                    </div>

                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span><i class="bi bi-envelope-fill text-muted me-2"></i> Correo Electrónico:</span>
                            <strong>{{ $usuario['email'] }}</strong>
                        </li>
                        
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span><i class="bi bi-calendar-check-fill text-muted me-2"></i> Miembro desde:</span>
                            <strong>{{ \Carbon\Carbon::parse($usuario['created_at'])->format('d/m/Y') }}</strong>
                        </li>
                    </ul>

                </div>
                <div class="card-footer text-center bg-light">
                    @if(session('user_rol') === 'Administrador')
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-primary">
                            <i class="bi bi-arrow-left"></i> Volver al Panel
                        </a>
                    @else
                        <a href="{{ route('productos.galeria') }}" class="btn btn-outline-primary">
                            <i class="bi bi-arrow-left"></i> Volver a la Tienda
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>