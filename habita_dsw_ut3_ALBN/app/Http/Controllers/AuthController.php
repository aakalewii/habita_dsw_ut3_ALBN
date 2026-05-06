<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use App\Services\ApiUsuarioService;

class AuthController extends Controller
{
    protected $apiUsuarios;

    // Inyectamos el servicio para consumir la API
    public function __construct(ApiUsuarioService $apiUsuarios)
    {
        $this->apiUsuarios = $apiUsuarios;
    }

    // Login y registro de usuario utilizando la capa intermedia Auth de Laravel.
    /**
     * Formulario de login
     */
    public function showLogin()
    {
        return view('auth.login');
    }

    /**
     * Autenticación del usuario
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => ['required','email'],
            'password' => ['required','string']
        ]);

        $throttleKey = Str::lower($request->input('email')) . '|' . $request->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 3)) {
            $seconds = RateLimiter::availableIn($throttleKey);

            throw ValidationException::withMessages([
                'email' => 'Demasiados intentos de inicio de sesión. Por favor, intenta de nuevo en ' . ceil($seconds / 60) . ' minutos.',
            ]);
        }

        // 1. Llamar a la API de Usuarios para el login
        $response = $this->apiUsuarios->login($request->email, $request->password);

        if ($response->successful()) {
            $data = $response->json();
            
            // 2. Guardar el token y los permisos en sesión (Requisito Obligatorio)
            Session::put('api_token', $data['access_token']);
            Session::put('user_rol', $data['rol']);
            Session::put('user_abilities', $data['abilities']);

            // 3. Obtener el perfil del usuario usando el nuevo token
            $perfilResponse = $this->apiUsuarios->perfil();
            
            if ($perfilResponse->successful()) {
                $user = $perfilResponse->json();
                
                // 4. Recrear las variables de sesión que utilizaba tu monolito antiguo
                Session::put('autorizacion_usuario', true);
                Session::put('usuario_id', $user['id']);
                Session::put('email', $user['email']);
                Session::put('name', $user['name']);
                Session::put('sesionId', Str::uuid()->toString());

                RateLimiter::clear($throttleKey);
                $request->session()->regenerate();

                // 5. Redirigir según el rol devuelto por la API
                if ($data['rol'] === 'Administrador') {
                    return redirect()->route('admin.dashboard')->with('success', 'Bienvenido, ' . $user['name']);
                }

                return redirect()->route('productos.galeria')->with('success', 'Bienvenido, ' . $user['name']);
            }
        }

        RateLimiter::hit($throttleKey, 300);

        return back()->withErrors([
            'email' => 'Las credenciales no son correctas o hubo un error en la API.',
        ])->onlyInput('email');
    }

    /**
     * Formulario de registro
     */
    public function showRegister()
    {
        return view('auth.register');
    }

    /**
     * Registro de usuario consumiendo la API
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'password' => 'required|string|min:6|confirmed',
        ]);

        // 1. Llamar a la API para registrar al usuario
        $response = $this->apiUsuarios->registrar([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
        ]);

        if ($response->successful()) {
            
            // 2. Si el registro es exitoso, hacemos login automáticamente mediante la API
            $loginResponse = $this->apiUsuarios->login($request->email, $request->password);

            if ($loginResponse->successful()) {
                $loginData = $loginResponse->json();
                
                Session::put('api_token', $loginData['access_token']);
                Session::put('user_rol', $loginData['rol']);
                Session::put('user_abilities', $loginData['abilities']);

                // Obtener datos del perfil
                $perfilResponse = $this->apiUsuarios->perfil();
                $user = $perfilResponse->json();

                // Establecer sesiones
                Session::put('autorizacion_usuario', true);
                Session::put('usuario_id', $user['id']);
                Session::put('email', $user['email']);
                Session::put('name', $user['name']);
                Session::put('sesionId', Str::uuid()->toString());
                
                $request->session()->regenerate();

                return redirect()->route('productos.galeria')->with('success', 'Registro completado correctamente.');
            }
        }

        // Si la API devuelve un error (ej. email duplicado)
        return back()->withErrors([
            'email' => 'No se pudo completar el registro. Es posible que el correo ya esté en uso.',
        ])->withInput();
    }

    /**
     * Cierre de sesión
     */
    public function logout(Request $request)
    {
        // Invalidar el token en la API externa
        if (session()->has('api_token')) {
            $this->apiUsuarios->logout();
        }

        // Limpiamos la sesión local
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Retorna a la vista de login
        return redirect()->route('login')->with('success', 'Sesión cerrada correctamente.');
    }

    /**
     * Mostrar el perfil del usuario autenticado
     */
    public function perfil()
    {
        // Hacemos la petición a la API de Usuarios mediante nuestro servicio
        $response = $this->apiUsuarios->perfil();

        // Comprobamos si la API nos devuelve un OK
        if ($response->successful()) {
            // Extraemos los datos del JSON
            $usuario = $response->json();
            
            // Retornamos la vista inyectando los datos del usuario.
            return view('User.index', compact('usuario')); 
        }

        // Si la API falla (ej. token inválido), destruimos la sesión local y mandamos al login
        session()->invalidate();
        session()->regenerateToken();

        return redirect()->route('login')->withErrors([
            'email' => 'Tu sesión ha caducado o es inválida. Por favor, vuelve a iniciar sesión.'
        ]);
    }

}
