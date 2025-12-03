@include('layouts.header')

<body>
    @include('layouts.menu')

    <div class="container mt-4">
        <h1>Producto: {{ $producto->nombre }}</h1>

        {{-- Muestra los datos del producto sin permitir cambios --}}
        <form>
            <div class="row">
                <!-- Campo ID -->
                <div class="form-group col-md-6">
                    <label for="id">ID:</label>
                    <input type="text" class="form-control" id="id" name="id" value="{{ $producto->id }}" readonly>
                </div>

                <!-- Campo Nombre -->
                <div class="form-group col-md-6">
                    <label for="nombre">Nombre:</label>
                    <input type="text" class="form-control" id="nombre" name="nombre" value="{{ $producto->nombre }}" readonly>
                </div>

                <!-- Campo Permisos -->
                <div class="form-group col-md-6">
                    <label for="categorias">Categorías:</label>
                    <select class="form-control" id="categorias" name="categorias[]" multiple readonly>
                        @foreach ($producto->categorias as $categoria)
                            <option value="{{ $categoria->id }}">{{ $categoria->nombre }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

                <div class="form-group col-md-6">
                    <label for="descripcion">Descripción:</label>
                    <input type="text" class="form-control" id="descripcion" name="descripcion" value="{{ $producto->descripcion }}" readonly>
                </div>

                <div class="form-group col-md-6">
                    <label for="precio">Precio:</label>
                    <input type="number" class="form-control" id="precio" name="precio" value="{{ $producto->precio }}" readonly>
                </div>

                <div class="form-group col-md-6">
                    <label for="stock">Stock:</label>
                    <input type="number" class="form-control" id="stock" name="stock" value="{{ $producto->stock }}" readonly>
                </div>

                <div class="form-group col-md-6">
                    <label for="dimensiones">Dimensiones:</label>
                    <input type="text" class="form-control" id="dimensiones" name="dimensiones" value="{{ $producto->dimensiones }}" readonly>
                </div>

                <div class="form-group col-md-6">
                    <label for="color_principal">Color:</label>
                    <input type="text" class="form-control" id="color" name="color" value="{{ $producto->color_principal }}" readonly>
                </div>

                <div class="form-group col-md-6">
                    <label class="form-label">Imágenes</label>
                    @if (!empty($producto->imagen_principal))
                        <div class="d-flex flex-wrap gap-2 mt-2">
                            @foreach ($producto->imagen_principal as $ruta)
                                <img src="{{ asset('storage/' . $ruta) }}" width="100" class="border p-1">
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted">Sin imágenes</p>
                    @endif
                </div>

                <div class="form-group col-md-6">
                    <label for="destacado">Descatado:</label>
                    <input type="text" class="form-control" id="destacado" name="destacado" value="{{ $producto->destacado }}" readonly>
                </div>

            <!-- Botón Volver -->
            <div class="mt-4">
                <a href="{{ route('productos.index') }}" class="btn btn-primary">Volver</a>
            </div>
        </form>
    </div>

    @include('layouts.footer')
</body>
