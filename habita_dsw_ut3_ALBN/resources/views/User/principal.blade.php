@include('layoutsUsuario.header')

<body>
    @include('layoutsUsuario.menu')
    <div class="container mt-4">
        <h2><i class="bi bi-grid"></i> Galería de Productos</h2>

        {{-- Filtros por categoría --}}

        <form id="filtros-form" class="mb-4" action="{{ route('productos.galeria') }}" method="GET">
            <div class="row g-2 align-items-center">
                {{-- Búsqueda por texto --}}
                <div class="col-md-3">
                    <input type="text" name="buscar" class="form-control" placeholder="Buscar producto..."
                        value="{{ request('buscar') }}">
                </div>

                {{-- Filtro por Categoría --}}
                <div class="col-md-2">
                    <select name="categoria_id" class="form-select">
                        <option value="">Todas las categorías</option>
                        @foreach ($categorias as $categoria)
                            <option value="{{ $categoria->id }}" @selected(request('categoria_id') == $categoria->id)>
                                {{ $categoria->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>
                {{-- Filtro por Color --}}
                <div class="col-md-2">
                    <select name="color_principal" class="form-select">
                        <option value="">Color</option>
                        @foreach ($colores as $color)
                            <option value="{{ $color }}" @selected(request('color_principal') === $color)>
                                {{ $color }}
                            </option>
                        @endforeach
                    </select>
                </div>
                {{-- Orden  --}}
                <div class="col-md-2">
                    <select name="orden" class="form-select">
                        <option value="nombre_asc" @selected(request('orden') === 'nombre_asc')>A-Z</option>
                        <option value="nombre_desc" @selected(request('orden') === 'nombre_desc')>Z-A</option>
                        <option value="precio_asc" @selected(request('orden') === 'precio_asc')>Menor a Mayor precio</option>
                        <option value="precio_desc" @selected(request('orden') === 'precio_desc')>Mayor a Menor precio</option>
                    </select>
                </div>
                {{-- Filtros de precio --}}
                <div class="col-md-2">
                    <label for="precio_min" class="visually-hidden">Precio Mínimo</label>
                    <input type="number" name="precio_min" id="precio_min" class="form-control"
                        placeholder="Precio Mín." step="0.01" value="{{ request('precio_min') }}">
                </div>
                <div class="col-md-2">
                    <label for="precio_max" class="visually-hidden">Precio Máximo</label>
                    <input type="number" name="precio_max" id="precio_max" class="form-control"
                        placeholder="Precio Máx." step="0.01" value="{{ request('precio_max') }}">
                </div>
                <div class="col-md-2 d-flex align-items-center">
                    <div class="form-check">
                        <input type="checkbox" name="destacado" id="destacado" class="form-check-input" value="1"
                            @checked(request()->boolean('destacado'))>
                        <label class="form-check-label" for="destacado">Destacado</label>
                    </div>
                </div>

                {{-- Botones de acción --}}
                <div class="col-md-3 d-flex justify-content-end ms-auto">
                    <button type="submit" class="btn btn-primary flex-grow-1 me-2">
                        <i class="bi bi-funnel-fill"></i> Aplicar Filtros
                    </button>
                    <a href="{{ route('productos.galeria') }}" class="btn btn-outline-secondary"
                        title="Limpiar filtros">Limpiar Filtros<i class="bi bi-x-lg"></i></a>
                </div>
            </div>
        </form>

        {{-- LECTURA DE LA MONEDA para mostrar el símbolo correcto--}}
        @php
            $monedaSimbolo = request()->cookie('preferencia_moneda', '€');
        @endphp

        {{-- Galería --}}
        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4">
            @forelse($listaProductos as $producto)
                <div class="col">
                    <div class="card h-100 shadow-sm border-0">

                        <div class="d-flex align-items-center justify-content-center bg-light" style="height:220px;">
                            @php
                                $imagen = $producto->imagen_url ?? null;
                                if (!$imagen && is_array($producto->imagen_principal)) {
                                    $imagen = $producto->imagen_principal[0] ?? null;
                                }
                            @endphp
                            @if(!empty($imagen))
                                <img src="{{ $imagen }}"
                                    alt="Imagen de {{ $producto->nombre }}" class="img-fluid h-100 w-100"
                                    style="object-fit: cover;">
                            @else
                                <i class="bi bi-image text-muted" style="font-size:3rem;"></i>
                            @endif
                        </div>


                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title text-primary">{{ $producto->nombre }}</h5>
                            <p class="card-text text-muted mb-1">{{ Str::limit($producto->descripcion, 60) }}</p>
                            {{-- Precio DINAMICO --}}
                            <p class="fw-bold text-success mt-auto">{{ $producto->precio }} {{ $monedaSimbolo }}</p>
                        </div>

                        <div class="card-footer bg-transparent border-0 d-flex justify-content-between">
                            <a href="{{ route('user.productos.show', $producto->id) }}"
                                class="btn btn-sm btn-outline-secondary">
                                <i class="bi bi-eye"></i> Ver
                            </a>
                            <form action="{{ route('carrito.add', $producto->id) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-primary">
                                    <i class="bi bi-cart-plus"></i> Añadir
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-5">
                    <p class="lead text-muted">No hay productos disponibles.</p>
                </div>
            @endforelse
        </div>

        {{-- Paginación de Laravel con Bootstrap 5 --}}
        <div class="mt-5 mb-4">
            {{ $listaProductos->appends(request()->query())->links() }}
        </div>

    </div>
    @include('layoutsUsuario.footer')
</body>
