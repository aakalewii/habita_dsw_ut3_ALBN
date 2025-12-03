@include('layoutsUsuario.header')

@include('layoutsUsuario.menu')

<div class="container mt-4">
    <h2><i class="bi bi-gear-fill"></i> Preferencias de Usuario</h2>

    {{-- Mensaje de confirmación --}}
    @if(session('mensaje'))
        <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
            <i class="bi bi-check-circle-fill"></i> {{ session('mensaje') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card shadow-sm mt-4">
        <div class="card-body">
            <form action="{{ route('preferencias.update') }}" method="POST">
                {{-- Opciones de moneda --}}
                @csrf
                <div class="mb-3">
                    <label for="moneda" class="form-label">Moneda</label>
                    <select name="moneda" id="moneda" class="form-select">
                        <option value="€" {{ $moneda == '€' ? 'selected' : '' }}>Euro (€)</option>
                        <option value="$" {{ $moneda == '$' ? 'selected' : '' }}>Dólar ($)</option>
                        <option value="£" {{ $moneda == '£' ? 'selected' : '' }}>Libra (£)</option>
                    </select>
                </div>
                {{-- Opciones de paginación --}}
                <div class="mb-3">
                    <label for="paginacion" class="form-label">Elementos por página</label>
                    <select name="paginacion" id="paginacion" class="form-select">
                        <option value="6" {{ $paginacion == '6' ? 'selected' : '' }}>6 elementos</option>
                        <option value="12" {{ $paginacion == '12' ? 'selected' : '' }}>12 elementos</option>
                        <option value="24" {{ $paginacion == '24' ? 'selected' : '' }}>24 elementos</option>
                    </select>
                    <small class="form-text text-muted">Esta preferencia se aplicará al visualizar los productos.</small>
                </div>

                {{-- Opciones de color --}}
                <div class="mb-3">
                    <label for="tema" class="form-label">Tema de la Interfaz</label>
                    <select name="tema" id="tema" class="form-select">
                        <option value="claro" {{ $tema == 'claro' ? 'selected' : '' }}>Claro</option>
                        <option value="oscuro" {{ $tema == 'oscuro' ? 'selected' : '' }}>Oscuro</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary">Guardar Preferencias</button>
            </form>
        </div>
    </div>
</div>

@include('layoutsUsuario.footer')
