@include('layouts.header')

<body>
    @include('layouts.menu')

    <div class="container py-5">
        <div class="text-center mb-4">
            <h1>Panel de administración</h1>
            <p class="text-muted">Accesos rápidos para gestionar el catálogo</p>
        </div>

        <div class="row g-4 justify-content-center">
            <div class="col-12 col-md-6 col-lg-5">
                <div class="card shadow-sm h-100 border-0">
                    <div class="card-body text-center py-5">
                        <div class="display-6 text-primary mb-3">
                            <i class="bi bi-folder-plus"></i>
                        </div>
                        <h4 class="mb-3">Crear categoría</h4>
                        <p class="text-muted mb-4">Crea y gestiona categorías para organizar los productos.</p>
                        <a href="{{ route('categorias.index') }}" class="btn btn-primary btn-lg">
                            Ir al listado de categorías
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-6 col-lg-5">
                <div class="card shadow-sm h-100 border-0">
                    <div class="card-body text-center py-5">
                        <div class="display-6 text-success mb-3">
                            <i class="bi bi-box-seam-fill"></i>
                        </div>
                        <h4 class="mb-3">Crear producto</h4>
                        <p class="text-muted mb-4">Añade y administra productos asignándolos a categorías.</p>
                        <a href="{{ route('productos.index') }}" class="btn btn-success btn-lg">
                            Ir al listado de productos
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('layouts.footer')
</body>
