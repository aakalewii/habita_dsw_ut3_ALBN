@extends('layouts.app')

@section('content')
    <div class="container mt-5" style="max-width: 500px;">
        <div class="card shadow-sm p-4">
            <h3 class="text-center mb-4"><i class="bi bi-box-arrow-in-right"></i> Iniciar sesión</h3>

            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <form method="POST" action="{{ route('login.post') }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Correo electrónico</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email') }}" required
                        autofocus>
                    @error('email')
                        <span class="text-danger small">{{ $message }}</span>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Contraseña</label>
                    <input type="password" name="password" class="form-control" required>
                    @error('password')
                        <span class="text-danger small">{{ $message }}</span>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary w-100">Entrar</button>

                <div class="text-center mt-3">
                    <a href="{{ route('register') }}">¿No tienes cuenta? Regístrate</a>
                </div>
            </form>
        </div>
    </div>
@endsection
