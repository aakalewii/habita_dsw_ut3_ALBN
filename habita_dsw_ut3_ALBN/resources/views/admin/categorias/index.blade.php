@include('layouts.header')

<body>
    @include('layouts.menu')

    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1>Gestión de Categorías</h1>
            <a href="{{ route('categorias.create') }}" class="btn btn-outline-success">Crear Categoría</a>
        </div>

        <table class="table table-striped table-hover">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Descripción</th>
                    <th colspan="2" class="text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($listaCategorias as $categoria)
                    <tr>
                        <td>{{ $categoria->id }}</td>
                        <td>
                            <a href="{{ route('categorias.show', $categoria->id) }}" class="text-decoration-none">
                                {{ $categoria->nombre }}
                            </a>
                        </td>
                        <td>
                            <a href="{{ route('categorias.show', $categoria->id) }}" class="text-decoration-none">
                                {{ $categoria->descripcion }}
                            </a>
                        </td>
                        <td class="col-1 text-center">
                            <a href="{{ route('categorias.edit', $categoria) }}" class="btn btn-primary btn-sm">
                                Editar
                            </a>
                        </td>
                        <td class="col-1 text-center">
                            <form action="{{ route('categorias.destroy', $categoria) }}" method="POST"
                                onsubmit="return confirm('¿Estás seguro de eliminar esta categoria?');">
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
