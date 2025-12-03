@include('layouts.header')

<body>
    @include('layouts.menu')

        <div class="container mt-4">
            {{-- Vista que lista todos los productos para administrarlos --}}
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h1>Gestión de Productos</h1>
                <a href="{{ route('productos.create') }}" class="btn btn-outline-success">Crear Producto</a>
            </div>

            
        {{-- Buscador para filtrar productos por nombre --}}
        <form id="filtros-form" class="mb-4" action="{{ route('productos.buscar') }}" method="GET">
            <div class="row g-2 align-items-center">
                {{-- Búsqueda por texto --}}
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

                {{-- Tabla que muestra cada producto con opciones de ver, editar o borrar --}}
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Categorías</th>
                            <th>Descripción</th>
                            <th>Precio</th>
                            <th>Stock</th>
                            <th>Materiales</th>
                            <th>Dimensiones</th>
                            <th>Color</th>
                            <th>Destacado</th>
                            <th colspan="2" class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($listaProductos as $producto)
                        <tr>
                            <td>{{ $producto->id }}</td>
                            <td>
                                <a href="{{ route('productos.show', $producto) }}" class="text-decoration-none">
                                    {{ $producto->nombre }}
                                </a>
                            </td>
                            <td>
                                @foreach ($producto->categorias as $categoria)
                                    <span class="badge bg-secondary">{{ $categoria->nombre }}</span>
                                @endforeach
                            </td>
                            <td>{{ $producto->descripcion }}</td>
                            <td>{{ $producto->precio }}</td>
                            <td>{{ $producto->stock }}</td>
                            <td>{{ $producto->materiales }}</td>
                            <td>{{ $producto->dimensiones }}</td>
                            <td>{{ $producto->color_principal }}</td>
                            <td>{{ $producto->destacado ? 'Sí' : 'No' }}</td>
                            <td class="col-1 text-center">
                                <a href="{{ route('productos.edit', $producto) }}" class="btn btn-primary btn-sm">
                                    Editar
                                </a>
                            </td>
                            <td class="col-1 text-center">
                                <form action="{{ route('productos.destroy', $producto) }}" method="POST"
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
