@include('layoutsUsuario.header')

<body>
    @include('layoutsUsuario.menu')
    <div class="container mt-4">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="bi bi-grid"></i> Galería de Productos</h2>

            <form class="d-flex" action="{{ route('productos.galeria') }}" method="GET">
                <input type="text" name="buscar" class="form-control me-2" placeholder="Buscar producto..."
                    value="{{ request('buscar') }}">
                <button class="btn btn-outline-primary" type="submit">Buscar<i class="bi bi-search"></i></button>
            </form>
        </div>

        {{-- Filtros por categoría --}}
        <form class="mb-4" action="{{ route('productos.galeria') }}" method="GET">
            <div class="row g-2 align-items-center">
                <div class="col-md-4">
                    <select name="categoria_id" class="form-select" onchange="this.form.submit()">
                        <option value="">Todas las categorías</option>
                        @foreach ($categorias as $categoria)
                            <option value="{{ $categoria->id }}" @selected(request('categoria_id') == $categoria->id)>
                                {{ $categoria->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </form>

        {{-- Galería --}}
        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4">
            @forelse($listaProductos as $producto)
                <div class="col">
                    <div class="card h-100 shadow-sm border-0">

                        <div class="d-flex align-items-center justify-content-center bg-light" style="height:220px;">
                            <i class="bi bi-image text-muted" style="font-size:3rem;"></i>
                        </div>


                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title text-primary">{{ $producto->nombre }}</h5>
                            <p class="card-text text-muted mb-1">{{ Str::limit($producto->descripcion, 60) }}</p>
                            <p class="fw-bold text-success mt-auto">{{ $producto->precioFormateado }}</p>
                        </div>

                        <div class="card-footer bg-transparent border-0 d-flex justify-content-between">
                            <a href="{{ route('user.productos.show', $producto) }}" class="btn btn-sm btn-outline-secondary">
                                <i class="bi bi-eye"></i> Ver
                            </a>
                            <a href="#" class="btn btn-sm btn-primary">
                                <i class="bi bi-cart-plus"></i> Añadir
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-5">
                    <p class="lead text-muted">No hay productos disponibles.</p>
                </div>
            @endforelse
        </div>

        {{-- Paginación --}}
        <div class="mt-4 d-flex justify-content-center">
            {{ $listaProductos->withQueryString()->links() }}
        </div>

    </div>
    @include('layoutsUsuario.footer')
</body>
