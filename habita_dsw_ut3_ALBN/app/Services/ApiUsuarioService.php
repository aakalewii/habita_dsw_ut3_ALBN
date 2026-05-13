<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class ApiUsuarioService
{
    protected $baseUrl;

    public function __construct()
    {
        $this->baseUrl = config('services.api_usuarios.url');
    }

    private function http()
    {
        return Http::acceptJson();
    }

    public function login($email, $password)
    {
        return $this->http()->post("{$this->baseUrl}/login", [
            'email' => $email,
            'password' => $password,
        ]);
    }

    public function registrar($data)
    {
        return $this->http()->post("{$this->baseUrl}/registrar", $data);
    }

    public function perfil()
    {
        return $this->http()->withToken(Session::get('api_token'))->get("{$this->baseUrl}/perfil");
    }

    public function logout()
    {
        return $this->http()->withToken(Session::get('api_token'))->post("{$this->baseUrl}/logout");
    }
}
