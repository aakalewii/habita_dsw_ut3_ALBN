@include('layoutsUsuario.header')

<body>
    @include('layoutsUsuario.menu')

    <div class="container mt-4">

        <div class="card shadow-sm border-0">
            <div class="row g-0">
                <div class="col-md-5">
                    <div class="h-100 bg-light d-flex align-items-center justify-content-center">
                        @if(!empty($producto->imagen_principal))
                            <img src="{{ Storage::url($producto->imagen_principal) }}" alt="Imagen de {{ $producto->nombre }}" class="img-fluid w-100 h-100" style="object-fit: cover;">
                        @else
                            <div class="text-center text-muted py-5 w-100">
                                <i class="bi bi-image" style="font-size:3rem;"></i>
                                <p class="mb-0">Sin imagen</p>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="col-md-7">
                    <div class="card-body h-100 d-flex flex-column">
                        <div class="d-flex align-items-start justify-content-between">
                            <div>
                                <h2 class="h4 mb-1">{{ $producto->nombre }}</h2>
                            </div>
                            @if($producto->destacado)
                                <span class="badge bg-warning text-dark">Destacado</span>
                            @endif
                        </div>

                        <p class="fs-4 fw-semibold text-success mb-3">{{ $producto->precio }} &euro;</p>
                        <p class="text-muted mb-3">{{ $producto->descripcion }}</p>

                        <div class="mb-3">
                            <span class="fw-semibold d-block mb-2">Categorias</span>
                            @forelse ($producto->categorias as $categoria)
                                <span class="badge bg-secondary me-1">{{ $categoria->nombre }}</span>
                            @empty
                                <span class="text-muted">Sin categorias</span>
                            @endforelse
                        </div>

                        <div class="row g-2 small">
                            <div class="col-sm-6">
                                <div class="p-2 border rounded bg-light h-100">
                                    <span class="d-block text-muted">Dimensiones</span>
                                    <span class="fw-semibold">{{ $producto->dimensiones ?: 'N/D' }}</span>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="p-2 border rounded bg-light h-100">
                                    <span class="d-block text-muted">Color</span>
                                    <span class="fw-semibold">{{ $producto->color_principal ?: 'N/D' }}</span>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="p-2 border rounded bg-light h-100">
                                    <span class="d-block text-muted">Stock</span>
                                    <span class="fw-semibold">{{ $producto->stock }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="mt-auto pt-3">
                            <a href="{{ route('productos.galeria') }}" class="btn btn-sm btn-outline-secondary">
                                <i class="bi bi-arrow-left"></i> Volver
                            </a>
                            <a href="#" class="btn btn-sm btn-primary">
                                <i class="bi bi-cart-plus"></i> Añadir al Carrito
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('layoutsUsuario.footer')
</body>
