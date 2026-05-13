<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Role;
use App\Services\UsuarioApiService;

class AuthController extends Controller
{
    protected UsuarioApiService $usuarioApi;

    public function __construct(UsuarioApiService $usuarioApi)
    {
        $this->usuarioApi = $usuarioApi;
    }

    /**
     * Formulario de login
     */
    public function showLogin()
    {
        return view('auth.login');
    }

    /**
     * Autenticación del usuario llamando a la API de Usuarios.
     * 1. Envía credenciales a la API de Usuarios (puerto 8001)
     * 2. Si es correcto, recibe un token + datos del usuario
     * 3. Crea/actualiza un usuario local para que Auth:: funcione
     * 4. Guarda el token en la sesión para usarlo con la API de Muebles
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        // Llamar a la API de Usuarios
        $result = $this->usuarioApi->login($credentials['email'], $credentials['password']);

        if ($result['status'] === 200 && !empty($result['body']['token'])) {
            $apiData = $result['body'];
            $token = $apiData['token'];
            $userData = $apiData['user'] ?? [];
            $abilities = $apiData['abilities'] ?? [];

            // Crear o actualizar usuario local para que Auth:: funcione
            $user = User::updateOrCreate(
                ['email' => $userData['email'] ?? $credentials['email']],
                [
                    'name'      => $userData['name'] ?? 'Usuario',
                    'apellidos' => $userData['apellidos'] ?? '',
                    'password'  => Hash::make($credentials['password']),
                    'role_id'   => $this->resolveRoleId($abilities),
                ]
            );

            // Login local (para que el middleware 'auth' funcione)
            Auth::login($user);
            $request->session()->regenerate();

            // Guardar el token de la API en la sesión
            Session::put('api_token', $token);
            Session::put('api_abilities', $abilities);
            Session::put('autorizacion_usuario', true);
            Session::put('usuario_id', $user->id);
            Session::put('email', $user->email);

            // Redirigir según el rol
            if ($user->role?->nombre === 'Administrador') {
                return redirect()->route('admin.dashboard')->with('success', 'Bienvenido, ' . $user->name);
            }

            return redirect()->route('productos.galeria')->with('success', 'Bienvenido, ' . $user->name);
        }

        // Login fallido
        return back()->withErrors([
            'email' => $result['body']['message'] ?? 'Las credenciales no son correctas.',
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
     * Registro de usuario llamando a la API de Usuarios.
     */
    public function register(Request $request)
    {
        $request->validate([
            'name'      => 'required|string|max:255',
            'apellidos' => 'required|string|max:255',
            'email'     => 'required|email|unique:users',
            'password'  => 'required|string|min:6|confirmed',
        ]);

        // Llamar a la API de Usuarios para registrar
        $result = $this->usuarioApi->register([
            'name'                  => $request->name,
            'apellidos'             => $request->apellidos,
            'email'                 => $request->email,
            'password'              => $request->password,
            'password_confirmation' => $request->password_confirmation,
        ]);

        if ($result['status'] === 201 || $result['status'] === 200) {
            $apiData = $result['body'];
            $token = $apiData['token'] ?? null;
            $userData = $apiData['user'] ?? [];

            // Crear usuario local
            $rolCliente = Role::where('nombre', 'Cliente')->first();
            $user = User::create([
                'name'      => $request->name,
                'apellidos' => $request->apellidos ?? '',
                'email'     => $request->email,
                'password'  => Hash::make($request->password),
                'role_id'   => $rolCliente?->id,
            ]);

            Auth::login($user);

            // Guardar token en sesión
            if ($token) {
                Session::put('api_token', $token);
            }
            Session::put('autorizacion_usuario', true);
            Session::put('usuario_id', $user->id);
            Session::put('email', $user->email);
            Session::regenerate();

            return redirect()->route('productos.galeria')->with('success', 'Registro completado correctamente.');
        }

        return back()->withErrors([
            'email' => $result['body']['message'] ?? 'Error al registrar el usuario.',
        ])->withInput();
    }

    /**
     * Cierre de sesión: llama a la API de Usuarios para invalidar el token.
     */
    public function logout(Request $request)
    {
        // Intentar cerrar sesión en la API de Usuarios
        $token = Session::get('api_token');
        if ($token) {
            $this->usuarioApi->logout($token);
        }

        // Cierre de sesión local
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }

    /**
     * Determina el role_id local basándose en las abilities del token.
     */
    private function resolveRoleId(array $abilities): ?int
    {
        if (in_array('admin.panel', $abilities)) {
            $role = Role::where('nombre', 'Administrador')->first();
        } elseif (in_array('muebles.crear', $abilities)) {
            $role = Role::where('nombre', 'Gestor')->orWhere('nombre', 'Administrador')->first();
        } else {
            $role = Role::where('nombre', 'Cliente')->first();
        }
        return $role?->id;
    }
}
