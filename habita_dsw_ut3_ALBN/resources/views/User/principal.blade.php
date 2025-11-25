@include('layoutsUsuario.header')

<body>
    @include('layoutsUsuario.menu')

    <div class="container mt-4">
        <h1 class="mb-4 text-center">Nuestros Productos</h1>

        <div class="row">
            @forelse ($listaProductos as $producto)
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        {{-- Idealmente aquí iría una imagen del producto --}}
                        {{-- <img src="{{ $producto->imagen_principal ?? 'https://via.placeholder.com/300' }}" class="card-img-top" alt="{{ $producto->nombre }}"> --}}
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title">{{ $producto->nombre }}</h5>
                            <p class="card-text">{{ Str::limit($producto->descripcion, 100) }}</p>
                            <p class="card-text"><strong>Precio:</strong> {{ number_format($producto->precio, 2) }} €</p>
                            <div class="mt-auto">
                                @foreach ($producto->categorias as $categoria)
                                    <span class="badge bg-secondary">{{ $categoria->nombre }}</span>
                                @endforeach
                            </div>
                            <a href="{{ route('user.productos.show', $producto) }}" class="btn btn-primary mt-3">Ver Producto</a>
                            <a href="#" class="btn btn-primary mt-3">Agregar al Carrito</a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col">
                    <p class="text-center">No hay productos disponibles en este momento.</p>
                </div>
            @endforelse
        </div>

        <div class="d-flex justify-content-center">
            {{ $listaProductos->links() }}
        </div>
    </div>

    @include('layoutsUsuario.footer')
</body>

