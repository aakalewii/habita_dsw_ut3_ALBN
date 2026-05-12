@include('layouts.header')

<body>
    @include('layouts.menu')

    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1>Gestión de Productos</h1>
            <a href="{{ route('productos.create') }}" class="btn btn-outline-success">Crear Producto</a>
        </div>

        <form id="filtros-form" class="mb-4" action="{{ route('productos.buscar') }}" method="GET">
            <div class="row g-2 align-items-center">
                <div class="col-md-3">
                    <input type="text" name="buscar" class="form-control" placeholder="Buscar producto..."
                        value="{{ request('buscar') }}">
                </div>
                <div class="col-md-3 d-flex">
                    <button type="submit" class="btn btn-primary flex-grow-1 me-2">
                        <i class="bi bi-funnel-fill"></i> Buscar
                    </button>
                </div>
            </div>
        </form>

        <table class="table table-striped table-hover">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Categoría</th>
                    <th>Descripción</th>
                    <th>Precio</th>
                    <th>Stock</th>
                    <th>Material</th>
                    <th>Color</th>
                    <th colspan="2" class="text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($listaProductos as $producto)
                <tr>
                    <td>{{ $producto['id'] }}</td>
                    <td>
                        <a href="{{ route('productos.show', $producto['id']) }}" class="text-decoration-none">
                            {{ $producto['nombre'] }}
                        </a>
                    </td>
                    <td>
                        @if(!empty($producto['categoria']))
                            <span class="badge bg-secondary">{{ $producto['categoria']['nombre'] }}</span>
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>
                    <td>{{ $producto['descripcion'] }}</td>
                    <td>{{ $producto['precio'] }}</td>
                    <td>{{ $producto['stock'] }}</td>
                    <td>{{ $producto['material'] ?? '-' }}</td>
                    <td>{{ $producto['color'] ?? '-' }}</td>
                    <td class="col-1 text-center">
                        <a href="{{ route('productos.edit', $producto['id']) }}" class="btn btn-primary btn-sm">
                            Editar
                        </a>
                    </td>
                    <td class="col-1 text-center">
                        <form action="{{ route('productos.destroy', $producto['id']) }}" method="POST"
                            onsubmit="return confirm('¿Estás seguro de eliminar este producto?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm">Eliminar</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @include('layouts.footer')
</body>
