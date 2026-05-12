<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class ApiMueblesService
{
    public string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = config('services.api_muebles.url');
    }

    // ── Rutas públicas (no requieren token) ──────────────────────────────

    public function listarMuebles(array $filtros = [])
    {
        return Http::get("{$this->baseUrl}/muebles", $filtros);
    }

    public function verMueble(int $id)
    {
        return Http::get("{$this->baseUrl}/muebles/{$id}");
    }

    public function listarCategorias(array $filtros = [])
    {
        return Http::get("{$this->baseUrl}/categorias", $filtros);
    }

    public function verCategoria(int $id)
    {
        return Http::get("{$this->baseUrl}/categorias/{$id}");
    }

    // ── Rutas protegidas (requieren token con abilities) ─────────────────

    public function crearMueble(array $datos)
    {
        return Http::withToken(Session::get('api_token'))
            ->post("{$this->baseUrl}/muebles", $datos);
    }

    public function actualizarMueble(int $id, array $datos)
    {
        return Http::withToken(Session::get('api_token'))
            ->put("{$this->baseUrl}/muebles/{$id}", $datos);
    }

    public function eliminarMueble(int $id)
    {
        return Http::withToken(Session::get('api_token'))
            ->delete("{$this->baseUrl}/muebles/{$id}");
    }

    public function crearCategoria(array $datos)
    {
        return Http::withToken(Session::get('api_token'))
            ->post("{$this->baseUrl}/categorias", $datos);
    }

    public function actualizarCategoria(int $id, array $datos)
    {
        return Http::withToken(Session::get('api_token'))
            ->put("{$this->baseUrl}/categorias/{$id}", $datos);
    }

    public function eliminarCategoria(int $id)
    {
        return Http::withToken(Session::get('api_token'))
            ->delete("{$this->baseUrl}/categorias/{$id}");
    }
}
