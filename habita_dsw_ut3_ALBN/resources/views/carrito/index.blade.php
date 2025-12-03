@extends('layouts.app')

@section('content')
    <div class="container py-5">
        <h1 class="mb-4">Tu Carrito de Compra</h1>

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        @if($items->count() > 0)
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Producto</th>
                            <th>Precio Unitario</th>
                            <th>Cantidad</th>
                            <th>Subtotal</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($items as $item)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        @php
                                            $imagen = is_array($item->producto->imagen_principal)
                                                ? ($item->producto->imagen_principal[0] ?? null)
                                                : $item->producto->imagen_principal;
                                        @endphp
                                        @if($imagen)
                                            <img src="{{ asset('storage/' . $imagen) }}" alt="{{ $item->producto->nombre }}"
                                                class="img-thumbnail me-3" style="width: 60px; height: 60px; object-fit: cover;">
                                        @endif
                                        <div>
                                            <h5 class="mb-0">{{ $item->producto->nombre }}</h5>
                                            <small class="text-muted">Stock disponible: {{ $item->producto->stock }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ number_format($item->precio_unitario, 2) }} €</td>
                                <td style="width: 150px;">
                                    <form action="{{ route('carrito.update', $item->id) }}" method="POST" class="d-flex">
                                        @csrf
                                        @method('PUT')
                                        <input type="number" name="cantidad" value="{{ $item->cantidad }}" min="1"
                                            class="form-control me-2" style="width: 80px;">
                                        <button type="submit" class="btn btn-sm btn-primary" title="Actualizar">
                                            <i class="bi bi-arrow-clockwise"></i> Act
                                        </button>
                                    </form>
                                </td>
                                <td>{{ number_format($item->precio_unitario * $item->cantidad, 2) }} €</td>
                                <td>
                                    <form action="{{ route('carrito.remove', $item->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger"
                                            onclick="return confirm('¿Estás seguro?')">
                                            Eliminar
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-light">
                        <tr>
                            <td colspan="3" class="text-end fw-bold">Subtotal:</td>
                            <td colspan="2">{{ number_format($subtotal, 2) }} €</td>
                        </tr>
                        <tr>
                            <td colspan="3" class="text-end fw-bold">Impuestos (10%):</td>
                            <td colspan="2">{{ number_format($impuestos, 2) }} €</td>
                        </tr>
                        <tr>
                            <td colspan="3" class="text-end fw-bold fs-5">Total:</td>
                            <td colspan="2" class="fs-5 fw-bold">{{ number_format($total, 2) }} €</td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <div class="d-flex justify-content-between mt-4">
                <a href="{{ route('productos.galeria') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Seguir Comprando
                </a>

                <div class="d-flex gap-2">
                    <form action="{{ route('carrito.clear') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-warning" onclick="return confirm('¿Vaciar todo el carrito?')">
                            Vaciar Carrito
                        </button>
                    </form>

                    <form action="{{ route('carrito.comprar') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-success btn-lg">
                            Comprar Ahora <i class="bi bi-check-circle"></i>
                        </button>
                    </form>
                </div>
            </div>

        @else
            <div class="text-center py-5">
                <div class="mb-4">
                    <i class="bi bi-cart-x display-1 text-muted"></i>
                </div>
                <h3>Tu carrito está vacío</h3>
                <p class="text-muted">¡Añade algunos productos increíbles!</p>
                <a href="{{ route('productos.galeria') }}" class="btn btn-primary mt-3">
                    Ir a la Galería
                </a>
            </div>
        @endif
    </div>
@endsection