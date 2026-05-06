<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class ApiUsuarioService
{
    protected $baseUrl;

    public function __construct()
    {
        $this->baseUrl = env('API_USUARIOS_URL');
    }

    public function login($email, $password)
    {
        return Http::post("{$this->baseUrl}/login", [
            'email' => $email,
            'password' => $password,
        ]);
    }

    public function registrar($data)
    {
        return Http::post("{$this->baseUrl}/registrar", $data);
    }

    public function perfil()
    {
        $token = Session::get('api_token');
        
        return Http::withToken($token)->get("{$this->baseUrl}/perfil");
    }

    public function logout()
    {
        $token = Session::get('api_token');
        
        return Http::withToken($token)->post("{$this->baseUrl}/logout");
    }
}