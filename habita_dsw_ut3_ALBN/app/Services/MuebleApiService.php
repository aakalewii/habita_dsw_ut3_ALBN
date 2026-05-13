<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Servicio para consumir la API REST de Muebles (puerto 8002).
 * Centraliza todas las llamadas HTTP hacia la API de Muebles,
 * siguiendo el patrón Service que se usa en el proyecto.
 */
class MuebleApiService
{
    protected string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = rtrim(env('API_MUEBLES_URL', 'http://127.0.0.1:8002/api/v1'), '/');
    }

    // ─── MUEBLES ────────────────────────────────────────────

    /**
     * Listar muebles con filtros, búsqueda, orden y paginación.
     * Devuelve un LengthAwarePaginator compatible con las vistas Blade.
     */
    public function listarMuebles(array $filtros = [], ?string $token = null): LengthAwarePaginator
    {
        $response = $this->get('/muebles', $filtros, $token);

        $items = collect($response['data'] ?? [])->map(fn($m) => $this->mapMueble($m));

        return new LengthAwarePaginator(
            $items,
            $response['meta']['total'] ?? 0,
            $response['meta']['per_page'] ?? 10,
            $response['meta']['current_page'] ?? 1,
            ['path' => request()->url(), 'query' => request()->query()]
        );
    }

    /**
     * Obtener un mueble por ID.
     */
    public function obtenerMueble(int $id, ?string $token = null): ?object
    {
        $response = $this->get("/muebles/{$id}", [], $token);
        return isset($response['data']) ? $this->mapMueble($response['data']) : null;
    }

    /**
     * Crear un mueble nuevo.
     */
    public function crearMueble(array $datos, string $token): array
    {
        return $this->post('/muebles', $datos, $token);
    }

    /**
     * Actualizar un mueble existente.
     */
    public function actualizarMueble(int $id, array $datos, string $token): array
    {
        return $this->put("/muebles/{$id}", $datos, $token);
    }

    /**
     * Eliminar un mueble.
     */
    public function eliminarMueble(int $id, string $token): array
    {
        return $this->deleteRequest("/muebles/{$id}", $token);
    }

    // ─── CATEGORÍAS ─────────────────────────────────────────

    /**
     * Listar todas las categorías.
     */
    public function listarCategorias(?string $token = null): array
    {
        $response = $this->get('/categorias', [], $token);
        $categorias = collect($response['data'] ?? [])->map(fn($c) => (object) $c);
        return $categorias->all();
    }

    /**
     * Obtener una categoría con sus muebles.
     */
    public function obtenerCategoria(int $id, ?string $token = null): ?object
    {
        $response = $this->get("/categorias/{$id}", [], $token);
        return isset($response['data']) ? (object) $response['data'] : null;
    }

    /**
     * Crear una categoría.
     */
    public function crearCategoria(array $datos, string $token): array
    {
        return $this->post('/categorias', $datos, $token);
    }

    /**
     * Actualizar una categoría.
     */
    public function actualizarCategoria(int $id, array $datos, string $token): array
    {
        return $this->put("/categorias/{$id}", $datos, $token);
    }

    /**
     * Eliminar una categoría.
     */
    public function eliminarCategoria(int $id, string $token): array
    {
        return $this->deleteRequest("/categorias/{$id}", $token);
    }

    // ─── MAPEO DE DATOS ─────────────────────────────────────

    /**
     * Mapea la respuesta JSON de la API al formato que esperan las vistas Blade
     * del proyecto original (UT3). Esto evita tener que modificar todas las vistas.
     */
    private function mapMueble(array $data): object
    {
        // Crear un array de categorías compatible con la vista (como colección)
        $categorias = [];
        if (!empty($data['categoria'])) {
            $categorias[] = (object) $data['categoria'];
        }

        return (object) [
            'id'               => $data['id'],
            'nombre'           => $data['nombre'],
            'descripcion'      => $data['descripcion'],
            'precio'           => $data['precio'],
            'stock'            => $data['stock'],
            // Mapeo de campos diferentes entre UT3 y API
            'materiales'       => $data['material'] ?? 'N/D',
            'dimensiones'      => 'N/D',
            'color_principal'  => $data['color'] ?? null,
            'imagen_principal' => $data['imagen_url'] ? [$data['imagen_url']] : [],
            'imagen_url'       => $data['imagen_url'] ?? null,
            'destacado'        => false,
            // Categoría: la API devuelve una sola, las vistas esperan una colección
            'categorias'       => collect($categorias),
            'categoria'        => !empty($data['categoria']) ? (object) $data['categoria'] : null,
            'categoria_id'     => $data['categoria']['id'] ?? null,
            'imagenes'         => collect($data['imagenes'] ?? []),
            'created_at'       => $data['created_at'] ?? null,
            'updated_at'       => $data['updated_at'] ?? null,
        ];
    }

    // ─── MÉTODOS HTTP BASE ──────────────────────────────────

    private function get(string $path, array $params = [], ?string $token = null): array
    {
        $request = Http::acceptJson();
        if ($token) $request = $request->withToken($token);

        $response = $request->get($this->baseUrl . $path, $params);
        return $response->json() ?? [];
    }

    private function post(string $path, array $data, ?string $token = null): array
    {
        $request = Http::acceptJson();
        if ($token) $request = $request->withToken($token);

        $response = $request->post($this->baseUrl . $path, $data);
        return ['status' => $response->status(), 'body' => $response->json() ?? []];
    }

    private function put(string $path, array $data, string $token): array
    {
        $response = Http::acceptJson()->withToken($token)
            ->put($this->baseUrl . $path, $data);
        return ['status' => $response->status(), 'body' => $response->json() ?? []];
    }

    private function deleteRequest(string $path, string $token): array
    {
        $response = Http::acceptJson()->withToken($token)
            ->delete($this->baseUrl . $path);
        return ['status' => $response->status(), 'body' => $response->json() ?? []];
    }
}
