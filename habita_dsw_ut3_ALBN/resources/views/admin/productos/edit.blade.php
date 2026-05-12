@include('layouts.header')

<body>
    @include('layouts.menu')
    <div class="container mt-4">
        <h1 class="mb-4">Editar Producto: {{ $producto['nombre'] }}</h1>

        <form action="{{ route('productos.update', $producto['id']) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="row">
                <div class="form-group col-md-6">
                    <label for="nombre">Nombre:</label>
                    <input type="text" class="form-control" id="nombre" name="nombre"
                        value="{{ old('nombre', $producto['nombre']) }}">
                    @error('nombre') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <div class="form-group col-md-6">
                    <label for="categoria_id">Categoría:</label>
                    <select class="form-control" id="categoria_id" name="categoria_id">
                        @foreach ($listaCategorias as $categoria)
                            <option value="{{ $categoria['id'] }}"
                                @selected(($producto['categoria_id'] ?? null) == $categoria['id'])>
                                {{ $categoria['nombre'] }}
                            </option>
                        @endforeach
                    </select>
                    @error('categoria_id') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <div class="form-group col-md-6">
                    <label for="descripcion">Descripción:</label>
                    <input type="text" class="form-control" id="descripcion" name="descripcion"
                        value="{{ old('descripcion', $producto['descripcion']) }}">
                    @error('descripcion') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <div class="form-group col-md-6">
                    <label for="precio">Precio:</label>
                    <input type="number" class="form-control" id="precio" name="precio" step="0.01"
                        value="{{ old('precio', $producto['precio']) }}">
                    @error('precio') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <div class="form-group col-md-6">
                    <label for="stock">Stock:</label>
                    <input type="number" class="form-control" id="stock" name="stock"
                        value="{{ old('stock', $producto['stock']) }}">
                    @error('stock') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <div class="form-group col-md-6">
                    <label for="material">Material:</label>
                    <input type="text" class="form-control" id="material" name="material"
                        value="{{ old('material', $producto['material'] ?? '') }}">
                    @error('material') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <div class="form-group col-md-6">
                    <label for="color">Color:</label>
                    <input type="text" class="form-control" id="color" name="color"
                        value="{{ old('color', $producto['color'] ?? '') }}">
                    @error('color') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <div class="form-group col-md-6">
                    <label class="form-label">Imagen</label>
                    <input type="file" name="imagen_principal" class="form-control">
                    @if(!empty($producto['imagen_url']))
                        <div class="mt-2">
                            <img src="{{ $producto['imagen_url'] }}" width="100" class="border p-1">
                        </div>
                    @endif
                    @error('imagen_principal') <small class="text-danger">{{ $message }}</small> @enderror
                </div>
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-primary">Actualizar</button>
                <a href="{{ route('productos.index') }}" class="btn btn-secondary">Volver</a>
            </div>
        </form>
    </div>

    @include('layouts.footer')
</body>
