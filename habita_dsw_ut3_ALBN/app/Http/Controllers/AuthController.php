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

class AuthController extends Controller
{
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
        $credentials = $request->validate([
            'email' => ['required','email'],
            'password' => ['required','string']
        ]);

        // Crear una clave única para el rate limiter basada en el email y la IP
        $throttleKey = Str::lower($request->input('email')) . '|' . $request->ip();

        // Verificar si el usuario está bloqueado por demasiados intentos
        if (RateLimiter::tooManyAttempts($throttleKey, 3)) {
            $seconds = RateLimiter::availableIn($throttleKey);

            throw ValidationException::withMessages([
                'email' => 'Demasiados intentos de inicio de sesión. Por favor, intenta de nuevo en ' . ceil($seconds / 60) . ' minutos.',
            ]);
        }

        // Login propio de la capa Auth (Facade de Laravel).
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            // Limpiar los intentos fallidos si el login es exitoso
            RateLimiter::clear($throttleKey);

            // Guardar datos en sesión (REQUISITO 2.3)
            $user = Auth::user();

            // Establecer la sesión de autorización
            Session::put('autorizacion_usuario', true);
            Session::put('usuario_id', $user->id);
            Session::put('email', $user->email);

            // Verificar si el usuario tiene rol de Administrador y redirigir al panel de administración
            // En caso contrario, redirigir a la galería de productos (vista de cliente)
            if ($user->role?->nombre === 'Administrador') {
                return redirect()->route('admin.dashboard')->with('success', 'Bienvenido, ' . $user->name);
            }

            return redirect()->route('productos.galeria')->with('success', 'Bienvenido, ' . $user->name);
        }

        // Incrementar el contador de intentos fallidos
        RateLimiter::hit($throttleKey, 300); // 300 segundos = 5 minutos

        return back()->withErrors([
            'email' => 'Las credenciales no son correctas.',
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
     * Registro de usuario
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'apellidos' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);

        // Obtener el rol de "Cliente" (los usuarios registrados desde la web siempre son clientes)
        $rolCliente = Role::where('nombre', 'Cliente')->first();

        // Utilizando el propio modelo que viene con Laravel por defecto.
        $user = User::create([
            'name' => $request->name,
            'apellidos' => $request->apellidos,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => $rolCliente->id, // Asignar automáticamente el rol de Cliente
        ]);

        // Login de la capa Auth (Facade de Laravel).
        Auth::login($user);

        // Crear datos de sesión estructurados
        $datosSesion = [
            'usuario_id' => $user->id,
            'email' => $user->email,
            'name' => $user->name,
            'role' => $rolCliente->nombre,
            'sesionId' => Str::uuid()->toString(),
            'login_time' => now()->toDateTimeString()
        ];

        // Guardar usuario en sesión
        Session::put('usuario', json_encode($datosSesion));
        Session::put('autorizacion_usuario', true);
        Session::put('usuario_id', $user->id);
        Session::put('email', $user->email);
        Session::put('sesionId', $datosSesion['sesionId']);
        Session::regenerate();

        return redirect()->route('productos.galeria')->with('success', 'Registro completado correctamente.');
    }

    /**
     * Cierre de sesión
     */
    public function logout(Request $request)
    {
        // Cierre de sesión en la capa Auth.
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        // Retorna a la vista de login
        return redirect()->route('login');
    }
}
