@include('layouts.header')

<body>
    @include('layouts.menu')
    <div class="container mt-4">
        <h1 class="mb-4">Editar Producto: {{ $producto->nombre }}</h1>

        <!-- Formulario para editar el rol -->
        <form action="{{ route('productos.update', $producto->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="row">
                <!-- Campo Nombre -->
                <div class="form-group col-md-6">
                    <label for="nombre">Nombre:</label>
                    <input type="text" class="form-control" id="nombre" name="nombre" value="{{ $producto->nombre }}">
                </div>

                <!-- Campo Permisos -->
                <div class="form-group col-md-6">
                    <label for="categorias">Categorías:</label>
                    <select class="form-control" id="categorias" name="categoria_id[]" multiple>
                        @foreach ($listaCategorias as $categoria)
                            <option value="{{ $categoria->id }}"
                                @if ($producto->categorias->contains('id', $categoria->id)) selected @endif>
                                {{ $categoria->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group col-md-6">
                    <label for="descripción">Descripción:</label>
                    <input type="text" class="form-control" id="descripcion" name="descripcion" value="{{ $producto->descripcion }}">
                </div>

                <div class="form-group col-md-6">
                    <label for="precio">Precio:</label>
                    <input type="number" class="form-control" id="precio" name="precio" value="{{ $producto->precio }}">
                </div>

                <div class="form-group col-md-6">
                    <label for="stock">Stock:</label>
                    <input type="number" class="form-control" id="stock" name="stock" value="{{ $producto->stock }}">
                </div>

                <div class="form-group col-md-6">
                    <label for="materiales">Materiales:</label>
                    <input type="text" class="form-control" id="materiales" name="materiales" value="{{ $producto->materiales }}">
                </div>

                <div class="form-group col-md-6">
                    <label for="dimensiones">Dimensiones:</label>
                    <input type="text" class="form-control" id="dimensiones" name="dimensiones" value="{{ $producto->dimensiones }}">
                </div>

                <div class="form-group col-md-6">
                    <label for="color_principal">Color:</label>
                    <input type="text" class="form-control" id="color_principal" name="color_principal" value="{{ $producto->color_principal }}">
                </div>

                <div class="form-group col-md-6">
                    <label class="form-label">Imagen</label>
                    <input type="file" name="imagen_principal" class="form-control">
                    @if (!empty($producto->imagen_principal))
                        <img src="{{ asset('storage/' . $producto->imagen_principal) }}" width="80" class="mt-2">
                    @endif

                    <!-- mensajes de error con plantillas BLADE -->
                    @error('imagen_principal')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <div class="form-group col-md-6">
                    <label for="destacado">Destacado:</label>

                    <!-- hidden para enviar 0 si no se marca -->
                    <input type="hidden" name="destacado" value="0">

                    <!-- Checkbox (envía 1 si está marcado) -->
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="1" id="destacado" name="destacado" {{ old('destacado', $producto->destacado) ? 'checked' : '' }}>
                        <label class="form-check-label" for="destacado">Marcar como destacado</label>
                    </div>

                </div>
            </div>

            <!-- Botones -->
            <div class="mt-4">
                <button type="submit" class="btn btn-primary">Actualizar</button>
                <a href="{{ route('productos.index') }}" class="btn btn-secondary">Volver</a>
            </div>
        </form>
    </div>

    @include('layouts.footer')
</body>
