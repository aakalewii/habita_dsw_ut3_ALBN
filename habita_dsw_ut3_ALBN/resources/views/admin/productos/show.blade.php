@include('layouts.header')

<body>
    @include('layouts.menu')

    <div class="container mt-4">
        <h1>Producto: {{ $producto['nombre'] }}</h1>

        <form>
            <div class="row">
                <div class="form-group col-md-6">
                    <label for="id">ID:</label>
                    <input type="text" class="form-control" id="id" value="{{ $producto['id'] }}" readonly>
                </div>

                <div class="form-group col-md-6">
                    <label for="nombre">Nombre:</label>
                    <input type="text" class="form-control" id="nombre" value="{{ $producto['nombre'] }}" readonly>
                </div>

                <div class="form-group col-md-6">
                    <label>Categoría:</label>
                    <input type="text" class="form-control" value="{{ $producto['categoria']['nombre'] ?? 'Sin categoría' }}" readonly>
                </div>

                <div class="form-group col-md-6">
                    <label for="descripcion">Descripción:</label>
                    <input type="text" class="form-control" id="descripcion" value="{{ $producto['descripcion'] }}" readonly>
                </div>

                <div class="form-group col-md-6">
                    <label for="precio">Precio:</label>
                    <input type="number" class="form-control" id="precio" value="{{ $producto['precio'] }}" readonly>
                </div>

                <div class="form-group col-md-6">
                    <label for="stock">Stock:</label>
                    <input type="number" class="form-control" id="stock" value="{{ $producto['stock'] }}" readonly>
                </div>

                <div class="form-group col-md-6">
                    <label for="material">Material:</label>
                    <input type="text" class="form-control" id="material" value="{{ $producto['material'] ?? 'N/D' }}" readonly>
                </div>

                <div class="form-group col-md-6">
                    <label for="color">Color:</label>
                    <input type="text" class="form-control" id="color" value="{{ $producto['color'] ?? 'N/D' }}" readonly>
                </div>

                <div class="form-group col-md-6">
                    <label class="form-label">Imagen</label>
                    @if(!empty($producto['imagen_url']))
                        <div class="mt-2">
                            <img src="{{ $producto['imagen_url'] }}" width="150" class="border p-1">
                        </div>
                    @else
                        <p class="text-muted">Sin imagen</p>
                    @endif
                </div>
            </div>

            <div class="mt-4">
                <a href="{{ route('productos.index') }}" class="btn btn-primary">Volver</a>
            </div>
        </form>
    </div>

    @include('layouts.footer')
</body>
