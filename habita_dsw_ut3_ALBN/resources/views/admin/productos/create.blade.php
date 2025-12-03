@include('layouts.header')

<body>
    @include('layouts.menu')

    <div class="container mt-4">
        <h1 class="mb-4">Crear Rol</h1>

        {{-- Formulario para crear un producto nuevo --}}
        <form action="{{ route('productos.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('POST')

            <div class="form-row">

                <div class="form-group col-md-6">
                    <label for="nombre">Nombre:</label>
                    <input type="text" class="form-control" id="nombre" name="nombre" value="">

                    <!-- mensajes de error con plantillas BLADE -->
                    @error('nombre')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <div class="form-group col-md-6">
                    <label for="categorias">Categorías:</label>
                    <select class="form-control" id="categorias" name="categoria_id[]" multiple>
                        @foreach ($listaCategorias as $categoria)
                            <option value="{{ $categoria->id }}">{{ $categoria->nombre }}</option>
                        @endforeach
                    </select>

                    @error('categoria_id')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <div class="form-group col-md-6">
                    <label for="descripción">Descripción:</label>
                    <input type="text" class="form-control" id="descripcion" name="descripcion" value="">

                    <!-- mensajes de error con plantillas BLADE -->
                    @error('descripcion')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <div class="form-group col-md-6">
                    <label for="precio">Precio:</label>
                    <input type="number" class="form-control" id="precio" name="precio" value="">

                    <!-- mensajes de error con plantillas BLADE -->
                    @error('precio')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <div class="form-group col-md-6">
                    <label for="stock">Stock:</label>
                    <input type="number" class="form-control" id="stock" name="stock" value="">

                    <!-- mensajes de error con plantillas BLADE -->
                    @error('stock')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <div class="form-group col-md-6">
                    <label for="materiales">Materiales:</label>
                    <input type="text" class="form-control" id="materiales" name="materiales" value="">

                    <!-- mensajes de error con plantillas BLADE -->
                    @error('materiales')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <div class="form-group col-md-6">
                    <label for="dimensiones">Dimensiones:</label>
                    <input type="text" class="form-control" id="dimensiones" name="dimensiones" value="">

                    <!-- mensajes de error con plantillas BLADE -->
                    @error('dimensiones')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <div class="form-group col-md-6">
                    <label for="color_principal">Color:</label>
                    <input type="text" class="form-control" id="color_principal" name="color_principal" value="">

                    <!-- mensajes de error con plantillas BLADE -->
                    @error('color_principal')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <div class="form-group col-md-6">
                    <label class="form-label">Imágenes</label>
                    <input type="file" name="imagen_principal[]" class="form-control" multiple>

                    <!-- mensajes de error con plantillas BLADE -->
                    @error('imagen_principal')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                    @error('imagen_principal.*')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <div class="form-group col-md-6">
                    <label for="destacado">Destacado:</label>

                    <!-- hidden para enviar 0 si no se marca -->
                    <input type="hidden" name="destacado" value="0">

                    <!-- Checkbox (envía 1 si está marcado) -->
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="1" id="destacado" name="destacado" {{ old('destacado') ? 'checked' : '' }}>
                        <label class="form-check-label" for="destacado">Marcar como destacado</label>
                    </div>

                    @error('destacado')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-primary">Crear</button>
                <a href="{{ route('productos.index') }}" class="btn btn-secondary">Volver</a>
            </div>
        </form>
    </div>

    @include('layouts.footer')
</body>
