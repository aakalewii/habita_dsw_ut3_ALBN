<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

/**
 * Servicio para consumir la API REST de Usuarios (puerto 8001).
 * Gestiona login, registro, perfil y logout llamando a la API externa.
 */
class UsuarioApiService
{
    protected string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = rtrim(env('API_USUARIOS_URL', 'http://127.0.0.1:8001/api'), '/');
    }

    /**
     * Iniciar sesión: envía credenciales a la API de Usuarios.
     * Devuelve token + datos del usuario + abilities si el login es correcto.
     */
    public function login(string $email, string $password): array
    {
        $response = Http::acceptJson()->post($this->baseUrl . '/login', [
            'email'    => $email,
            'password' => $password,
        ]);

        return [
            'status' => $response->status(),
            'body'   => $response->json() ?? [],
        ];
    }

    /**
     * Registrar un usuario nuevo en la API de Usuarios.
     */
    public function register(array $datos): array
    {
        $response = Http::acceptJson()->post($this->baseUrl . '/registrar', $datos);

        return [
            'status' => $response->status(),
            'body'   => $response->json() ?? [],
        ];
    }

    /**
     * Obtener el perfil del usuario autenticado.
     */
    public function perfil(string $token): array
    {
        $response = Http::acceptJson()->withToken($token)
            ->get($this->baseUrl . '/perfil');

        return [
            'status' => $response->status(),
            'body'   => $response->json() ?? [],
        ];
    }

    /**
     * Cerrar sesión: elimina el token en la API de Usuarios.
     */
    public function logout(string $token): array
    {
        $response = Http::acceptJson()->withToken($token)
            ->post($this->baseUrl . '/logout');

        return [
            'status' => $response->status(),
            'body'   => $response->json() ?? [],
        ];
    }
}
